        <!-- Input Winning Result -->
        <div class="modal fade text-left" id="inputWinningResults" tabindex="-1" role="dialog" aria-labelledby="winningResult" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger white">
                        <h4 class="modal-title" id="winningResult">ENTER RESULT</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
			<div class="nav-vertical nav-custom">
              <ul class="nav nav-tabs nav-left">
              	<?php $i = 1; ?>
              	@foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                <li class="nav-item">
                	<a class="nav-link{{ ($i == 1) ? ' active' : ''}}" id="baseVerticalLeft2-tab{{ $i }}" data-toggle="tab" aria-controls="tabVerticalLeft2{{ $i }}" href="#tabVerticalLeft2{{ $i }}" aria-expanded="false"><i class="fa fa-clock-o"></i> {{ date('g-A', strtotime($timeslot)) }}</a>
                </li>
                <?php $i++; ?>
                @endforeach
              </ul>
              <div class="tab-content px-1">
              	<?php $i = 1; ?>
              	@foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                <div role="tabpanel" class="tab-pane{{ ($i == 1) ? ' active' : ''}}" id="tabVerticalLeft2{{ $i }}" aria-expanded="false" aria-labelledby="baseVerticalLeft2-tab{{ $i }}">
                 <h5 class="modal-title" id="winningResult">DRAW RESULT {{ date('gA', strtotime($timeslot)) }}.</h5>
                    <form method="post" id="winning-form" action="{{ route('set-winning-result') }}">
                    	{{ csrf_field() }}
                    		<input name="winning-timeslot" value="{{ $timeslot }}" type="hidden" />
                        <div class="modal-body">
                        		@foreach (\App\System\Games\GamesFactory::getGameNames() as $gameName)
                            <label>{{ \App\System\Games\GamesFactory::getGameLabelByGameName($gameName) }}: </label>
                            <div class="form-group">
                                <input type="text" value="{{ isset($winnings[$sched_key][\App\System\Games\GamesFactory::getGameLabelByGameName($gameName)]) ? $winnings[$sched_key][\App\System\Games\GamesFactory::getGameLabelByGameName($gameName)] : '' }}" name="{{ $gameName . '-result1' }}" maxlength="3" placeholder="Enter {{ \App\System\Games\GamesFactory::getGameLabelByGameName($gameName) }} Result" class="form-control">
                            </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-outline-danger round" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-outline-primary round">Submit Results</button>
                        </div>
                    </form>
                </div>
                <?php $i++; ?>
                @endforeach
              </div>
            </div>


                </div>
            </div>
        </div>

        <!-- Filter Winners -->
        <div class="modal fade text-left" id="filterWinners" tabindex="-1" role="dialog" aria-labelledby="filterWinners" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="winningResult">Filter Winners</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body"> 
                            <div class="form-group" id="pick-a-date">
                                <label>Select Date From</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type='text' class="form-control pickadate" placeholder="Day Month, Year" />
                                </div>
                            </div>   
                            <div class="form-group" id="pick-a-date">
                                <label>Select Date To</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type='text' class="form-control pickadate" placeholder="Day Month, Year" />
                                </div>
                            </div>                                             
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-outline-danger round" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-outline-primary round">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>