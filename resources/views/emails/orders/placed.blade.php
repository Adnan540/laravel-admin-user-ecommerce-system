@component('mail::message')
# Thank you for your order!

Hello {{ $order->user?->name ?? 'Customer' }},

We have received your order **#{{ $order->id }}**.

@component('mail::panel')
**Total:** {{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'USD' }} <br>
**Status:** {{ ucfirst($order->status) }} <br>
**Date:** {{ $order->created_at->format('Y-m-d H:i') }}
@endcomponent

@component('mail::table')
| Product | Quantity | Price |
|:--------|:--------:|------:|
@foreach ($order->items as $item)
| {{ $item->product?->name ?? '-' }} | {{ $item->quantity }} | {{ number_format($item->price_at_purchase, 2) }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => url('/orders/'.$order->id)])
View Your Order
@endcomponent

Thanks for shopping with us!
**Sadeek Sweets**
@endcomponent