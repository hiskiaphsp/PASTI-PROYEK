<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VSE;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $url = "http://localhost:7002/products";

        try {
            $response = Http::get($url);
            $products = $response->json();

            // Pastikan respons API mengembalikan data role
            if ($products) {
                return view('pages.web.product.main', compact('products'));
            }

            return null;
        } catch (\Exception $e) {
            return view('pages.web.product.main');
        }
    }

    public function loadCart()
    {
        $userId = session('user')['id'];
        $cart = session('cart.' . $userId, []);
        $total = 0;

        $cartDetails = View::make('pages.web.home.cart-loader', compact('cart'))->render();

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $cartSubtotal = "Rp. " . number_format($total, 2, ',', '.');

        $response = [
            'cart_details' => $cartDetails,
            'cart_subtotal' => $cartSubtotal
        ];

        return response()->json($response);
    }

    public function show($id)
    {
        $url = "http://localhost:7002/products/". $id;

        try {
            $client = new Client();
            $response = $client->get($url);
            $product = json_decode($response->getBody(), true);

            // Make sure the API response returns the product data
            if ($product) {
                return view('pages.web.product.show', compact('product'));
            }

            return null;
        } catch (\Exception $e) {
            return view('pages.web.product.main');
        }
    }

    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $userId = session('user')['id'];

        $response = Http::get('http://localhost:7002/products/'.$productId);

        if ($response->failed()) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product = $response->json();

        $cart = session()->get('cart.'.$userId, []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product['id'],
                'name' => $product['product_name'],
                'price' => $product['product_price'],
                'quantity' => $quantity,
                'image' => $product['product_image'],
                'user_id' => $userId,
            ];
        }

        session()->put('cart.'.$userId, $cart);

        return response()->json(['message' => 'Product added to cart.'], 200);
    }
    public function removeFromCart(Request $request)
    {
        $productId = $request->input('product_id');
        $userId = session('user')['id'];

        $cart = session()->get('cart.'.$userId, []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart.'.$userId, $cart);

            return back()->with('success', 'Product removed from cart');
        }

        return back()->with('success', 'Product removed from cart');
    }
    public function checkout_product(Request $request)
    {
        // Ambil data dari keranjang belanja
        $userId = Auth::id();
        $cart = session()->get('cart.'.$userId, []);

        // Buat data order baru
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // set karakter yang digunakan
        $order_number = 'QS' . substr(str_shuffle($characters), 0, 14);

        $order = new Order();
        $order->user_id = $userId;
        $order->order_status = 'Pending'; // bisa diganti dengan status yang sesuai
        $order->order_amount = 0;
        $order->order_number = $order_number;
        $order->save();

        $totalPrice = 0;

        // Looping untuk membuat data order_item dari data cart
        foreach ($cart as $cartItem) {
            $product = Product::find($cartItem['id']);

            if (!$product) {
                // jika produk tidak ditemukan, skip ke produk berikutnya
                continue;
            }

            // Buat data order_item baru
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->quantity = $cartItem['quantity'];
            $orderItem->order_number = $order->order_number;
            $orderItem->save();

            // Hitung total harga order
            $totalPrice += $product->product_price * $cartItem['quantity'];
        }

        // Update total harga order
        $order->order_amount = $totalPrice;
        $order->save();

        // Kosongkan keranjang belanja
        session()->forget('cart.'.$userId);

        return redirect()->route('order.index')->with('success', 'You successfully checkout the order');
    }

}


