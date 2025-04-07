@extends('layouts.master')

@section('title', 'User Profile')

@section('content')
<div class="row">
    <div class="m-4 col-sm-6">
        <table class="table table-striped">
            <tr>
                <th>Name</th><td>{{$user->name}}</td>
            </tr>
            <tr>
                <th>Email</th><td>{{$user->email}}</td>
            </tr>
            <tr>
                    <th>Credit</th>
                    <td>${{ number_format($user->credit, 2) }}</td>
                </tr>
            <tr>  
                <th>Roles</th>
                <td>
                    @foreach($user->roles as $role)
                        <span class="badge bg-primary">{{$role->name}}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>Permissions</th>
                <td>
                    @foreach($permissions as $permission)
                        <span class="badge bg-success">{{$permission->display_name}}</span>
                    @endforeach
                </td>
            </tr>
        </table>

        <h3>Purchased Items</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price at Purchase</th>
                    <th>Date Purchased</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->product->name }}</td> 
                        <td>{{ $purchase->quantity }}</td>
                        <td>${{ number_format($purchase->price_at_purchase, 2) }}</td>
                        <td>{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No purchases yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="row">
            <div class="col col-6"></div>
            @if(auth()->user()->hasPermissionTo('admin_users') || auth()->id() == $user->id)
                <div class="col col-4">
                    <a class="btn btn-primary" href="{{ route('edit_password', $user->id) }}">Change Password</a>
                </div>
            @else
                <div class="col col-4"></div>
            @endif
            @if(auth()->user()->hasPermissionTo('edit_users') || auth()->id() == $user->id)
                <div class="col col-2">
                    <a href="{{ route('users_edit', $user->id) }}" class="btn btn-success form-control">Edit</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
