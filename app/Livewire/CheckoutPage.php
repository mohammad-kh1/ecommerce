<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutPage extends Component
{
    #[Validate("required")]
    public string $first_name;
    #[Validate("required")]
    public string $last_name;
    #[Validate("required")]
    public string $street_address;
    #[Validate("required")]
    public string $phone;
    #[Validate("required")]
    public string $city;
    #[Validate("required")]
    public string $state;
    #[Validate("required")]
    public string $zip_code;
    #[Validate("required")]
    public string $payment_method;

    public function save()
    {
        $this->validate();

        $cart_items = CartManagement::getCartItemsFromCookie();
        $line_items = [];
        foreach ($cart_items as $item){
            $line_items[]=[
                "price_data" => [
                    "currency" => "usd",
                    "unit_amount" => $item["unit_amount"] * 100,
                    "product_data"=>[
                        "name" => $item["name"],

                    ]
                ],
                "quantity" => $item["quantity"]
            ];
        }

        $order =  new Order();
        $order->user_id = auth()->id();
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = "pending";
        $order->status = "new";
        $order->currency = "usd";
        $order->shipping_amount = 0;
        $order->shipping_method = "none";
        $order->notes = "Order Placed by " . auth()->user()->name;

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;

        $redirect_url = "";
        if($this->payment_method == "stripe"){
            Stripe::setApiKey(env("STRIPE_SECRET"));
            $sessionCheckout = Session::create([
                "payment_method_types" => ["card"],
                "customer_email" => auth()->user()->email,
                "mode" => "payment",
                "line_items" => $line_items,
                "success_url" => route('success')."?session_id={CHECKOUT_SESSION_ID}",
                "cancel_url" => route('cancel'),

            ]);
            $redirect_url = $sessionCheckout->url;
        }else{
            $redirect_url = route('success');
        }

        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_items);
        CartManagement::clearCartItemsFormCookie();
        Mail::to(request()->user())->send(new OrderPlaced($order));
        return redirect($redirect_url);
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);
        return view('livewire.checkout-page')->with([
            "cart_items" => $cart_items,
            "grand_total" => $grand_total
        ]);
    }
}
