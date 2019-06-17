@extends('layouts.mail')

@section('content')
<table>
	<tr>
  		<td class="td-width">Name: </td>
  		<td>{{ $user->name }}</td>
  	</tr>
  	<tr>
  		<td class="td-width">Account: </td>
  		<td>{{ $user->email }}</td>
  	</tr>
  	<tr>
  		<td class="td-width">Login: </td>
  		<td>{{ $login_info->log_time }} PHT</td>
  	</tr>
  	<tr>
  		<td class="td-width">IP Address: </td>
  		<td>{{ $login_info->ip_address }}</td>
  	</tr>
  	<tr>
  		<td class="td-width">Device/Agent: </td>
  		<td>{{ $login_info->agent }}</td>
  	</tr>
</table>
@endsection