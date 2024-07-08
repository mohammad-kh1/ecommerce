<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrdersPage extends Component
{

    use WithPagination;
    public function render()
    {
        $orders = Order::where("user_id" , auth()->user()->id)->latest()->paginate(4);
        return view('livewire.my-orders-page')->with([
            "orders" => $orders
        ]);
    }
}
