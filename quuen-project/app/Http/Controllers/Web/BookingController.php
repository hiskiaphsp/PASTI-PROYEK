<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class BookingController extends Controller
{

    public function index()
    {
        $urlBookings = 'http://localhost:7007/bookings';
        $urlUsers = 'http://localhost:7000/users';
        $urlServices = 'http://localhost:7001/services';

        try {
            $client = new Client();

            // Mengambil data pengguna yang login
            $loggedInUserId = session('user')['id'];

            // Mengambil data booking
            $responseBookings = $client->get($urlBookings);
            $bookings = json_decode($responseBookings->getBody(), true);

            // Mengambil data user untuk mapping
            $responseUsers = $client->get($urlUsers . '/' . $loggedInUserId);
            $users = json_decode($responseUsers->getBody(), true);

            // Mengambil data service untuk mapping
            $responseServices = $client->get($urlServices);
            $services = json_decode($responseServices->getBody(), true);

            // Menggabungkan data booking, user, dan service
            $mergedBookings = [];

            foreach ($bookings as $booking) {
                $userId = $booking['user_id'];
                $serviceId = $booking['service_id'];

                // Hanya tambahkan data booking jika ID pengguna sesuai dengan ID pengguna yang login
                if ($userId === $loggedInUserId) {
                    // Ambil data user berdasarkan user_id
                    $responseUser = $client->get($urlUsers . '/' . $userId);
                    $user = json_decode($responseUser->getBody(), true);

                    // Ambil data service berdasarkan service_id
                    $responseService = $client->get($urlServices . '/' . $serviceId);
                    $service = json_decode($responseService->getBody(), true);

                    $mergedBooking = array_merge($booking, [
                        'user_name' => $user['name'] ?? '',
                        'service_name' => $service['service_name'] ?? '',
                    ]);

                    $mergedBookings[] = $mergedBooking;
                }
            }

            // Pastikan respons API mengembalikan data booking
            if ($mergedBookings) {
                return view('pages.web.booking.main', compact('mergedBookings'));
            }
            if (!$mergedBookings) {
                return view('pages.web.booking.main');
            }

            return view('pages.web.booking.main')->with('mergedBookings', $mergedBookings);
        } catch (\Exception $e) {
            // Tangani kesalahan ketika URL tidak tersedia
            return view('pages.web.booking.main')->with('error', 'Server is under maintenance!');
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $url = 'http://localhost:7001/services';

        try {
            $client = new Client();
            $response = $client->get($url);
            $services = json_decode($response->getBody(), true);

            // Pastikan respons API mengembalikan data services
            if ($services) {
                return view('pages.web.booking.create', compact('services'));
            }

            return null;
        } catch (RequestException $e) {
            // Tangani kesalahan permintaan
            if ($e->getResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = $e->getResponse()->getBody()->getContents();
            } else {
                $statusCode = 500;
                $errorMessage = 'Error occurred: ' . $e->getMessage();
            }

            // Tampilkan pesan kesalahan
            return view('pages.web.home.main')->with('error', $errorMessage);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'user_id' => '',
            'service_id' => 'required',
            'phone_number' => 'required',
            'start_booking_date' => 'required',
            'end_booking_date' => 'required',
            'payment_method' => '',
            'status' => '',
            'booking_description' => '',
        ]);

        // Inisialisasi klien GuzzleHTTP
        $client = new Client();
        $validatedData['user_id'] = session('user')['id'];
        $validatedData['payment_method'] = "Cash";
        $validatedData['status'] = "Pending";
        $validatedData['start_booking_date'] = Carbon::parse($validatedData['start_booking_date'])->timestamp;
        $validatedData['end_booking_date'] = Carbon::parse($validatedData['end_booking_date'])->timestamp;

        try {
            // Kirim permintaan ke API create booking
            $response = $client->post('http://localhost:7007/bookings', [
                'json' => $validatedData,
            ]);
            // Periksa status kode respons
            if ($response->getStatusCode() == 201 || $response->getStatusCode() == 204) {
                // Alihkan pengguna ke halaman "index"
                return redirect()->route('booking.index')->with('success', 'Successfully make booking!');
            } else {
                return response()->json([
                    'message' => 'Failed to create booking',
                ], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $url = 'http://localhost:7007/bookings/' . $id;
        $urlService = 'http://localhost:7001/services';
        try {
            $client = new Client();
            $response = $client->get($url);
            $booking = json_decode($response->getBody(), true);
            $response = $client->get($urlService);
            $services = json_decode($response->getBody(), true);
            // Pastikan respons API mengembalikan data booking
            if ($booking) {
                return view('pages.web.booking.edit', compact('booking', 'services'));
            }

            return null;
        } catch (RequestException $e) {
            // Tangani kesalahan permintaan
            if ($e->getResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = $e->getResponse()->getBody()->getContents();
            } else {
                $statusCode = 500;
                $errorMessage = 'Error occurred: ' . $e->getMessage();
            }

            // Tampilkan pesan kesalahan
            return view('pages.web.home.main')->with('error', $errorMessage);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */

    public function updateStatus(Request $request, $id, $status)
    {
        $client = new Client();

        try {
            $response = $client->put('http://localhost:7007/bookings/' . $id . '/status', [
                'json' => [
                    'status' => $status,
                ],
            ]);
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 204) {
                return back()->with('success', 'Successfully cancel booking!');
            } else {
                return back()->with('error', 'Something went wrong!');

            }
        } catch (RequestException $e) {
            if ($e->getResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = $e->getResponse()->getBody()->getContents();
            } else {
                $statusCode = 500;
                $errorMessage = 'Error occurred: ' . $e->getMessage();
            }

            return view('pages.web.home.main')->with('error', $errorMessage);
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'service_id' => 'required',
            'phone_number' => 'required',
            'start_booking_date' => 'required',
            'end_booking_date' => 'required',
            'booking_description' => '',
        ]);

        // Inisialisasi klien GuzzleHTTP
        $client = new Client();
        $url = 'http://localhost:7007/bookings/' . $id;
        $validatedData['user_id'] = session('user')['id'];
        $validatedData['payment_method'] = "Cash";
        $validatedData['status'] = "Pending";
        $validatedData['start_booking_date'] = Carbon::parse($validatedData['start_booking_date'])->timestamp;
        $validatedData['end_booking_date'] = Carbon::parse($validatedData['end_booking_date'])->timestamp;
        try {
            // Kirim permintaan ke API update booking
            $response = $client->put($url, [
                'json' => $validatedData,
            ]);

            // Periksa status kode respons
            if ($response->getStatusCode() === 200) {
                return redirect()->route('booking.index')->with('success', 'Successfully update booking!');
            } else {
                // Handle error response
            }
        } catch (RequestException $e) {
            // Handle request exception
            if ($e->getResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = $e->getResponse()->getBody()->getContents();
            } else {
                $statusCode = 500;
                $errorMessage = 'Error occurred: ' . $e->getMessage();
            }

            // Tampilkan pesan kesalahan
            return view('pages.web.home.main')->with('error', $errorMessage);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }

    public function cancel_booking($id)
    {
        $booking = Booking::find($id);
        $booking->status = 'Cancelled';
        $booking->save();

        return redirect()->route('booking.index')->with('success', 'Your bookings have been cancelled');
    }
}
