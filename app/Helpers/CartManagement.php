<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    //add item to cart
    static public function addItemToCart($product_it){
        $cart_items = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key=>$item){
            if($item["product_id"] == $product_it){
                $existing_item = $key;
                break;
            }
        }

        if($existing_item !==null){
            $cart_items[$existing_item]["quantity"]++;
            $cart_items[$existing_item]["total_amount"]=$cart_items[$existing_item]["quantity"] * $cart_items[$existing_item]["unit_amount"];
        }else{
            $product = Product::where("id" , $product_it)->first(["id" , 'name' , 'price' , 'images']);
            if($product){
                $cart_items[] =[
                    "product_id" => $product_it,
                    "name" => $product->name,
                    "image" => $product->images[0],
                    "quantity" =>1,
                    "unit_amount" => $product->price,
                    "total_amount" => $product->price
                ];
            }
        }

        self::addCartItemToCookie($cart_items);
        return count($cart_items);

    }

    //remote item from cart
    static public function removeCartItem($product_id){
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key=>$item){
            if($item["product_id"] == $product_id){
                unset($cart_items[$key]);
            }
        }

        self::addCartItemToCookie($cart_items);

        return $cart_items;
    }


    // add cart items to cookie

    static public function addCartItemToCookie($cart_items){
        Cookie::queue("cart_items" , json_decode($cart_items) , 60*24*30);
    }


    //clean cart items from cookie
    static public function clearCartItemsFormCookie(){
        Cookie::queue(Cookie::forget("cart_items"));
    }

    //get add cart items from cookie
    static public function getCartItemsFromCookie(){
        $cart_items = json_decode(Cookie::get("cart_items") , true);
        if(!$cart_items){
            $cart_items = [];
        }
        return $cart_items;
    }


    //increment item quantity
    static public function incrementQuantityToCartItem($product_id){
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key=>$item){
            if($item["product_id"] == $product_id){
                $cart_items[$key]["quantity"]++;
                $cart_items[$key]["total_amount"] = $cart_items[$key]["quantity"] * $cart_items[$key]["unit_amount"];
            }
        }

        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }


    // decrement item quantity

    static public function decrementQuantityToCartItem($product_id){
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key=>$item) {
            if($item["product_id"] == $product_id){
                if($cart_items[$key]["quantity"] > 0){
                    $cart_items[$key]["quantity"]--;
                    $cart_items[$key]["total_amount"] = $cart_items[$key]["quantity"] * $cart_items[$key]["unit_amount"];
                }
            }
        }
        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }

    // calculate grand total
    static public function calculateGrandTotal($items){
        return array_sum(array_column($items , "total_amount"));
    }
}
