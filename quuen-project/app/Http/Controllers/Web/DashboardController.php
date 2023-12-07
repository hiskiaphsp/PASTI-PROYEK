<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;


class DashboardController extends Controller
{
    public function index()
    {
        $url = "http://localhost:7002/products";

        try {
            $response = Http::get($url);
            $products = $response->json();

            // Pastikan respons API mengembalikan data role
            if ($products) {
                return view('pages.web.home.main', compact('products'));
            }

            return null;
        } catch (\Exception $e) {
            return view('pages.web.home.main');
        }
    }
}
