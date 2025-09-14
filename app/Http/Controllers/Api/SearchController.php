<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $locale = $request->query('locale', app()->getLocale());

        if (mb_strlen($q) < 2) {
            return response()->json([
                'query'   => $q,
                'results' => [
                    'products'   => [],
                    'categories' => [],
                    // 'kitchen' => [],
                    // 'posts'   => [],
                ],
                'counts'  => ['total' => 0],
            ]);
        }

        // Helper to safely pick localized columns if you keep *_ar / *_en fields
        $pick = function ($model, array $keys) use ($locale) {
            foreach ($keys as $base) {
                $locKey = "{$base}_" . substr($locale, 0, 2);
                if (isset($model->{$locKey}) && $model->{$locKey}) return $model->{$locKey};
                if (isset($model->{$base}) && $model->{$base})     return $model->{$base};
            }
            return null;
        };

        // Products
        $products = Product::query()
            ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('description_en', 'like', "%{$q}%")
                    ->orWhere('name_ar', 'like', "%{$q}%")
                    ->orWhere('description_ar', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function ($p) use ($pick) {
                $title = $pick($p, ['name', 'title']);
                $desc  = $pick($p, ['description', 'short_description']);
                return [
                    'id'    => $p->id,
                    'type'  => 'product',
                    'title' => $title,
                    'desc'  => Str::limit(strip_tags((string) $desc), 140),
                    'url'   => '/product/' . ($p->slug ?? $p->id),
                    'image' => $p->image_url ?? $p->image ?? null,
                    'meta'  => ['price' => $p->price ?? null],
                ];
            });

        // Categories
        $categories = Category::query()
            ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('name_ar', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(function ($c) use ($pick) {
                $title = $pick($c, ['name', 'title']);
                return [
                    'id'    => $c->id,
                    'type'  => 'category',
                    'title' => $title,
                    'desc'  => null,
                    'url'   => '/category/' . ($c->slug ?? $c->id),
                    'image' => $c->image ?? null,
                    'meta'  => null,
                ];
            });

        // Kitchen (optional)
        // $kitchen = Kitchen::query()
        //     ->where(function ($qq) use ($q) {
        //         $qq->where('title', 'like', "%{$q}%")
        //            ->orWhere('description', 'like', "%{$q}%")
        //            ->orWhere('title_en', 'like', "%{$q}%")
        //            ->orWhere('description_en', 'like', "%{$q}%")
        //            ->orWhere('title_ar', 'like', "%{$q}%")
        //            ->orWhere('description_ar', 'like', "%{$q}%");
        //     })
        //     ->orderByDesc('id')
        //     ->limit(6)
        //     ->get()
        //     ->map(function ($k) use ($pick) {
        //         $title = $pick($k, ['title', 'name']);
        //         $desc  = $pick($k, ['description']);
        //         return [
        //             'id'    => $k->id,
        //             'type'  => 'kitchen',
        //             'title' => $title,
        //             'desc'  => \Illuminate\Support\Str::limit(strip_tags((string) $desc), 140),
        //             'url'   => '/our-kitchen',
        //             'image' => $k->image ?? null,
        //             'meta'  => ['price' => $k->price ?? null],
        //         ];
        //     });

        $results = [
            'products'   => $products,
            'categories' => $categories,
            // 'kitchen' => $kitchen,
        ];

        $total = collect($results)->flatMap(fn($g) => $g)->count();

        return response()->json([
            'query'   => $q,
            'results' => $results,
            'counts'  => [
                'products'   => $products->count(),
                'categories' => $categories->count(),
                // 'kitchen'  => $kitchen->count(),
                'total'      => $total,
            ],
        ]);
    }
}
