<x-web-layout title="My Booking">
    <section class="cart-area pb-80">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div>
                        <div class="d-flex justify-content-end mt-10 mb-10">
                            <div class="">
                                <a class="tp-btn tp-color-btn banner-animation" href="{{ url('booking/create') }}" name="update_cart">Make Booking</a>
                            </div>
                        </div>
                        <div class="table-content table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Service Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Booking Code</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($mergedBookings))
                                        @foreach ($mergedBookings as $item)
                                            @php
                                                $startBookingDate = \Carbon\Carbon::createFromTimestamp($item['start_booking_date']);
                                                $endBookingDate = \Carbon\Carbon::createFromTimestamp($item['end_booking_date']);
                                            @endphp
                                            <tr>
                                                <td>{{ $item['user_name'] }}</td>
                                                <td>{{ $item['service_name'] }}</td>
                                                <td>{{ $startBookingDate->format('m/d/Y h:i A') }}</td>
                                                <td>{{ $endBookingDate->format('m/d/Y h:i A') }}</td>
                                                <td>{{ $item['booking_code'] }}</td>
                                                <td>{{ $item['status'] }}</td>
                                                <td>
                                                    @if ($item['status'] != 'Cancelled')
                                                        <div class="dropdown">
                                                            <a class="dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="icon-settings"></i>
                                                            </a>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                                <li><a class="dropdown-item" href="{{ route('booking.edit', $item['id']) }}">Edit</a></li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Canceled']) }}" onclick="event.preventDefault(); document.getElementById('update-booking-form-{{ $item['id'] }}').submit();">Cancel</a>
                                                                    <form id="update-booking-form-{{ $item['id'] }}" action="{{ route('booking.updateBookingStatus', ['id' => $item['id'], 'status' => 'Canceled']) }}" method="POST" style="display: none;">
                                                                        @method('PUT')
                                                                        @csrf
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @else
                                                        No Action
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                        <div class="mt-10">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-web-layout>
