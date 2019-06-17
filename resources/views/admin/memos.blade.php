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
                      <div class="h4">Memos</div>
                  </div>
              </div>
          </div>

          <div class="col-auto">
              <a href="{{route('new-memo')}}" class="btn btn-secondary">ADD NEW MEMO</a>
          </div>


          <!-- / APP TITLE -->
      </div>

      @if (\Session::has('memo-success'))
      <div class="page-content-card">
        <div class="col-12 col-sm-6 col-xl-12 p-12">
              <div class="alert alert-success" role="alert">{{ session('memo-success') }}</div>
        </div>
      </div>
      @endif

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
                                        <span class="column-title">Message</span>
                                    </div>
                                </th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse ($memos as $memo)
                              <tr>
                                <td>{{ Carbon\Carbon::parse($memo['datetime'])->toDayDateTimeString() }}</td>
                                <td>{{ $memo['message'] }}</td>
                              </tr>
                            @empty
                              <tr>
                                <td><p>No memos made yet</p></td>
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
