<?php

use Illuminate\Support\Facades\Route;

Route::get('/' , \App\Livewire\HomePage::class)->name("home");
Route::get('/categories' , \App\Livewire\CategoriesPage::class);
Route::get('/products' , \App\Livewire\ProductsPage::class);
Route::get('/products/{slug}' , \App\Livewire\ProductDetailPage::class);
Route::get('/cart' , \App\Livewire\CartPage::class);


Route::middleware("guest")->group(function(){
    Route::get('/login' , \App\Livewire\Auth\Login::class)->name("login");
    Route::get('/register' , \App\Livewire\Auth\Register::class);
    Route::get('/forgot-password' , \App\Livewire\Auth\ForgetPassword::class)->name("password.request");
    Route::get('/reset/{token}' , \App\Livewire\Auth\ResetPassword::class)->name("password.reset");
});




Route::middleware("auth")->group(function(){
    Route::get('/checkout' , \App\Livewire\CheckoutPage::class);
    Route::get('/my-orders' , \App\Livewire\MyOrdersPage::class);
    Route::get('/my-orders/{order}' , \App\Livewire\MyOrderDetailPage::class);
    Route::get("/success" , \App\Livewire\SuccessPage::class);
    Route::get("/cancel" , \App\Livewire\CancelPage::class);
    Route::get("/logout",function(){
        auth()->logout();
        return to_route("home");
    });

});
