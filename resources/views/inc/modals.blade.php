@if(\Route::current()->getName() == 'dashboard')
    @include('inc.modals.input-results')
    @include('inc.modals.filter-dashboard')
@endif

@if(\Route::current()->getName() == 'all-payouts')
    @include('inc.modals.new-payout')
    @include('inc.modals.filter-payouts')
@endif

@if(\Route::current()->getName() == 'all-transactions')
    @include('inc.modals.filter-transactions')
@endif

@if(\Route::current()->getName() == 'all-transactions-canceled')
    @include('inc.modals.cancel-ticket')
@endif

@if(\Route::current()->getName() == 'reports-summary')
    @include('inc.modals.filter-summary-reports')
@endif

@if(\Route::current()->getName() == 'reports-highest-bet')
    @include('inc.modals.filter-highest-bet')
@endif

@if(\Route::current()->getName() == 'reports-hotnumbers')
    @include('inc.modals.filter-hot-numbers')
@endif
