@extends('layouts.main') @section('content')


<div class="page-layout carded full-width">

    <div class="top-bg bg-secondary"></div>

    <!-- CONTENT -->
    <div class="page-content">
      <!-- HEADER -->
      <div class="header bg-secondary text-auto row no-gutters align-items-center justify-content-between">

          <!-- APP TITLE -->
          <div class="col-10 col-sm">

              <div class="logo row no-gutters align-items-start">
                  <div class="logo-icon mr-3 mt-1">
                      <i class="icon-message-text s-6"></i>
                  </div>
                  <div class="logo-text">
                      <div class="h4">User logs for {{ $user->name }}</div>
                  </div>
              </div>
          </div>


          <!-- / APP TITLE -->
      </div>


      <div class="page-content-card">

          <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">

              <div class="dataTables_scroll">

                  <div class="dataTables_scrollBody">
                      <table id="e-commerce-orders-table" class="table dataTable">
                          <thead>
                            <tr>
                                <th>
                                    <div class="table-header">
                                        <span class="column-title">Date</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span class="column-title">Action</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="table-header">
                                        <span class="column-title">Description</span>
                                    </div>
                                </th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse ($logs as $log)
                              <tr>
                                <td>{{ Carbon\Carbon::parse($log['log_time'])->toDayDateTimeString() }}</td>
                                <td>{{ $log['mode'] }}</td>
                                <td>{{ $log['description'] }}</td>
                              </tr>
                            @empty
                              <tr>
                                <td><p>No logs yet</p></td>
                              </tr>
                            @endforelse
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>





    </div>
    <!-- / CONTENT -->
</div>

@endsection
