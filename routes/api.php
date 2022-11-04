<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\BuyCookiesController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//routes/api.php
Route::post('register',[PassportAuthController::class,'registerUser']);
Route::post('login',[PassportAuthController::class,'loginUser']);
//add this middleware to ensure that every request is authenticated
Route::middleware('auth:api')->group(function(){
    Route::get('user', [PassportAuthController::class,'authenticatedUserDetails']);
    Route::post('addMoney', [PassportAuthController::class,'walletamount']);
    Route::post('buyCookie', [BuyCookiesController::class,'buyCookies']);
});
Route::fallback(function(){
    return response()->json([
        'message' => 'Invalid Request'], 404);
});