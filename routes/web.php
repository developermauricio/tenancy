<?php

use Illuminate\Support\Facades\Route;

/*=============================================
RUTAS DE STRIPE, PERMITE REALIZAR PETICIONES
=============================================*/
Route::post(
    'stripe/webhook',
    'StripeWebHookController@handleWebhook'
);


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * RUTAS QUE NECESITAN AUTENTICACIÓN Y PERTENECEN AL ADMINISTRADOR DEL SISTEMA (ROOT)
 */
Route::group(["middleware" => ["auth", "root"]], function () {
    Route::get("credit-card", 'BillingController@creditCardForm')
        ->name("billing.credit_card_form");
    Route::post("credit-card", 'BillingController@processCreditCardForm')
        ->name("billing.process_credit_card");

    Route::group(["middleware" => "admin"], function () {
        Route::get("plans/create", "PlanController@create")->name("plans.create");
        Route::post("plans/store", "PlanController@store")->name("plans.store");

        Route::get("tenants", "TenantController@index")->name("tenants.index");
        Route::get("tenants/{id}", "TenantController@destroy")->name("tenants.destroy");
    });

    Route::get("plans", "PlanController@index")->name("plans.index");
    Route::post("plans/purchase", "PlanController@purchase")->name("plans.purchase");
    Route::post("plans/cancel", "PlanController@cancelSubscription")->name("plans.cancel");
    Route::post("plans/resume", "PlanController@resumeSubscription")->name("plans.resume");
});


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
