<x-admin-layout title="Booking">
    @section('css')
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/datatables.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/datatable-extension.css')}}">
    @endsection
    @section('breadcrumb-title')
    <h3>Booking</h3>
    @endsection

    @section('breadcrumb-items')
        <li class="breadcrumb-item">Booking</li>
        <li class="breadcrumb-item active">Booking Data</li>
    @endsection
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h3>Booking</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive user-datatable">
                            <table class="display" id="export-button">
                                <thead>
                                    <tr class="text-center">
                                        <th>Username</th>
                                        <th>Service Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Booking Code</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($mergedBookings))
                                        @foreach ($mergedBookings as $item)
                                            <tr>
                                                <td>{{ $item['user_name'] }}</td>
                                                <td>{{ $item['service_name'] }}</td>
                                                <td>{{ $item['start_booking_date'] }}</td>
                                                <td>{{ $item['end_booking_date'] }}</td>
                                                <td>{{ $item['booking_code'] }}</td>
                                                <td>{{ $item['payment_method'] }}</td>
                                                <td>{{ $item['status'] }}</td>
                                                <td>
                                                    <div class="dropdown-basic me-0">
                                                        <div class="btn-group dropstart">
                                                            <a class="dropdown-toggle btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                                            <ul class="dropdown-menu dropdown-block">
                                                                @if ($item['status'] == 'Accepted')
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Completed']) }}" onclick="event.preventDefault(); document.getElementById('complete-booking-form-{{ $item['id'] }}').submit();">Complete</a>
                                                                    <form id="complete-booking-form-{{ $item['id'] }}" action="{{ route('admin.booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Completed']) }}" method="POST" style="display: none;">
                                                                        @method('PUT')
                                                                        @csrf
                                                                    </form>
                                                                </li>
                                                                @endif
                                                                @if ($item['status'] == 'Pending')
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Accepted']) }}" onclick="event.preventDefault(); document.getElementById('accept-booking-form-{{ $item['id'] }}').submit();">Accept</a>
                                                                    <form id="accept-booking-form-{{ $item['id'] }}" action="{{ route('admin.booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Accepted']) }}" method="POST" style="display: none;">
                                                                        @method('PUT')
                                                                        @csrf
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Rejected']) }}" onclick="event.preventDefault(); document.getElementById('reject-booking-form-{{ $item['id'] }}').submit();">Reject</a>
                                                                    <form id="reject-booking-form-{{ $item['id'] }}" action="{{ route('admin.booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Rejected']) }}" method="POST" style="display: none;">
                                                                        @method('PUT')
                                                                        @csrf
                                                                    </form>
                                                                </li>
                                                                @endif
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.booking.destroy', $item['id']) }}" onclick="event.preventDefault(); document.getElementById('delete-booking-form-{{ $item['id'] }}').submit();">Delete</a>
                                                                    <form id="delete-booking-form-{{ $item['id'] }}" action="{{ route('admin.booking.destroy',  $item['id']) }}" method="POST" style="display: none;">
                                                                        @method('delete')
                                                                        @csrf
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Username</th>
                                        <th>Service Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Booking Code</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
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
    <script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/jszip.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/buttons.colVis.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/pdfmake.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/vfs_fonts.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/datatable/datatable-extension/custom.js')}}"></script>
@endsection
</x-admin-layout>
