@extends('layouts.master')

@section('title', 'Add Credit')

@section('content')
<div class="container mt-4">
    <h2>Add Credit for {{ $user->name }}</h2>
    <form action="{{ route('add_credit', $user->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="credit">Credit Amount</label>
            <input type="number" name="credit" id="credit" class="form-control" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success mt-2">Add Credit</button>
    </form>
</div>
@endsection