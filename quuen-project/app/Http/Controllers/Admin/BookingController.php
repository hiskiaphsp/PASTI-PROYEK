<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class BookingController extends Controller
{
    public function index()
    {
        $urlBookings = 'http://localhost:7007/bookings';
        $urlUsers = 'http://localhost:7000/users';
        $urlServices = 'http://localhost:7001/services';

        try {
            $client = new Client();

            // Mengambil data booking
            $responseBookings = $client->get($urlBookings);
            $bookings = json_decode($responseBookings->getBody(), true);

            // Mengambil data user untuk mapping
            $responseUsers = $client->get($urlUsers);
            $users = json_decode($responseUsers->getBody(), true);

            // Mengambil data service untuk mapping
            $responseServices = $client->get($urlServices);
            $services = json_decode($responseServices->getBody(), true);

            // Menggabungkan data booking, user, dan service
            $mergedBookings = [];

            foreach ($bookings as $booking) {
                $userId = $booking['user_id'];
                $serviceId = $booking['service_id'];

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

            // Pastikan respons API mengembalikan data booking
            if ($mergedBookings) {
                return view('pages.admin.booking.main', compact('mergedBookings'));
            }
            if(!$mergedBooking){
                // Data booking tidak ditemukan
                return view('pages.admin.booking.main');
            }
        } catch (\Exception $e) {
            // Tangani kesalahan ketika URL tidak tersedia
        return view('pages.admin.booking.main')->with('error', 'Server under maintenance');
        }
    }


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
                return back()->with('success', 'Booking '. $status .'!');
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

    public function accept_booking($id)
    {
        $booking = Booking::find($id);
        $booking->status = 'Accepted';
        $booking->save();

        return redirect()->route('admin.booking.index')->with('success','Successfully updated status booking');
    }

    public function reject_booking($id)
    {
        $booking = Booking::find($id);
        $booking->status = 'Rejected';
        $booking->save();

        return redirect()->route('admin.booking.index')->with('success','Successfully updated status booking');
    }
    public function destroy($id)
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Make a DELETE request to the API to delete the booking
            $response = $client->delete('http://localhost:7007/bookings/'.$id);

            // Check the response status code
            $statusCode = $response->getStatusCode();
            if ($statusCode == 204) {
                // Booking successfully deleted
                return redirect()->route('admin.booking.index')->with('success', 'Booking deleted successfully.');
            } else {
                // Failed to delete booking
                return redirect()->route('admin.booking.index')->with('error', 'Failed to delete booking. Please try again.');
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return redirect()->route('admin.booking.index')->with('error', 'An error occurred while deleting the booking. Please try again.');
        }
    }

}
