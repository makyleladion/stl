        <!-- Filter Summary Reports -->
        <div class="modal fade text-left" id="summaryReports" tabindex="-1" role="dialog" aria-labelledby="newOutlet" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="newOutlet">Filter Summary Reports</h4>
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
                            <div class="form-group">
                                <label>Select Option</label>
                                <select id="projectinput5" name="interested" class="form-control">
                                    <option value="All" selected="" disabled="">All</option>
                                    <option value="Outlet">Outlet</option> 
                                    <option value="Usher">Usher</option>                                    
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