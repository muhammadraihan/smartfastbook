<?php

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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // check if user is auth then redirect to dashboard page
    if (Auth::check()) {
        return redirect()->route('backoffice.dashboard');
    }
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'backoffice', 'middleware' => ['auth']], function () {
    // backoffice
    Route::get('/', 'DashboardController@index');
    Route::get('dashboard', 'DashboardController@dashboard')->name('backoffice.dashboard');
    // logs
    Route::get('logs', 'ActivityController@index')->name('logs');
    // profile
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::patch('profile/{user}/update', 'UserController@ProfileUpdate')->name('profile.update');
    Route::patch('profile/{user}/password', 'UserController@ChangePassword')->name('profile.password');
    // resource
    Route::resource('menus', 'MenuController');
    Route::resource('users', 'UserController');
    Route::resource('permissions', 'PermissionController');
    Route::resource('roles', 'RoleController');
    Route::resource('customer', 'CustomerController');
    Route::resource('bank', 'BankController');
    Route::resource('kas', 'KasController');
    Route::resource('transaksi', 'TransaksiController');
    Route::resource('tariktunai', 'TarikTunaiController');
    Route::resource('payment', 'PaymentController');
    Route::resource('ewallet', 'EWalletController');
    Route::resource('saldo', 'SaldoController');
    Route::resource('saldoKeluar', 'SaldoKeluarController');
    Route::resource('perusahaan', 'PerusahaanController');
    Route::resource('rekening', 'RekeningController');
    Route::get('getPayment', 'EWalletController@GetPayment')->name('getPayment');
    Route::get('filter', 'SaldoController@filter')->name('get.filter');
    Route::POST('search', 'KasController@cekrekening')->name('get.cekrekening');
    Route::get('invoice', 'InvoiceController@print')->name('print');
});
