            <!-- New Payout -->
        <div class="modal fade text-left" id="newPayout" tabindex="-1" role="dialog" aria-labelledby="newUser" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="newUser">New Payout</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="payout-form" action="{{ route('save-payout') }}">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="position-relative has-icon-left">
                                        <input type="text" name="ticket-number" id="ticket-number" class="form-control">
                                        <div class="form-control-position">
                                            <i class="ft-search"></i>
                                        </div>     
                                        <div class="mt-2">
                                            <button type="button" id="ticket-search-for-payout" class="col-12 btn btn-info">Search</button>
                                        </div>                                  
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <table class="table table-responsive-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Number</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Win</th>
                                            </tr>
                                        </thead>
                                    </table>
                                        <table class="table table-responsive-sm">
                                            <tbody id="payout-search-results">
                                            </tbody>
                                        </table>
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

        <script>
        $(document).ready(function() {
            $('#ticket-search-for-payout').click(function () {
                $.ajax({
                url: '{{ route('check-ticket') }}',
                type: 'post',
                data: {'ticket-number': $('#ticket-number').val()},
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                cache: false,
                success: function (win) {
                    var html = "";
                    for (var b in win.winning_bets) {
                        html += "<tr>";
                        html += "<td scope='row'>" + win.winning_bets[b].number + "</td>";
                        html += "<td>" + win.winning_bets[b].bet_type + "</td>";
                        html += "<td>PHP " + win.winning_bets[b].amount + "</td>";
                        html += "<td>PHP " + win.winning_bets[b].price + "</td>";
                        html += "</tr>";
                    }

                    $('#payout-search-results').html(html);
                        if (typeof win.passbets !== 'undefined') {
                    $('#passbets').val(win.passbets);
                    } else {
                        $('#passbets').val('');
                    }

                    if (typeof win.win_error !== 'undefined') {
                        alert(win.win_error);
                    } else if (win.winning_bets.length <= 0) {
                        alert('Ticket has no winning bet.');
                    }
                },
                error: function(e) {
                    alert('Ticket has no winning bet.');
                }
                });
            });
        });
        </script>