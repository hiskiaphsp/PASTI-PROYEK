<x-admin-layout title="Product">
    @section('css')
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatable-extension.css') }}">
    @endsection
    @section('breadcrumb-title')
        <h3>Products</h3>
    @endsection

    @section('breadcrumb-items')
        <li class="breadcrumb-item">Products</li>
        <li class="breadcrumb-item active">Data Products</li>
    @endsection

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h3>Products</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <a class="btn btn-primary" href="{{ url('admin/product/create') }}">Add Product</a>
                        </div>
                        <div class="table-responsive user-datatable">
                            <table class="display" id="export-button">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Description</th>
                                        <th>Stock</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($products)
                                        @foreach ($products as $item)
                                            <tr>
                                                <td><img class="img-fluid table-avtar"
                                                        src="{{ asset('images/products/'.$item['product_image']) }}"
                                                        alt="{{$item['product_image']}}">{{ $item['product_name'] }}</td>
                                                <td>{!! \Illuminate\Support\Str::limit($item['product_description'], 20, '...') !!}</td>
                                                <td>{{ $item['product_stock'] }}</td>
                                                <td>{{ $item['sku'] }}</td>
                                                <td>{{ $item['product_price'] }}</td>
                                                <td>
                                                    <ul class="action">
                                                        <li class="edit">
                                                            <a href="{{ route('admin.product.edit', $item['id']) }}">
                                                                <i class="icon-pencil-alt"></i>
                                                            </a>
                                                        </li>
                                                        <li class="delete">
                                                            <a href="{{ route('admin.product.destroy', $item['id']) }}"
                                                                onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('delete-form-{{ $item['id'] }}').submit(); }">
                                                                <i class="icon-trash"></i>
                                                            </a>
                                                            <form id="delete-form-{{ $item['id'] }}"
                                                                action="{{ route('admin.product.destroy', $item['id']) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Description</th>
                                        <th>Stock</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('script')
        <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.colVis.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/vfs_fonts.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable-extension/custom.js') }}"></script>
    @endsection
</x-admin-layout>
