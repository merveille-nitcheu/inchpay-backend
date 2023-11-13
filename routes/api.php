<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SendMail;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\TransactionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::group(['middleware'=>['auth:sanctum']],function() {
//     Route::get("users", [UserController::class,'index']);
// });



// routes pour l'utilisateur::

// Route::group(['middleware'=>'cors'],function() {

// Route::get("teste", [ApplicationController::class,'tester']);
Route::post("logout", [UserController::class,'logout']);
Route::post("login", [UserController::class,'login']);
Route::get("users", [UserController::class,'index']);
Route::post("storeuser", [UserController::class,'store']);
Route::post("storeadmin", [UserController::class,'storeadmin']);
Route::delete("user/{slug}", [UserController::class,'destroy']);
Route::get("user/{slug}", [UserController::class,'show']);
Route::post("user/{slug}", [UserController::class,'update']);
Route::post("sendmail", [SendMail::class,'store']);


//routes pour l'application

Route::get("apps", [ApplicationController::class,'index']);
Route::get("app/widgets", [ApplicationController::class,'widgetlist']);
Route::post("app/token/{slug}", [ApplicationController::class,'tokengenerate']);

Route::post("app/{slug_user}", [ApplicationController::class,'store']);
Route::post("app/{slug}/update", [ApplicationController::class,'update']);
Route::get("app/{slug}", [ApplicationController::class,'show']);
Route::get("appuser/{slug}", [ApplicationController::class,'appuser']);
Route::delete("app/{slug}", [ApplicationController::class,'destroy']);
Route::get("widget/{slug}",[ApplicationController::class,'show']);
Route::get("widgetuser/{slug}",[ApplicationController::class,'showwidgetuser']);


Route::get("inchpay/widget/{generatelink}",[ApplicationController::class,'veriftoken']);


// routes pour les transactions


// go live

Route::post("requesttopay/{slug}",[TransactionsController::class,'requesttopay']);
Route::post("requesttowithdrawal/{slug}",[TransactionsController::class,'requesttowithdrawal']);
Route::get("responseWithDrawal",[TransactionsController::class,'responseWithDrawal']);
Route::get("responseDeposit",[TransactionsController::class,'responseDeposit']);
Route::get("transactions",[TransactionsController::class,'index']);
Route::get("transaction/{slug}",[TransactionsController::class,'show']);
Route::get("transactionuser/{slug}",[TransactionsController::class,'showtransactionuser']);



// });


// routes pour le profil

// routes pour le compte





