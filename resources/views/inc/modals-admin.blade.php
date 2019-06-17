
<!-- import modal -->
<div id="importTransactions" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="import-form" action="{{ route('import-transactions')}}" enctype="multipart/form-data">
        <div class="modal-header">
          <h4 class="modal-title" id="importModal">Import Transactions</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-xs-5">&nbsp;&nbsp&nbsp</div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label for="transactionimport">Select JSON file:</label>
                        <input class="form-control" type="file" name="transactionimport" id="transactionimport">
                    </div>
                </div>
                <div class="col-xs-3">&nbsp;</div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if (session('message_import'))
<div id="importTransactionsResult" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
     <div class="modal-header">
          <h4 class="modal-title" id="importTransactionsResultModal">Import Result</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p>{{ session('message_import')  }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
            $('#importTransactionsResult').modal('show')  
    });
</script>

@endif