<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class ServiceController extends Controller
{
    public function index()
    {
        $url = "http://localhost:7001/services";

        try {
            $response = Http::get($url);
            $services = $response->json();

            // Pastikan respons API mengembalikan data role
            if ($services) {
                return view('pages.web.service.main', compact('services'));
            }

            return null;
        } catch (\Exception $e) {
            return view('pages.web.service.main');
        }
    }


    public function show(Booking $booking)
    {

    }

}
