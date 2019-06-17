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
              <div class="h4">Edit {{ ucfirst($user->role()) }}: {{ $user->name() }}</div>
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

            <form action="{{ route('update-user') }}" method="post">
              {{ csrf_field() }}
              <input type="hidden" name="user_id" value="{{ $user->id() }}" />
              <div class="form-group">
                <input type="text" name="name" id="name" class="form-control" aria-describedby="outlet name" value="{{ $user->name() }}" />
                <label>Name</label>
              </div>

              <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" aria-describedby="outlet tags" value="{{ $user->email() }}" />
                <label>Email</label>
              </div>

              <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" aria-describedby="outlet tags" />
                <label>Replace Password</label>
              </div>
							
			  			@if ($user->role() == $user::ROLE_TELLER)
              <div class="form-group">
                <label>Assign Outlet</label>
                <select name="default_outlet" class="form-control">
                  @foreach ($outlets as $outlet)
                  <option value="{{ $outlet->id() }}"{{ ($default_outlet->id == $outlet->id()) ? ' selected' : '' }}>{{ $outlet->name() }}</option>
                  @endforeach
                </select>
              </div>
              @endif
              
              <div class="form-group">
                <label>Assign Superior</label>
                <select name="user_superior" id="user_superior" class="form-control">
                	@if (auth()->user()->is_superadmin)
                	<option value="0" id="no-superior">None</option>
                	@endif
                  @foreach ($adminUsers as $u)
                  <option value="{{ $u->id }}" {{ ($user->getSuperior() && $u->id == $user->getSuperior()->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                  @endforeach
                </select>
              </div>
              
              @if ($user->role() == $user::ROLE_ADMIN)
              <div class="form-group">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_read_only" value="1" class="custom-control-input"<?php echo $user->isReadOnly() ? ' checked' : '' ?>>
                    <span class="custom-control-indicator fuse-ripple-ready"></span>
                	<span class="label">Read-only</span>
              	</label>
              </div>
              @else
              <div class="form-group">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_usher" value="1" class="custom-control-input"<?php echo $user->isUsher() ? ' checked' : '' ?>>
                    <span class="custom-control-indicator fuse-ripple-ready"></span>
                	<span class="label">Is usher</span>
              	</label>
              </div>
              @endif

              <button type="submit" class="btn btn-secondary">SAVE</button>
              @if (auth()->user()->id != $user->id())
              <a href="{{ route('delete-user', ['user_id' => $user->id()]) }}" class="btn btn-danger delete-btn">DELETE</a>
              @endif
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
		$(".delete-btn").click(function(){
			return confirm("Are you sure you want to delete this user?");
		});
	});
	</script>
@endsection
