<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;

class MyOrderDetailPage extends Component
{

    public $order_id ;

    public function mount($order_id)
    {
        $this->order_id = $order_id;
    }
    public function render()
    {
        $orderItem = OrderItem::with("product")->where("order_id", $this->order_id)->get();
        $address = Address::where("order_id" , $this->order_id)->first();
        $order = Order::where("id" , $this->order_id)->first();
        return view('livewire.my-order-detail-page')->with([
            "orderItem" => $orderItem,
            "address" =>$address,
            "order" => $order
        ]);
    }
}
