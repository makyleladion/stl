        <!-- Cancel Ticker -->
        <div class="modal fade text-left" id="cancelTicket" tabindex="-1" role="dialog" aria-labelledby="newUser" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="newUser">Cancel Ticket</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form  method="post" id="cancel-form" action="{{ route('cancel-ticket') }}">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="position-relative has-icon-left">
                                        <input type="text" name="ticket-number" id="ticket-number-cancellation" class="form-control">
                                        <div class="form-control-position">
                                            <i class="ft-search"></i>
                                        </div>     
                                        <div class="mt-2">
                                            <button type="button" class="col-12 btn btn-info" id="ticket-search-for-cancellation">Search</button>
                                            <div id="force-cancel">
                                                <input type="checkbox" name="force-cancel" id="force-cancel" value="true" />
                                                <label>Force Cancel</label>  
                                            </div>
                                        </div>                                  
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Number</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Win</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cancellation-search-results">
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
            $('#ticket-search-for-cancellation').click(function() {
                var ticketNumber = $('#ticket-number-cancellation').val();
                $.ajax({
                url: '{{ route('check-cancellation') }}',
                type: 'post',
                data: {'ticket-number': $('#ticket-number-cancellation').val(), 'force-cancel' : $('#force-cancel').is(':checked')},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                cache: false,
                success: function (cancel) {
                    $('#cancellation-search-results').html('');
                        if (typeof cancel.error !== 'undefined') {
                            alert(cancel.error);
                        } else {
                            for (var c in cancel.bets) {
                                var html = '<tr>';
                                html += '<td>' + cancel.bets[c].bet + '</td>';
                                html += '<td>' + cancel.bets[c].type + '</td>';
                                html += '<td>' + cancel.bets[c].amount + '</td>';
                                html += '<td>' + cancel.bets[c].draw_datetime + '</td>';
                                html += '<td>' + cancel.bets[c].teller + '</td>';
                                html += '<td>' + cancel.bets[c].outlet + '</td>';
                                html += '</tr>';
                                $('#cancellation-search-results').append(html);
                            }
                        }
                },
                error: function(e) {
                    alert(e);
                }
                });
            });
        });
        </script>