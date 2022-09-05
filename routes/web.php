<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    } else {
        return view('auth.login');
    }
})->name('front');

Auth::routes();

Route::group(['middleware' => ['auth'], 'namespace' => 'admin'], function () {

    //dashboard route start
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    //dashboard route end

    //return product route start
    Route::get('return','ReturnController@index')->name('return');
    Route::get('return/get_return_list','ReturnController@getData')->name('return.get_return_list');
    Route::get('return/getData/{seller}/{retailer}','ReturnController@getData')->name('return.getData');
    Route::get('return/create','ReturnController@create')->name('return.create');
    Route::post('retailer-wise/products','ReturnController@retailerWiseProducts')->name('retailer_wise.products');
    Route::get('return/status_change/{id}', 'ReturnController@statusChange')->name('return.status_change');
    Route::get('return/show/{id}', 'ReturnController@show')->name('return.show');
    Route::get('return/edit/{id}', 'ReturnController@edit')->name('return.edit');
    Route::put('return/update/{id}', 'ReturnController@update')->name('return.update');

    Route::post('return/store','ReturnController@store')->name('return.store');
    //return product route end
    Route::get('return/retailer/list','ReturnController@returnRetailerList')->name('return.retailer.list');

    //sales invoice route start
    Route::get('overview','OverviewController@index')->name('overview');
    Route::get('overview/getData/{id?}','OverviewController@getData')->name('overview.getData');
    Route::get('overview/history/{role}/{seller?}/{id}','OverviewController@history')->name('overview.history');
    Route::get('seller/information','OverviewController@sellerInformation')->name('seller.information');
    //sales invoice route end

    //order distribute routes start
    Route::get('order_distribute','OrderDistributesController@index')->name('order_distribute');
    Route::get('order_distribute/getData','OrderDistributesController@getData')->name('order_distribute.getData');
    Route::get('order/chalan','OrderDistributesController@chalan')->name('order.chalan');
    Route::get('order/distribute/approve/{id}','OrderDistributesController@distributeApprove')->name('order.distribute.approve');
    Route::get('order/invoice','OrderDistributesController@invoice')->name('order.invoice');



    //order distribute routes end

    //stock route start
    Route::get('stock', 'StockController@index')->name('stock');
    Route::get('stock/getData', 'StockController@getData')->name('stock.getData');
    Route::post('stock/store', 'StockController@store')->name('stock.store');
    Route::get('stock/edit/{id}', 'StockController@edit')->name('stock.edit');
    Route::put('stock/update/{id}', 'StockController@update')->name('stock.update');
    Route::delete('stock/destroy/{id}', 'StockController@destroy')->name('stock.destroy');
    Route::get('stock/product/category_brand', 'StockController@stockProductCategoryBrand')->name('stock.product.category_brand');
    //stock route end
    Route::get('/stock/{type}', 'InventoryController@index')->name('inventory');
    Route::get('inventory/getData/{type}', 'InventoryController@inventoryGetData')->name('inventory.getData');


    //units route start
    Route::get('unit', 'UnitController@index')->name('unit');
    Route::get('unit/getData', 'UnitController@getData')->name('unit.getData');
    Route::get('unit/create', 'UnitController@create')->name('unit.create');
    Route::post('unit/store', 'UnitController@store')->name('unit.store');
    Route::get('unit/edit/{id}', 'UnitController@edit')->name('unit.edit');
    Route::put('unit/update/{id}', 'UnitController@update')->name('unit.update');
    Route::delete('unit/destroy/{id}', 'UnitController@destroy')->name('unit.destroy');
    Route::get('unit/status_change/{id}', 'UnitController@statusChange')->name('unit.status_change');
    //units route end

    //banks route start
    Route::get('bank', 'BankController@index')->name('bank');
    Route::get('bank/getData', 'BankController@getData')->name('bank.getData');
    Route::get('bank/create', 'BankController@create')->name('bank.create');
    Route::post('bank/store', 'BankController@store')->name('bank.store');
    Route::get('bank/edit/{id}', 'BankController@edit')->name('bank.edit');
    Route::put('bank/update/{id}', 'BankController@update')->name('bank.update');
    Route::delete('bank/destroy/{id}', 'BankController@destroy')->name('bank.destroy');
    Route::get('bank/status_change/{id}', 'BankController@statusChange')->name('bank.status_change');
    //banks route end

    //category route start
    Route::get('category', 'CategoryController@index')->name('category');
    Route::get('category/getData', 'CategoryController@getData')->name('category.getData');
    Route::get('category/create', 'CategoryController@create')->name('category.create');
    Route::post('category/store', 'CategoryController@store')->name('category.store');
    Route::get('category/edit/{id}', 'CategoryController@edit')->name('category.edit');
    Route::put('category/update/{id}', 'CategoryController@update')->name('category.update');
    Route::delete('category/destroy/{id}', 'CategoryController@destroy')->name('category.destroy');
    Route::get('category/status_change/{id}', 'CategoryController@statusChange')->name('category.status_change');
    //category route end

    //Brand route start
    Route::get('brand', 'BrandController@index')->name('brand');
    Route::get('brand/getData', 'BrandController@getData')->name('brand.getData');
    Route::get('brand/create', 'BrandController@create')->name('brand.create');
    Route::post('brand/store', 'BrandController@store')->name('brand.store');
    Route::get('brand/edit/{id}', 'BrandController@edit')->name('brand.edit');
    Route::put('brand/update/{id}', 'BrandController@update')->name('brand.update');
    Route::delete('brand/destroy/{id}', 'BrandController@destroy')->name('brand.destroy');
    Route::get('brand/status_change/{id}', 'BrandController@statusChange')->name('brand.status_change');
    //Brand route end

    //return reason route start
    Route::get('return_reason', 'ProductReturnReasonController@index')->name('return_reason');
    Route::get('return_reason/getData', 'ProductReturnReasonController@getData')->name('return_reason.getData');
    Route::get('return_reason/create', 'ProductReturnReasonController@create')->name('return_reason.create');
    Route::post('return_reason/store', 'ProductReturnReasonController@store')->name('return_reason.store');
    Route::get('return_reason/edit/{id}', 'ProductReturnReasonController@edit')->name('return_reason.edit');
    Route::put('return_reason/update/{id}', 'ProductReturnReasonController@update')->name('return_reason.update');
    Route::delete('return_reason/destroy/{id}', 'ProductReturnReasonController@destroy')->name('return_reason.destroy');
    Route::get('return_reason/status_change/{id}', 'ProductReturnReasonController@statusChange')->name('return_reason.status_change');
    //Brand route end

    //return reason route start
    Route::get('expanse_reason', 'ExpanseReasonController@index')->name('expanse_reason');
    Route::get('expanse_reason/getData', 'ExpanseReasonController@getData')->name('expanse_reason.getData');
    Route::get('expanse_reason/create', 'ExpanseReasonController@create')->name('expanse_reason.create');
    Route::post('expanse_reason/store', 'ExpanseReasonController@store')->name('expanse_reason.store');
    Route::get('expanse_reason/edit/{id}', 'ExpanseReasonController@edit')->name('expanse_reason.edit');
    Route::put('expanse_reason/update/{id}', 'ExpanseReasonController@update')->name('expanse_reason.update');
    Route::delete('expanse_reason/destroy/{id}', 'ExpanseReasonController@destroy')->name('expanse_reason.destroy');
    Route::get('expanse_reason/status_change/{id}', 'ExpanseReasonController@statusChange')->name('expanse_reason.status_change');
    //Brand route end

    //expanse route start
    Route::get('expanse', 'ExpanseController@index')->name('expanse');
    Route::get('expanse/getData', 'ExpanseController@getData')->name('expanse.getData');
    Route::get('expanse/create', 'ExpanseController@create')->name('expanse.create');
    Route::post('expanse/store', 'ExpanseController@store')->name('expanse.store');
    Route::get('expanse/edit/{id}', 'ExpanseController@edit')->name('expanse.edit');
    Route::put('expanse/update/{id}', 'ExpanseController@update')->name('expanse.update');
    Route::delete('expanse/destroy/{id}', 'ExpanseController@destroy')->name('expanse.destroy');
    Route::get('expanse/status_change/{id}', 'ExpanseController@statusChange')->name('expanse.status_change');
    //expanse route end

    //products route start
    Route::get('product', 'ProductController@index')->name('product');
    Route::get('product/getData', 'ProductController@getData')->name('product.getData');
    Route::get('product/create', 'ProductController@create')->name('product.create');
    Route::post('product/store', 'ProductController@store')->name('product.store');
    Route::get('product/edit/{id}', 'ProductController@edit')->name('product.edit');
    Route::put('product/update/{id}', 'ProductController@update')->name('product.update');
    Route::delete('product/destroy/{id}', 'ProductController@destroy')->name('product.destroy');
    Route::get('product/status_change/{id}', 'ProductController@statusChange')->name('product.status_change');
    //products route end

    //user management route start
    //permission route start
    Route::get('permission', 'PermissionController@index')->name('permission');
    Route::get('permission/getData', 'PermissionController@getData')->name('permission.getData');
    Route::get('permission/create', 'PermissionController@create')->name('permission.create');
    Route::post('permission/store', 'PermissionController@store')->name('permission.store');
    Route::get('permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
    Route::get('permission/edit/{id}', 'PermissionController@edit')->name('permission.edit');
    Route::put('permission/update/{id}', 'PermissionController@update')->name('permission.update');
    Route::delete('permission/destroy/{id}', 'PermissionController@destroy')->name('permission.destroy');
    //permission route end

    //roles route start
    Route::get('role', 'RoleController@index')->name('role');
    Route::get('role/getData', 'RoleController@getData')->name('role.getData');
    Route::get('role/create', 'RoleController@create')->name('role.create');
    Route::post('role/store', 'RoleController@store')->name('role.store');
    Route::get('role/edit/{id}', 'RoleController@edit')->name('role.edit');
    Route::put('role/update/{id}', 'RoleController@update')->name('role.update');
    Route::delete('role/destroy/{id}', 'RoleController@destroy')->name('role.destroy');
    //roles route end

    //user route start
    Route::get('user', 'UserController@index')->name('user');
    Route::get('user/getData', 'UserController@getData')->name('user.getData');
    Route::get('user/create', 'UserController@create')->name('user.create');
    Route::post('user/store', 'UserController@store')->name('user.store');
    Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit');
    Route::put('user/update/{id}', 'UserController@update')->name('user.update');
    Route::delete('user/destroy/{id}', 'UserController@destroy')->name('user.destroy');
    Route::get('user/status_change/{id}', 'UserController@statusChange')->name('user.status_change');
    Route::get('all-seller-get', 'UserController@getAllSeller')->name('all-seller-get');
    //user route end

    //order manage route start
    Route::get('order', 'OrderController@index')->name('order');
    Route::get('order/getData', 'OrderController@getData')->name('order.getData');
    Route::get('order/create', 'OrderController@create')->name('order.create');
    Route::post('order/store', 'OrderController@store')->name('order.store');
    Route::get('order/edit/{id}', 'OrderController@edit')->name('order.edit');
    Route::put('order/update/{id}', 'OrderController@update')->name('order.update');
    Route::delete('order/destroy/{id}', 'OrderController@destroy')->name('order.destroy');
    Route::get('order/get_old_out_standing', 'OrderController@getOldOutStanding')->name('order.get.old_out_standing');
    Route::get('order/get/retailer', 'OrderController@getRetailer')->name('get.retailer');
    Route::get('order/get_price', 'OrderController@getPrice')->name('order.get_price');
    Route::get('order/brand/category/products', 'OrderController@brandCategoryProducts')->name('brand.category.products');
    Route::get('order/product/unit/stock/price', 'OrderController@productUnitStockPrice')->name('productUnitStockPrice');

    Route::get('order/approve/list', 'OrderController@approveList')->name('order.approve.list');
    Route::get('order/getApproveData', 'OrderController@getApproveData')->name('order.getApproveData');
    Route::get('order/approve/{id}', 'OrderController@approve')->name('order.approve');

    Route::get('order/cancel/list', 'OrderController@cancelList')->name('order.cancel.list');
    Route::get('order/getCancelData', 'OrderController@getCancelData')->name('order.getCancelData');
    //order manage route end

    //transaction route start
    Route::get('transaction', 'TransactionController@index')->name('transaction');
    Route::get('transaction/getData', 'TransactionController@getData')->name('transaction.getData');
    Route::get('order/transaction/getData/{id}/{retailer_id}', 'TransactionController@transactionGetData')->name('order.transaction.getData');
    Route::get('transaction/create/{id}', 'TransactionController@create')->name('transaction.create');
    // Route::get('transaction/seller_data', 'TransactionController@sellerData')->name('transaction.seller_data');
    Route::post('transaction/store', 'TransactionController@store')->name('transaction.store');

    Route::get('large/transaction', 'LargeTransactionController@index')->name('largeTransaction');
    Route::post('large/transaction/store', 'LargeTransactionController@store')->name('largeTransaction.store');
    Route::get('largeTransaction/getData', 'LargeTransactionController@getData')->name('largeTransaction.getData');
    Route::get('largeTransaction/retailer/list','LargeTransactionController@largeTransactionRetailerList')->name('largeTransaction.retailer.list');

    //transaction route end

    //adjustment route start
    Route::get('adjustment', 'AdjustmentController@index')->name('adjustment');
    Route::get('adjustment/getData', 'AdjustmentController@getData')->name('adjustment.getData');
    Route::get('adjustment/create', 'AdjustmentController@create')->name('adjustment.create');
    Route::get('adjustment/edit/{id}', 'AdjustmentController@edit')->name('adjustment.edit');
    Route::post('adjustment/store', 'AdjustmentController@store')->name('adjustment.store');
    Route::get('adjustment/retailer/list','AdjustmentController@adjustmentRetailerList')->name('adjustment.retailer.list');
    Route::get('adjustment/retailer/outStanding','AdjustmentController@outStanding')->name('adjustment.retailer.outStanding');
    Route::put('adjustment/update/{id}', 'AdjustmentController@update')->name('adjustment.update');

    //adjustment route
    //user management route end

    // report
    Route::get('report/show', 'ReportController@show')->name('report');
    Route::get('all/report', 'ReportController@reportManage')->name('reportManage');
});
