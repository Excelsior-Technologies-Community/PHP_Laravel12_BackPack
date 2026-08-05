@extends(backpack_view('blank'))

@section('content')

<div class="container-fluid">

    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Products</h6>
                    <h2>{{ $stats['total_products'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Total Categories</h6>
                    <h2>{{ $stats['total_categories'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Available Products</h6>
                    <h2>{{ $stats['available_products'] }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Low Stock</h6>
                    <h2>{{ $stats['low_stock_products'] }}</h2>
                </div>
            </div>
        </div>

    </div>


   <div class="row mt-3">

    <!-- Total Inventory Value -->
    <div class="col-md-3 mb-3">

        <div class="card shadow-sm h-100 border-start border-success border-4">

            <div class="card-body">

                <small class="text-muted text-uppercase">
                    Total Inventory Value
                </small>

                <h3 class="mt-2 text-success fw-bold">
                    ₹{{ number_format($stats['total_inventory_value'],2) }}
                </h3>

            </div>

        </div>

    </div>

    <!-- Average Product Price -->
    <div class="col-md-3 mb-3">

        <div class="card shadow-sm h-100 border-start border-info border-4">

            <div class="card-body">

                <small class="text-muted text-uppercase">
                    Average Product Price
                </small>

                <h3 class="mt-2 text-info fw-bold">
                    ₹{{ number_format($stats['average_price'],2) }}
                </h3>

            </div>

        </div>

    </div>

    <!-- Highest Price Product -->
    <div class="col-md-3 mb-3">

        <div class="card shadow-sm h-100 border-start border-warning border-4">

            <div class="card-body">

                <small class="text-muted text-uppercase">
                    Highest Price Product
                </small>

                @if($highest_price_product)

                    <h5 class="mt-2 mb-1 fw-bold">
                        {{ $highest_price_product->name }}
                    </h5>

                    <h4 class="text-warning fw-bold">
                        ₹{{ number_format($highest_price_product->price,2) }}
                    </h4>

                @else

                    <p class="text-muted mt-3">
                        N/A
                    </p>

                @endif

            </div>

        </div>

    </div>

    <!-- Lowest Price Product -->
    <div class="col-md-3 mb-3">

        <div class="card shadow-sm h-100 border-start border-danger border-4">

            <div class="card-body">

                <small class="text-muted text-uppercase">
                    Lowest Price Product
                </small>

                @if($lowest_price_product)

                    <h5 class="mt-2 mb-1 fw-bold">
                        {{ $lowest_price_product->name }}
                    </h5>

                    <h4 class="text-danger fw-bold">
                        ₹{{ number_format($lowest_price_product->price,2) }}
                    </h4>

                @else

                    <p class="text-muted mt-3">
                        N/A
                    </p>

                @endif

            </div>

        </div>

    </div>

</div>

    <div class="row">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    <strong>Recent Products</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($recent_products as $product)

                            <tr>

                                <td>{{ $product->name }}</td>

                                <td>{{ $product->category->name ?? 'N/A' }}</td>

                                <td>₹{{ number_format($product->price,2) }}</td>

                                <td>

                                    @if($product->quantity > 10)

                                    <span class="badge bg-success">
                                        {{ $product->quantity }}
                                    </span>

                                    @elseif($product->quantity > 0)

                                    <span class="badge bg-warning">
                                        {{ $product->quantity }}
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
                                        {{ $product->quantity }}
                                    </span>

                                    @endif

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="4" class="text-center">
                                    No Products Found
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    <strong>Top Categories</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>
                                <th>Category</th>
                                <th>Products</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($top_categories as $category)

                            <tr>

                                <td>{{ $category->name }}</td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ $category->products_count }}
                                    </span>
                                </td>

                                <td>

                                    @if($category->is_active)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                    @endif

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="3" class="text-center">
                                    No Categories Found
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-4">

            <div class="card border-danger">

                <div class="card-body">

                    <h5>Out Of Stock</h5>

                    <h1 class="text-danger">
                        {{ $stats['out_of_stock'] }}
                    </h1>

                </div>

            </div>

        </div>

        <div class="col-md-8">

            <div class="card border-primary">

                <div class="card-body">

                    <h5>Total Inventory Value</h5>

                    <h2 class="text-primary">
                        ₹{{ number_format($stats['total_inventory_value'],2) }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header bg-warning text-dark">
                    <strong>Low Stock Products</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Restock Level</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($low_stock_products as $product)

                            <tr>

                                <td>{{ $product->name }}</td>

                                <td>{{ $product->category->name ?? 'N/A' }}</td>

                                <td>
                                    <span class="badge bg-warning">
                                        {{ $product->quantity }}
                                    </span>
                                </td>

                                <td>{{ $product->restock_level }}</td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="4" class="text-center">
                                    No Low Stock Products
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card">

                <div class="card-header bg-danger text-white">
                    <strong>Out Of Stock Products</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($out_of_stock_products as $product)

                            <tr>

                                <td>{{ $product->name }}</td>

                                <td>{{ $product->category->name ?? 'N/A' }}</td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="2" class="text-center">
                                    No Out Of Stock Products
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <!-- Top Inventory Value Products -->
    <div class="row mt-4">

        <div class="col-md-12">

            <div class="card">

                <div class="card-header bg-success text-white">
                    <strong>Top Inventory Value Products</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($valuable_products as $product)

                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>₹{{ number_format($product->price,2) }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td class="fw-bold text-success">
                                    ₹{{ number_format($product->price * $product->quantity,2) }}
                                </td>
                            </tr>

                            @empty

                            <tr>
                                <td colspan="5" class="text-center">
                                    No Products Found
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <!-- Category Analytics -->
    <div class="row mt-4">

        <div class="col-md-12">

            <div class="card">

                <div class="card-header bg-primary text-white">
                    <strong>Category Analytics</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>
                                <th>Category</th>
                                <th>Total Products</th>
                                <th>Total Quantity</th>
                                <th>Inventory Value</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($category_analytics as $category)

                            <tr>

                                <td>{{ $category->name }}</td>

                                <td>
                                    {{ $category->products_count }}
                                </td>

                                <td>
                                    {{ $category->stock_quantity }}
                                </td>

                                <td class="fw-bold text-success">
                                    ₹{{ number_format($category->inventory_value,2) }}
                                </td>

                                <td>

                                    @if($category->is_active)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                    @endif

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="5" class="text-center">
                                    No Categories Found
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection