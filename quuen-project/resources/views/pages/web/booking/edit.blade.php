<x-web-layout title="Update Booking">
    @section('css')
        <link id="color" rel="stylesheet" href="{{asset('assets/css/color-1.css')}}" media="screen">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/font-awesome.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/date-time-picker.css')}}">
    @endsection

    <main class="grey-bg">
        <div class="container">
            <section class="row justify-content-center">
                <div class="tpproduct col-lg-6 mt-60 mb-60">
                    <div class="tpform__wrapper ml-60 mt-60 mb-60 mr-60">
                        <h4 class="tpform__title">Update Booking</h4>
                        <div class="tpform__box">
                            <form action="{{route('booking.update', $booking['id'])}}" method="post" id="booking_form">
                                @csrf
                                @method('PUT')
                                <div class="row gx-7">
                                    <div class="col-lg-12">
                                        <label for="username" class="mx-2">Name<span class="text-danger">*</span></label>
                                        <div class="tpform__input">
                                            <input class="" type="text" name="username" value="{{session('user')['name']}}" id="username" placeholder="Your Name" disabled>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-20 mb-20">
                                        <label for="service_id" class="mx-2">Service<span class="text-danger">*</span></label>
                                        <div class="ml-2 tpform__select">
                                            <select name="service_id" class="@error('service_id') is-invalid @enderror" id="service_id">
                                                <option value="" selected disabled>Please choose service</option>
                                                @foreach ($services as $item)
                                                    <option value="{{$item['id']}}" {{ $booking['service_id'] == $item['id'] ? 'selected' : '' }}>{{$item['service_name']}}</option>
                                                @endforeach
                                            </select>
                                            @error('service_id')
                                                <div class="invalid-feedback">
                                                    {{$message}}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="phone_number" class="mx-2">Phone<span class="text-danger">*</span></label>
                                        <div class="tpform__input mb-20">
                                            <input class="@error('phone_number') is-invalid @enderror" type="number" placeholder="Phone" name="phone_number" id="phone_number" value="{{$booking['phone_number']}}">
                                            @error('phone_number')
                                                <div class="invalid-feedback">
                                                    {{$message}}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-20">
                                        @php
                                            $startBookingDate = \Carbon\Carbon::createFromTimestamp($booking['start_booking_date']);
                                            $endBookingDate = \Carbon\Carbon::createFromTimestamp($booking['end_booking_date']);
                                        @endphp

                                        <div class="tpform__input">
                                            <label for="start_booking_date" class="mx-2">Start Time<span class="text-danger">*</span></label>
                                            <div class="input-group date" id="dt-enab-disab-date" data-target-input="nearest">
                                                <input id="start_booking_date" class="@error('start_booking_date') is-invalid @enderror form-control datetimepicker-input digits" type="text" name="start_booking_date" data-target="#dt-enab-disab-date" value="{{ $startBookingDate->format('m/d/Y h:i A') }}">
                                                <div class="input-group-text" data-target="#dt-enab-disab-date" data-toggle="datetimepicker"><i class="fa fa-calendar"></i></div>
                                            </div>
                                            @error('start_booking_date')
                                                <div class="invalid-feedback">
                                                    {{$message}}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="tpform__input">
                                            <label for="end_booking_date" class="mx-2">End Time<span class="text-danger">*</span></label>
                                            <div class="input-group date" id="dt-enab-disab-date-end" data-target-input="nearest">
                                                <input class="@error('end_booking_date') is-invalid @enderror form-control datetimepicker-input digits" type="text" name="end_booking_date" id="end_booking_date" data-target="#dt-enab-disab-date-end" value="{{ $endBookingDate->format('m/d/Y h:i A') }}">
                                                <div class="input-group-text" data-target="#dt-enab-disab-date-end" data-toggle="datetimepicker"><i class="fa fa-calendar"></i></div>
                                            </div>
                                            @error('end_booking_date')
                                                <div class="invalid-feedback">
                                                    {{$message}}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="booking_description" class="mx-2">Description (optional)</label>
                                        <div class="tpform__textarea">
                                            <textarea name="booking_description" id="booking_description" placeholder="Description..." cols="30" rows="10">{{$booking['booking_description']}}</textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="mr-10 ml-10">
                                            <a class="btn btn-outline-danger" href="{{url('booking')}}">Cancel</a>
                                        </div>
                                        <div class="">
                                            <button type="submit" class="btn btn-success rounded">Update Booking</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    @section('script')
        <script src="{{asset('assets/js/datepicker/date-time-picker/moment.min.js')}}"></script>
        <script src="{{asset('assets/js/datepicker/date-time-picker/tempusdominus-bootstrap-4.min.js')}}"></script>
        <script src="{{asset('assets/js/datepicker/date-time-picker/datetimepicker.custom.js')}}"></script>
    @endsection
</x-web-layout>
