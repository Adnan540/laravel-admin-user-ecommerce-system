<?php

namespace App\Http\Controllers\Api\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'message' => 'Profile fetched',
            'data'    => $request->user()->only([
                'id',
                'name',
                'email',
                'phone',
                'company',
                'country',
                'location',
                'created_at'
            ])
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'company'  => ['nullable', 'string', 'max:120'],
            'country'  => ['nullable', 'string', 'size:2'],
            'location' => ['nullable', 'string', 'max:190'],
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated',
            'data'    => $user->only(['id', 'name', 'email', 'phone', 'company', 'country', 'location'])
        ]);
    }
}
