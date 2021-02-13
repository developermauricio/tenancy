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
    
});


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
