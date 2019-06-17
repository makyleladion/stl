<?php

use App\User;
use App\Events\CalculatedSalesDataEvent;

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

Route::get('/', 'Auth\LoginController@redirectTo');

Auth::routes();

// STL system
Route::get('/dashboard/{draw_date?}', 'DashboardController@index')->name('dashboard');
Route::get('/dashboard/outlet/{outlet_id}/{draw_date?}', 'DashboardController@perOutlet')->name('outlet-dashboard');
Route::post('/dashboard/outlet/{outlet_id}/calc', 'DashboardController@ajaxCalculateWinning')->name('outlet-calc');
Route::post('/dashboard/outlet/{outlet_id}/create/bets', 'DashboardController@postCreateTransaction')->name('outlet-create-bets');
Route::post('/dashboard/winning', 'DashboardController@postInsertWinning')->name('set-winning-result');
Route::post('/dashboard/winning/check-ticket/{outlet_id?}', 'DashboardController@ajaxCheckTicketForPayout')->name('check-ticket');
Route::post('/dashboard/winning/save/payout/{outlet_id?}', 'DashboardController@postSavePayouts')->name('save-payout');
Route::post('/dashboard/daily-sales/{outlet_id}', 'DashboardController@ajaxGetDailySales')->name('daily-sales');
Route::post('/dashboard/check-cancellation', 'DashboardController@ajaxCheckTicketCancellation')->name('check-cancellation');
Route::post('/dashboard/cancel-ticket', 'DashboardController@postCancelTicket')->name('cancel-ticket');
Route::post('/dashboard/disable-outlet', 'DashboardController@ajaxDisableOutlet')->name('disable-outlet');
Route::post('/user/change-password/{outlet_id?}', 'DashboardController@changePassword')->name('change-password');

Route::get('/transactions/all', 'TransactionsController@all')->name('all-transactions');
Route::get('/transactions/all-canceled/{page?}', 'TransactionsController@allCanceled')->name('all-transactions-canceled');
Route::get('/transactions/per-outlet/{outlet_id}/{page?}', 'TransactionsController@perOutlet')->name('per-outlet-transactions');
Route::get('/transactions/single/{outlet_id}/{transaction_id}', 'TransactionsController@single')->name('single-transaction');
Route::get('/transactions/pdf/{page?}', 'TransactionsController@pdfDownload')->name('pdf-transactions');
Route::get('/transactions/export-unsync-transactions', 'TransactionsController@exportUnsyncTransactions')->name('export-unsync-transactions')->middleware('auth');
Route::post('/transactions/import', 'TransactionsController@importTransactions')->name('import-transactions');
Route::get('/transactions/invalid-tickets/{start?}/{end?}', 'ReportsController@invalidTickets')->name('transactions-invalid-tickets');

Route::get('/payouts/all/{page?}', 'PayoutsController@all')->name('all-payouts');

Route::get('/outlets/all/{page?}', 'OutletsController@all')->name('all-outlets');
Route::get('/outlets/create', 'OutletsController@create')->name('new-outlet');
Route::post('/outlets/create', 'OutletsController@postCreate')->name('create-outlet');
Route::get('/outlets/edit/{outlet_id}', 'OutletsController@edit')->name('edit-outlet');
Route::post('/outlets/edit', 'OutletsController@postEdit')->name('update-outlet');
Route::get('/outlets/remove/{outlet_id}', 'OutletsController@removeOutlet')->name('remove-outlet');

Route::get('/users/all/{page?}', 'UsersController@all')->name('all-users');
Route::get('/users/create', 'UsersController@create')->name('new-user');
Route::post('/users/create', 'UsersController@postCreate')->name('create-user');
Route::get('/users/edit/{user_id}', 'UsersController@edit')->name('edit-user');
Route::post('/users/edit', 'UsersController@postEdit')->name('update-user');
Route::get('/users/delete/{user_id}', 'UsersController@deleteUser')->name('delete-user');
Route::post('/users/logout/{is_idle?}', 'UsersController@logoutUser')->name('user-logout');
Route::get('/users/view-log/{user_id}', 'UsersController@viewLogs')->name('view-user-log');
Route::post('/users/disable-user-betting', 'UsersController@ajaxDisableUserBetting')->name('disable-user-betting');

Route::get('/print/receipt/{ticket_id}', 'PrintController@receipt')->name('receipt');
Route::get('/print/multiple-receipts/{tickets_str}/{pos}', 'PrintController@multipleReceipts')->name('multiple-receipts');
Route::get('/print/sales-print-receipts/{pos}', 'PrintController@salesPrintReceipts')->name('sales-print-receipts');

Route::get('/settings/sms-notification', 'SettingsController@smsNotification')->name('sms-notification');
Route::post('/settings/sms-notification', 'SettingsController@postSaveAdminPhoneNumber')->name('save-sms-notification');
Route::get('/settings/bet-reactivation', 'SettingsController@betReactivation')->name('bet-reactivation');
Route::get('/settings/delete-mobile-number/{mobile_id}', 'SettingsController@processDeleteAdminPhoneNumber')->name('delete-mobile-number');

/*Route::get('/reports', 'ReportsController@index')->name('reports');*/

Route::get('/reports', 'ReportsController@index')->name('reports');
Route::get('/reports/summary/{page?}/{draw_date?}', 'ReportsController@summaryReports')->name('reports-summary');
Route::get('/reports/highest-bet/{draw_date?}', 'ReportsController@highestBets')->name('reports-highest-bet');
Route::get('/reports/hotnumbers/{top?}/{game?}/{draw_date?}/{sched_key?}', 'ReportsController@hotNumbers')->name('reports-hotnumbers');
Route::post('/reports/hotnumbers/capture', 'ReportsController@ajaxRecordSearch')->name('reports-hotnumbers-capture');
Route::post('/reports/separate-sales-mobile', 'ReportsController@getSeparateMobileSales')->name('separate-sales-mobile');
Route::get('/reports/summary-range/winnings', 'ReportsController@dateRangeCalculationsWinnings')->name('reports-date-range-calculations-winnings');
Route::get('/reports/summary-range/winnings/trigger/{date_from?}/{date_to?}', 'ReportsController@triggerRangeCalculationsWinnings')->name('reports-date-range-calculations-winnings-trigger');
Route::get('/reports/summary-range/{page?}/{date_from?}/{date_to?}', 'ReportsController@dateRangeCalculations')->name('reports-date-range-calculations');

Route::get('/memos/all/{page?}', 'MemoController@all')->name('all-memos');
Route::get('/memos/create', 'MemoController@create')->name('new-memo');
Route::post('/memos/create', 'MemoController@postCreate')->name('create-memo');
Route::post('/memos/post/{memoId}', 'MemoController@getMemo')->name('get-memo');
