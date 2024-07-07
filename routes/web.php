<?php

use Illuminate\Support\Facades\Route;

Route::get('/' , \App\Livewire\HomePage::class);
Route::get('/categories' , \App\Livewire\CategoriesPage::class);
Route::get('/products' , \App\Livewire\ProductsPage::class);
Route::get('/products/{slug}' , \App\Livewire\ProductDetailPage::class);
Route::get('/cart' , \App\Livewire\CartPage::class);
Route::get('/checkout' , \App\Livewire\CheckoutPage::class);
Route::get('/my-orders' , \App\Livewire\MyOrdersPage::class);
Route::get('/my-orders/{order}' , \App\Livewire\MyOrderDetailPage::class);

Route::get('/login' , \App\Livewire\Auth\Login::class);
Route::get('/register' , \App\Livewire\Auth\Register::class);
Route::get('/forget-password' , \App\Livewire\Auth\ForgetPassword::class);
Route::get('/reset-password' , \App\Livewire\Auth\ResetPassword::class);

Route::get("/success" , \App\Livewire\SuccessPage::class);
Route::get("/cancel" , \App\Livewire\CancelPage::class);

