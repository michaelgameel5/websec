@extends('layouts.master')

@section('title', 'Products')

@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    <div class="col col-2">
        @can('add_products')
        <a href="{{ route('products_edit') }}" class="btn btn-success form-control">Add Product</a>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mt-2">
        {{ session('error') }}
    </div>
@endif

<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="number" class="form-control" placeholder="Min Price" value="{{ request()->min_price }}"/>
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="number" class="form-control" placeholder="Max Price" value="{{ request()->max_price }}"/>
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" disabled {{ request()->order_by == "" ? 'selected' : '' }}>Order By</option>
                <option value="name" {{ request()->order_by == "name" ? 'selected' : '' }}>Name</option>
                <option value="price" {{ request()->order_by == "price" ? 'selected' : '' }}>Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" disabled {{ request()->order_direction == "" ? 'selected' : '' }}>Order Direction</option>
                <option value="ASC" {{ request()->order_direction == "ASC" ? 'selected' : '' }}>ASC</option>
                <option value="DESC" {{ request()->order_direction == "DESC" ? 'selected' : '' }}>DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <a href="{{ route('products_list') }}" class="btn btn-danger">Reset</a>
        </div>
    </div>
</form>

@foreach($products as $product)
<div class="card mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col col-sm-12 col-lg-4">
                <img src="{{ asset('images/' . $product->photo) }}" class="img-thumbnail" alt="{{ $product->name }}" width="100%">
            </div>
            <div class="col col-sm-12 col-lg-8 mt-3">
                <div class="row mb-2">
                    <div class="col-6">
                        <h3>{{ $product->name }}</h3>
                    </div>
                    <div class="col-2">
                        @can('edit_products')
                        <a href="{{ route('products_edit', $product->id) }}" class="btn btn-success form-control">Edit</a>
                        @endcan
                    </div>
                    <div class="col-2">
                        @can('delete_products')
                        <a href="{{ route('products_delete', $product->id) }}" class="btn btn-danger form-control"
                           onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        @endcan
                    </div>
                    <div class="col-2">
                        @auth
                            <form action="{{ route('purchases.store', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary form-control"
                                    {{ $product->stock < 1 ? 'disabled' : '' }}>
                                    {{ $product->stock < 1 ? 'Out of Stock' : 'Buy' }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary form-control">Login to Buy</a>
                        @endauth
                    </div>
                </div>

                <table class="table table-striped">
                    <tr><th width="20%">Name</th><td>{{ $product->name }}</td></tr>
                    <tr><th>Model</th><td>{{ $product->model }}</td></tr>
                    <tr><th>Code</th><td>{{ $product->code }}</td></tr>
                    <tr><th>Price</th><td>${{ number_format($product->price, 2) }}</td></tr>
                    <tr>
                        <th>Stock</th>
                        <td>
                            {{ $product->stock }} available
                            @can('edit_products')
                            <button type="button" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#editStockModal{{ $product->id }}">
                                Edit
                            </button>

                            <!-- Modal for Editing Stock -->
                            <div class="modal fade" id="editStockModal{{ $product->id }}" tabindex="-1" aria-labelledby="editStockModalLabel{{ $product->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editStockModalLabel{{ $product->id }}">Edit Stock for {{ $product->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('products.update_stock', $product->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="stock{{ $product->id }}">New Stock</label>
                                                    <input type="number" name="stock" id="stock{{ $product->id }}" class="form-control" value="{{ $product->stock }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    <tr><th>Description</th><td>{{ $product->description }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
