        <!-- Filter Highest Bet -->
        <div class="modal fade text-left" id="highestBet" tabindex="-1" role="dialog" aria-labelledby="newOutlet" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="newOutlet">Filter Highest Bet</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body"> 
                            <div class="form-group" id="pick-a-date">
                                <label>Select Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="fa fa-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type='text' class="form-control pickadate" placeholder="Day Month, Year" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Select Game</label>
                                <select id="projectinput5" name="interested" class="form-control">
                                    <option value="All" selected="" disabled="">All</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="Outlet">Outlet</option> 
                                    <option value="Usher">Usher</option>                                    
                                </select>                                
                            </div>  
                            <div class="form-group">
                                <label>Select Draw Time</label>
                                <select id="projectinput5" name="interested" class="form-control">
                                    <option value="All" selected="" disabled="">All</option>
                                    <option value="11">11 AM</option>
                                    <option value="4">4 PM</option> 
                                    <option value="9">9 PM</option>                                    
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