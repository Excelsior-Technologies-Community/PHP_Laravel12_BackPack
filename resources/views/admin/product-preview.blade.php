@extends(backpack_view('blank'))

@section('content')

<a href="{{ route('product.export.csv') }}"
   class="btn btn-success btn-sm">

    <i class="la la-download"></i>

    Export CSV

</a>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3>{{ $product->name }}</h3>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    @if($product->image)

                    <img
                        src="{{ asset('storage/'.$product->image) }}"
                        class="img-fluid rounded">

                    @else

                    <img
                        src="https://via.placeholder.com/300x300?text=No+Image"
                        class="img-fluid rounded">

                    @endif

                </div>

                <div class="col-md-8">

                    <table class="table table-bordered">

                        <tr>

                            <th width="200">Product Name</th>

                            <td>{{ $product->name }}</td>

                        </tr>

                        <tr>

                            <th>Category</th>

                            <td>{{ $product->category->name }}</td>

                        </tr>

                        <tr>

                            <th>Description</th>

                            <td>{{ $product->description }}</td>

                        </tr>

                        <tr>

                            <th>Price</th>

                            <td>

                                ₹{{ number_format($product->price,2) }}

                            </td>

                        </tr>

                        <tr>

                            <th>Quantity</th>

                            <td>

                                {{ $product->quantity }}

                            </td>

                        </tr>

                        <tr>

                            <th>Restock Level</th>

                            <td>

                                {{ $product->restock_level }}

                            </td>

                        </tr>

                        <tr>

                            <th>Availability</th>

                            <td>

                                @if($product->is_available)

                                <span class="badge bg-success">

                                    Available

                                </span>

                                @else

                                <span class="badge bg-danger">

                                    Not Available

                                </span>

                                @endif

                            </td>

                        </tr>

                        <tr>

                            <th>Stock Status</th>

                            <td>

                                @if($product->quantity==0)

                                <span class="badge bg-danger">

                                    Out Of Stock

                                </span>

                                @elseif($product->quantity <= $product->restock_level)

                                    <span class="badge bg-warning text-dark">

                                        Low Stock

                                    </span>

                                    @else

                                    <span class="badge bg-success">

                                        In Stock

                                    </span>

                                    @endif

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection