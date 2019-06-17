<div class="quick-panel-sidebar" fuse-cloak data-fuse-bar="quick-panel-sidebar"
     data-fuse-bar-position="right">
    <div class="list-group" class="date">
        <?php date_default_timezone_set('Asia/Manila'); ?>
        <div class="list-group-item subheader">TODAY <br>
            <!--CLOCK DISPLAY-->
              <span class="clock"></span>
                <script type="text/javascript">
                    jQuery(function($) {
                  setInterval(function() {
                    var date = new Date(),
                        time = date.toLocaleTimeString();
                    $(".clock").html(time);
                  }, 1000);
                });
                </script>
        </div>
        <div class="list-group-item two-line">
            <div class="text-muted">
                <div class="h1">
                    <?php echo strftime('%A'); ?>
                </div>
                <div class="h2 row no-gutters align-items-start">
					<span>
						<?php echo date('j') ?>
					</span>
                    <span class="h6">
						<?php echo date('S') ?>
					</span>
                    <span>
						<?php echo date('M') ?>
					</span>
                </div>
            </div>
        </div>

    </div>
    <div class="divider"></div>
    <div class="list-group">
         <div class="list-group-item subheader">Action Buttons</div>
         <div class="list-group-item two-line">
             <div class="list-item-content">
                <!-- <a href="#" class="btn btn-secondary btn-lg active" role="button" data-toggle="modal" data-target="#WinningResult" aria-pressed="true">Winning Result</a>         
                <br> -->
                @if (\Route::current()->getName() == 'dashboard' || \Route::current()->getName() == 'outlet-dashboard')
                <a href="#" class="btn btn-secondary btn-lg active" role="button" data-toggle="modal" data-target="#SearchWinResultAdmin" aria-pressed="true">Payouts</a>
                <br>
                <a href="#" class="btn btn-secondary btn-lg active" role="button" data-toggle="modal" data-target="#CancelTicket" aria-pressed="true">Cancel Ticket</a> 
                <br>
                @endif
                @if (\Route::current()->getName() == 'outlet-dashboard' && (!auth()->user()->is_admin && auth()->user()->outlets()->count() <= 0))
                <a href="#" class="btn btn-secondary btn-lg active" role="button" data-toggle="modal" data-target="#NewTicketForm" aria-pressed="true">Bet Ticket</a>
                <br>
                @endif
                @if (\Route::current()->getName() == 'all-transactions')
                <a href="#" class="btn btn-secondary btn-lg active" role="button" data-toggle="modal" data-target="#FilterTransaction" aria-pressed="true">Filter Transactions</a>
                <br>
                <a href="{{ route('pdf-transactions', ['page' => $page]) }}{{ !is_null($query) ? '?' . $query : '' }}" class="btn btn-secondary btn-lg active" role="button">Download as PDF</a>
                @endif
             </div>
         </div>
    </div>
    <div class="divider"></div>
    <div class="list-group">
        <div class="list-group-item subheader">Notes



        </div>
        <!-- <div class="list-group-item two-line">
            <div class="list-item-content">
                <h3>Bank Deposit</h3>
                <p>Last edit: Sep 9th, 2017</p>
            </div>
        </div>
        <div class="list-group-item two-line">
            <div class="list-item-content">
                <h3>Request for Receipt Papers</h3>
                <p>Last edit: Sep 2nd, 2017</p>
            </div>
        </div> -->

    </div>
</div>
