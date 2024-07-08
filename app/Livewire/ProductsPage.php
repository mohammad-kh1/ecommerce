<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title("Products")]
class ProductsPage extends Component
{
    use WithPagination;

    #[Url()]
    public $selected_categories=[];
    #[Url()]
    public $selected_brands=[];

    #[Url()]
    public $featured;

    #[Url]
    public $onsale;

    #[Url()]
    public $price_range = 300000;

    public $sort = "latest";

    public function render()
    {
        $product = Product::query()->where('is_active' ,1);

        if(!empty($this->selected_categories)){
            $product->whereIn("category_id" , $this->selected_categories);
        }
        if(!empty($this->selected_brands)){
            $product->whereIn("brand_id" , $this->selected_brands);
        }

        if($this->featured){
            $product->where("is_featured" ,1);
        }

        if($this->onsale){
            $product->where("os_sale" ,1);
        }

        if($this->price_range){
            $product->whereBetween("price" , [0 , $this->price_range]);
        }

        if($this->sort =="latest"){
            $product->latest();
        }

        if($this->sort == "price"){
            $product->orderBy("price");
        }

        return view('livewire.products-page')->with([
            "products" => $product->paginate(9),
            "brands" => Brand::where("is_active" ,1)->get(["id" , "name" ,"slug"]),
            "categories" => Category::where("is_active",1)->get(["id" , "name" ,"slug"])
        ]);
    }
}
