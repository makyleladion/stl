<?php

use Illuminate\Http\Request;

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
Route::group(['middleware' => 'auth:api'], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/winners', 'Api\OfflineSyncController@getWinners')->name('api-winners');
    Route::get('/winning-results', 'Api\OfflineSyncController@getWinnings')->name('api-winning-results');
    /*Route::get('/payouts', 'Api\OfflineSyncController@getUpdatedPayouts')->name('api-payouts'); */
    Route::post('/new_transaction', 'Api\TransactionsApiController@index')->name('api-new-transaction');

    Route::get('/ping_server', 'Api\ServerPingController@index')->name('api-server-ping');
    Route::post('/ping_server', 'Api\ServerPingController@index')->name('api-server-ping-post');

    // Endpoints for mobile

    Route::post("/per_outlet/", "Api\TransactionsApiController@perOutlet")->name('per_outlet');
    Route::post("/new_transaction_mobile", "Api\TransactionsApiController@postCreateTransaction")->name('new-transaction-mobile');
    Route::post("/outlet_calc_winning", "Api\TransactionsApiController@ajaxCalculateWinning");
    Route::post("/update_password", "Api\LoginApiController@updatePassword")->name('update-password');
    Route::post("/daily_sales", "Api\ReportsApiController@getDailySales")->name('daily-sales-api');

    Route::post("/check_ticket_for_payout", "Api\PayoutsApiController@checkTicketForPayout")->name('check-ticket-payout');
    Route::post("/post_payout", "Api\PayoutsApiController@postPayout");

    Route::post("/set_outlet_status", "Api\OutletsApiController@setOutletStatus")->name('set-outlet-status');
    Route::post("/get_outlet_status", "Api\OutletsApiController@getOutletStatus");

    Route::post("/log_user", "Api\LoginApiController@logUser");

    Route::post("/cancelled-tickets", "Api\TransactionsApiController@getCancelledTransactions")->name('api-cancelled-transactions');
    Route::post("/transactions", "Api\TransactionsApiController@getTransactions")->name('api-transactions');
    Route::post("/cancel-ticket", "Api\TransactionsApiController@cancelTicket")->name('api-cancel-ticket');
    Route::post("/invalid-tickets", "Api\TransactionsApiController@getInvalidAPITransactions")->name('api-cancel-ticket');

    Route::post("/memos", "Api\UsersApiController@getMemos")->name('api-user-memos');
    Route::post("/memo", "Api\UsersApiController@getMemo")->name('api-user-memo');
    Route::post("/set-user-betting", "Api\UsersApiController@setUserBettingStatus")->name('api-user-betting');

    Route::post("/winnings", "Api\TransactionsApiController@getWinningTickets")->name('api-winnings');
    
});

Route::get('/login', 'Api\LoginApiController@index')->name('api-login');
Route::get('/', 'Api\LoginApiController@showCase')->name('api-showcase');
