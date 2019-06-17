        <!-- Filter Transactions -->
        <div class="modal fade text-left" id="filterPayouts" tabindex="-1" role="dialog" aria-labelledby="newOutlet" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="newOutlet">Filter Payouts</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="get" id="transaction-filter-form" action="{{ route('all-transactions') }}">
                        <div class="modal-body"> 
                            <div class="form-group" id="pick-a-date">
                                <label>Select Date From</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type='text' name="datefrom" id="datefrom" class="form-control pickadate" placeholder="Day Month, Year" />
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
                                    <input type='text' name="dateto" id="dateto" class="form-control pickadate" placeholder="Day Month, Year" />
                                </div>
                            </div>   
                            <div class="form-group">
                                <label>Select Outlet</label>
                                <select name="outlet" class="form-control" id="outlet-filter">
                                    @if (isset($outlets))
                                    @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                    @endif
                                </select>                               
                            </div>                                               
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-outline-danger round" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-outline-primary round">Continue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        $(document).ready(function() {
            $('#datefrom').datepicker({format:'yyyy-mm-dd'});
            $('#dateto').datepicker({format:'yyyy-mm-dd'});
        });