<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

$controller_path = 'App\Http\Controllers';

// Main Page Route
Route::get('/', 'App\Http\Controllers\dashboard\Analytics@index')->name('dashboard-analytics')->middleware('auth');
Route::get('/stats', 'App\Http\Controllers\dashboard\Analytics@stats')->name('stats')->middleware('auth');
//Route::get('/privacy_policy', 'App\Http\Controllers\DocumentationController@public');

Route::group(['middleware' => ['auth']], function () {
  Route::get('/version', 'App\Http\Controllers\VersionController@index')->name('version');
  Route::get('/stats', 'App\Http\Controllers\dashboard\Analytics@stats')->name('stats');
  Route::get('/category/index', 'App\Http\Controllers\CategoryController@index')->name('category-index');
  Route::get('/subcategory/index', 'App\Http\Controllers\SubcategoryController@index')->name('subcategory-index');
//  Route::get('/family/index', 'App\Http\Controllers\FamilyController@index')->name('family-index');
//  Route::get('/offer/index', 'App\Http\Controllers\OfferController@index')->name('offer-index');
  Route::get('/product/index', 'App\Http\Controllers\ProductController@index')->name('product-index');
//  Route::get('/section/index', 'App\Http\Controllers\SectionController@index')->name('section-index');
  Route::get('/order/index', 'App\Http\Controllers\OrderController@index')->name('order-index');
  Route::get('/order/inbox', 'App\Http\Controllers\OrderController@inbox')->name('order-inbox');
  Route::get('/order/outbox', 'App\Http\Controllers\OrderController@outbox')->name('order-outbox');
  Route::get('/order/{id}/info', 'App\Http\Controllers\OrderController@info')->name('order-info');
  Route::get('/order/{id}/items', 'App\Http\Controllers\ItemController@index')->name('order-items');
//  Route::get('/driver/index', 'App\Http\Controllers\DriverController@index')->name('driver-index');
  Route::get('/user/index', 'App\Http\Controllers\UserController@index')->name('user-index');
  Route::get('/user/browse', 'App\Http\Controllers\UserController@browse')->name('user-browse');
  Route::get('/notice/index', 'App\Http\Controllers\NoticeController@index')->name('notice-index');
  Route::get('/ad/index', 'App\Http\Controllers\AdController@index')->name('ad-index');
  Route::get('/stock/index', 'App\Http\Controllers\StockController@index')->name('stock-index');
  Route::get('/user/{id}/stocks', 'App\Http\Controllers\UserController@stocks')->name('user-stocks');
  Route::get('/cart/index', 'App\Http\Controllers\CartController@index')->name('cart-index');
  Route::get('/category/list', 'App\Http\Controllers\DatatablesController@categories')->name('category-list');
  Route::post('/subcategory/list', 'App\Http\Controllers\DatatablesController@subcategories')->name('subcategory-list');
  Route::get('/family/list', 'App\Http\Controllers\DatatablesController@families')->name('family-list');
  Route::get('/offer/list', 'App\Http\Controllers\DatatablesController@offers')->name('offer-list');
  Route::post('/product/list', 'App\Http\Controllers\DatatablesController@products')->name('product-list');
  Route::get('/section/list', 'App\Http\Controllers\DatatablesController@sections')->name('section-list');
  Route::post('/order/list', 'App\Http\Controllers\DatatablesController@orders')->name('order-list');
  Route::post('/item/list', 'App\Http\Controllers\DatatablesController@items')->name('item-list');
  Route::get('/driver/list', 'App\Http\Controllers\DatatablesController@drivers')->name('driver-list');
  Route::post('/user/list', 'App\Http\Controllers\DatatablesController@users')->name('user-list');
  Route::get('/notice/list', 'App\Http\Controllers\DatatablesController@notices')->name('notice-list');
  Route::get('/ad/list', 'App\Http\Controllers\DatatablesController@ads')->name('ad-list');
  Route::post('/stock/list', 'App\Http\Controllers\DatatablesController@stocks')->name('stock-list');

});

Route::group(['middleware' => ['auth']], function () {
  Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
  Route::post('/user/update', 'App\Http\Controllers\UserController@update');
  Route::post('/user/change_password', 'App\Http\Controllers\UserController@change_password');
  Route::post('/user/reset_password', 'App\Http\Controllers\UserController@reset_password');

  Route::post('/category/create', 'App\Http\Controllers\CategoryController@create');
  Route::post('/category/update', 'App\Http\Controllers\CategoryController@update');
  Route::post('/category/delete', 'App\Http\Controllers\CategoryController@delete');
  Route::post('/category/restore', 'App\Http\Controllers\CategoryController@restore');
  Route::post('/category/get', 'App\Http\Controllers\CategoryController@get');

  Route::post('/subcategory/create', 'App\Http\Controllers\SubcategoryController@create');
  Route::post('/subcategory/update', 'App\Http\Controllers\SubcategoryController@update');
  Route::post('/subcategory/delete', 'App\Http\Controllers\SubcategoryController@delete');
  Route::post('/subcategory/restore', 'App\Http\Controllers\SubcategoryController@restore');
  Route::post('/subcategory/get', 'App\Http\Controllers\SubcategoryController@get');


  Route::post('/family/create', 'App\Http\Controllers\FamilyController@create');
  Route::post('/family/update', 'App\Http\Controllers\FamilyController@update');
  Route::post('/family/delete', 'App\Http\Controllers\FamilyController@delete');
  Route::post('/family/restore', 'App\Http\Controllers\FamilyController@restore');


  Route::post('/product/create', 'App\Http\Controllers\ProductController@create');
  Route::post('/product/update', 'App\Http\Controllers\ProductController@update');
  Route::post('/product/delete', 'App\Http\Controllers\ProductController@delete');
  Route::post('/product/restore', 'App\Http\Controllers\ProductController@restore');
  Route::post('/product/get', 'App\Http\Controllers\ProductController@get');


  Route::post('/stock/create', 'App\Http\Controllers\StockController@create');
  Route::post('/stock/create/multi', 'App\Http\Controllers\StockController@multi_create');
  Route::post('/stock/update', 'App\Http\Controllers\StockController@update');
  Route::post('/stock/delete', 'App\Http\Controllers\StockController@delete');
  Route::post('/stock/restore', 'App\Http\Controllers\StockController@restore');
  Route::post('/stock/get', 'App\Http\Controllers\StockController@get');


  Route::post('/discount/create', 'App\Http\Controllers\DiscountController@create');
  Route::post('/discount/update', 'App\Http\Controllers\DiscountController@update');
  Route::post('/discount/delete', 'App\Http\Controllers\DiscountController@delete');
  Route::post('/discount/restore', 'App\Http\Controllers\DiscountController@restore');


  Route::post('/ad/create', 'App\Http\Controllers\AdController@create');
  Route::post('/ad/update', 'App\Http\Controllers\AdController@update');
  Route::post('/ad/delete', 'App\Http\Controllers\AdController@delete');
  Route::post('/ad/restore', 'App\Http\Controllers\AdController@restore');


  Route::post('/offer/create', 'App\Http\Controllers\OfferController@create');
  Route::post('/offer/update', 'App\Http\Controllers\OfferController@update');
  Route::post('/offer/delete', 'App\Http\Controllers\OfferController@delete');
  Route::post('/offer/restore', 'App\Http\Controllers\OfferController@restore');



  Route::post('/section/add', 'App\Http\Controllers\SectionController@add');
  Route::post('/section/delete', 'App\Http\Controllers\SectionController@delete');
  Route::post('/section/switch', 'App\Http\Controllers\SectionController@switch');
  Route::post('/section/insert', 'App\Http\Controllers\SectionController@insert');


  Route::post('/driver/create', 'App\Http\Controllers\DriverController@create');
  Route::post('/driver/update', 'App\Http\Controllers\DriverController@update');
  Route::post('/driver/delete', 'App\Http\Controllers\DriverController@delete');
  Route::post('/driver/restore', 'App\Http\Controllers\DriverController@restore');

  Route::post('/item/add', 'App\Http\Controllers\ItemController@add');
  Route::post('/item/edit', 'App\Http\Controllers\ItemController@edit');
  Route::post('/item/update', 'App\Http\Controllers\ItemController@update');
  Route::post('/item/delete', 'App\Http\Controllers\ItemController@delete');
  Route::post('/item/restore', 'App\Http\Controllers\ItemController@restore');

  Route::post('/order/create', 'App\Http\Controllers\OrderController@create');
  Route::post('/order/update', 'App\Http\Controllers\OrderController@update');
  Route::post('/order/delete', 'App\Http\Controllers\OrderController@delete');

  Route::post('/invoice/update', 'App\Http\Controllers\InvoiceController@update');

  Route::post('/cart/refresh', 'App\Http\Controllers\CartController@refresh');
  Route::get('/cart/empty', 'App\Http\Controllers\CartController@empty');

  Route::post('/notice/create', 'App\Http\Controllers\NoticeController@create');
  Route::post('/notice/update', 'App\Http\Controllers\NoticeController@update');
  Route::post('/notice/delete', 'App\Http\Controllers\NoticeController@delete');

 // Route::get('/documentation/privacy_policy', 'App\Http\Controllers\DocumentationController@index')->name('documentation_privacy_policy');
//  Route::get('/documentation/about', 'App\Http\Controllers\DocumentationController@index')->name('documentation_about');
Route::get('/policy', 'App\Http\Controllers\DocumentationController@policy')->name('policy');
  Route::post('/documentation/update', 'App\Http\Controllers\DocumentationController@update');

  Route::post('/user/delete', 'App\Http\Controllers\UserController@delete');
  Route::post('/user/restore', 'App\Http\Controllers\UserController@restore');
  Route::post('/user/update', 'App\Http\Controllers\UserController@update');
  Route::post('/city/get', 'App\Http\Controllers\CityController@get');
  Route::post('/shipping/switch', 'App\Http\Controllers\SetController@shipping');
  Route::post('/version/update', 'App\Http\Controllers\VersionController@update');

  Route::get('/pos', function () {
    return view('content.misc.pos');
  })->name('pos');

  Route::get('/diffusion', function () {
    return view('content.misc.diffusion');
  })->name('diffusion');
  //language
  Route::get('/lang', function () {
    $locale = session()->get('locale') == 'ar' ? 'fr' : 'ar';
    Session::put('locale', $locale);
    App::setLocale(Session::get('locale'));
    return redirect()->back();
  });

});

Route::get('/privacy-policy', function () {
  return view('content.pages.privacy-policy')->with('data',\App\Models\Documentation::privacy_policy()->content_en);
})->name('privacy-policy');

Route::get('/delete-account', function () {
  return view('content.pages.delete-account');
})->name('delete-account');

Route::get('/auth/redirect', 'App\Http\Controllers\authentications\LoginBasic@redirect');

Route::get('/auth/callback', 'App\Http\Controllers\authentications\LoginBasic@callback');


// pages
Route::group(['middleware' => ['auth']], function () {
//  Route::get('/pages/account-settings-account', 'App\Http\Controllers\pages\AccountSettingsAccount@index')->name('pages-account-settings-account');
//  Route::get('/pages/account-settings-notifications', 'App\Http\Controllers\pages\AccountSettingsNotifications@index')->name('pages-account-settings-notifications');
//  Route::get('/pages/account-settings-connections', 'App\Http\Controllers\pages\AccountSettingsConnections@index')->name('pages-account-settings-connections');
//  Route::get('/pages/misc-under-maintenance', 'App\Http\Controllers\pages\MiscUnderMaintenance@index')->name('pages-misc-under-maintenance');
Route::get('/pages/misc-error', 'App\Http\Controllers\pages\MiscError@index')->name('pages-misc-error');
});
// authentication
Route::get('/auth/register-basic', 'App\Http\Controllers\authentications\RegisterBasic@index')->name('auth-register-basic');
Route::post('/auth/register-action', 'App\Http\Controllers\authentications\RegisterBasic@register');
Route::get('/auth/login-basic', 'App\Http\Controllers\authentications\LoginBasic@index')->name('login');
Route::post('/auth/login-action', 'App\Http\Controllers\authentications\LoginBasic@login');
Route::get('/auth/logout', 'App\Http\Controllers\authentications\LogoutBasic@logout')->name('auth-logout');
//Route::get('/auth/forgot-password-basic', 'App\Http\Controllers\authentications\ForgotPasswordBasic@index')->name('auth-reset-password-basic');


Route::get('/downloadApp',function(){
  return view('redirect');
})->name('');

