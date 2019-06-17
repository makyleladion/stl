@extends('layouts.main') @section('content')

  <div class="page-layout carded full-width">

    <div class="top-bg bg-secondary"></div>

    <!-- CONTENT -->
    <div class="page-content">

      <!-- HEADER -->
      <div class="header bg-secondary text-auto row no-gutters align-items-center justify-content-between">

        <!-- APP TITLE -->
        <div class="col-12 col-sm">

          <div class="logo row no-gutters align-items-start">
            <div class="logo-icon mr-3 mt-1">
              <i class="icon-account-plus s-6"></i>
            </div>
            <div class="logo-text">
              <div class="h4">New User</div>
            </div>
          </div>

        </div>
        <!-- / APP TITLE -->

      </div>
      <!-- / HEADER -->

      <div class="page-content-card">
        <div class="col-12 col-sm-6 col-xl-12 p-12">
          <div class="widget widget1 card p-6">

              @if (\Session::has('user-success'))
                <div class="alert alert-success" role="alert">{{ session('user-success') }}</div>
              @endif

              @if (\Session::has('error-flash'))
                <div class="alert alert-danger" role="alert">{{ session('error-flash') }}</div>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

            <form action="{{ route('create-user') }}" method="post">
              {{ csrf_field() }}
              <div class="form-group">
                <input type="text" name="name" id="name" class="form-control" aria-describedby="outlet name" />
                <label>Name</label>
              </div>

              <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" aria-describedby="outlet tags" />
                <label>Email</label>
              </div>

              <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" aria-describedby="outlet tags" />
                <label>Password</label>
              </div>

              <div class="form-group">
                <label>Assign Outlet</label>
                <select name="default_outlet" id="default_outlet" class="form-control">
                  <option value="0">Main Office/Coordinator</option>
                  @foreach ($outlets as $outlet)
                  <option value="{{ $outlet->id() }}">{{ $outlet->name() }}</option>
                  @endforeach
                </select>
              </div>
              
              <div class="form-group">
                <label>Assign Superior</label>
                <select name="user_superior" id="user_superior" class="form-control">
                	@if ($adminUser->isSuperAdmin())
                	<option value="0" id="no-superior">None</option>
                	@endif
                  @foreach ($adminUsers as $user)
                  <option value="{{ $user->id }}">{{ $user->name }}</option>
                  @endforeach
                </select>
              </div>
              
              <div class="form-group" id="user-read-only-toggle">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_read_only" value="1" class="custom-control-input">
                    <span class="custom-control-indicator fuse-ripple-ready"></span>
                	<span class="label">Read-only</span>
              	</label>
              </div>
              
              <div class="form-group" id="user-is-usher-toggle">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_usher" value="1" class="custom-control-input">
                    <span class="custom-control-indicator fuse-ripple-ready"></span>
                	<span class="label">Is usher</span>
              	</label>
              </div>

              <button type="submit" class="btn btn-secondary">SAVE</button>

            </form>

          </div>
        </div>
      </div>
    </div>
    <!-- / CONTENT -->
  </div>

  <script type="text/javascript" src="{{url('/assets/js/apps/e-commerce/product/product.js?v=1')}}"></script>
  <script>
	$(document).ready(function() {
		$("#user-is-usher-toggle").hide();

		var superiorSelect;
		$('#default_outlet').change(function() {
			var id = $(this).val();
			if (id > 0) {
				@if ($adminUser->isSuperAdmin())
				superiorSelect = $("#no-superior").detach();
				@endif
				$("#user-read-only-toggle").hide();
				$("#user-is-usher-toggle").show();
			} else {
				$("#user-read-only-toggle").show();
				$("#user-is-usher-toggle").hide();
				@if ($adminUser->isSuperAdmin())
				superiorSelect.prependTo("#user_superior");
				superiorSelect = null;
				@endif
			}
		});
	});
  </script>

@endsection
