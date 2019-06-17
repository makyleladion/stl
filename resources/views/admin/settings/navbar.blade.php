            <ul class="nav nav-tabs" id="myTab" role="tablist">

                <li class="nav-item">
                    <a class="nav-link btn {{ request()->is('settings/bet-reactivation') ? 'active' : '' }}" id="bet-reactivation-tab" href="{{ route('bet-reactivation')}}" aria-controls="bet-reactivation-tab-pane" aria-expanded="true">Bet Reactivation Setting</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link btn {{ request()->is('settings/sms-notification') ? 'active' : '' }}" id="sms-notification-tab" href="{{ route('sms-notification')}}" aria-controls="sms-notification-tab-pane" aria-expanded="true">Admin SMS Notification</a>
                </li>

            </ul>
