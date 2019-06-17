@extends('layouts.mail2')

@section('content')
<table style="width:100%">
    <thead>
        <tr>
            <th style="min-width: 120px;">
                <div>
                    <span>Name</span>
                </div>
            </th>

            <th style="min-width: 120px;">
                <div>
                    <span>Address</span>
                </div>
            </th>

            <th style="min-width: 120px;">
                <div>
                    <span>Owner</span>
                </div>
            </th>

            <th style="min-width: 120px;">
                <div>
                    <span>Assigned Tellers</span>
                </div>
            </th>

            <th style="min-width: 120px;">
                <div>
                    <span>Time-In</span>
                </div>
            </th>

        </tr>
    </thead>

    <tbody>
        @foreach($outlets as $outlet)
        <tr>
            <td>{{ $outlet->name() }}</td>
            <td>{{ $outlet->address() }}</td>
            <td>{{ $outlet->owner() }}</td>
            <td>{{ $outlet->assignedTellers(true) }}</td>
            <td><b>{{ $outlet->getTimeIn() }}</b></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
