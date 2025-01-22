@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Products</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">All Products</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="" name="name"
                                tabindex="2" value="" aria-required="true" required="">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.product-create') }}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="table-responsive">
                @if (Session::has('status'))
                    <div class="alert alert-success lead">
                        {{ Session::get('status') }}
                    </div>
                @endif
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>SalePrice</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Featured</th>
                            <th>Stock</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->id  }}</td>
                            <td class="pname">
                                <div class="image">
                                    <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" alt="{{ $product->name }}" class="image">
                                </div>
                                <div class="name">
                                    <a href="#" class="body-title-2">{{ $product->name }}</a>
                                    <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                </div>
                            </td>
                            <td>{{ $product->regular_price }}</td>
                            <td>{{ $product->sale_price }}</td>
                            <td>{{ $product->SKU }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->brand->name }}</td>
                            <td>{{ $product->featured == 0 ? 'No' : 'Yes' }}</td>
                            <td>{{ $product->stock_status }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>
                                <div class="list-icon-function">
                                    <a href="#" target="_blank">
                                        <div class="item eye">
                                            <i class="icon-eye"></i>
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.product-edit', $product->id) }}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>
                                    <form action="{{ route('admin.product-destroy', $product->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="item text-danger delete">
                                            <i class="icon-trash-2"></i>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .swal-button {
            font-size: 16px;
            padding: 12px 30px;
            border-radius: 8px;
        }

        .swal-button--cancel {
            background-color: #6c757d;
            color: black;
        }

        .swal-button--cancel:hover {
            background-color: #5a6268;
            color: #000;
        }

        /* Confirm (Delete) button styles */
        .swal-button--danger {
            background-color: #dc3545;
            color: white;
        }

        .swal-button--danger:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            $('.delete').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                swal({
                    title: 'Are you sure?',
                    text: 'Once deleted, this action cannot be undone!',
                    icon: 'warning', // Icon type: warning
                    buttons: {
                        cancel: {
                            text: "Cancel", // Text for the cancel button
                            value: null,
                            visible: true,
                            className: "btn btn-secondary btn-lg", // Bootstrap secondary button with large size
                            closeModal: true
                        },
                        confirm: {
                            text: "Delete", // Text for the confirm button
                            value: true,
                            visible: true,
                            className: "btn btn-danger btn-lg", // Bootstrap danger button with large size
                            closeModal: false
                        }
                    },
                    dangerMode: true // Enable danger mode for better visuals
                }).then((willDelete) => {
                    if (willDelete) {
                        form.submit(); // Submit the form if confirmed
                        swal("Deleted!", "Your record has been deleted.", "success");
                    } else {
                        swal("Cancelled", "Your record is safe!", "info");
                    }
                });
            });
        });
    </script>
@endpush

