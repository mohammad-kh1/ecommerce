<x-mail::message>
# Order Placed Sucessfuly!

Thank you your order
Your Order number is {{$order->id}}

<x-mail::button :url="$url">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
