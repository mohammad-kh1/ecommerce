<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Product")]
class ProductDetailPage extends Component
{
    use LivewireAlert;
    public $slug;
    public $quantity = 1;

    public function increaseQTY()
    {
        $this->quantity+=1;
    }

    public function decreaseQTY()
    {
        if($this->quantity > 1){
        return $this->quantity-=1;
        }
    }

    public function addToCart($product_id){
        $total_count = CartManagement::addItemToCartWithQuantity($product_id , $this->quantity);
        $this->dispatch("update-cart-count",$total_count)->to(Navbar::class);
        $this->alert('success', 'Prodcut Added To Cart', [
            'position' => 'top-start',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
    public function mount($slug)
    {
        $this->slug = $slug;
    }
    public function render()
    {
        $product = Product::where("slug" , $this->slug)->firstOrFail();
        return view('livewire.product-detail-page')->with([
            "product" => $product
        ]);
    }
}
