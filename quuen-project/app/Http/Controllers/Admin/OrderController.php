<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Notification;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Make a GET request to the API to fetch the products data
            $response = $client->get('http://localhost:7008/orders');

            // Get the response body
            $data = json_decode($response->getBody(), true);

            // Pass the products data to the view
            return view('pages.admin.order.main', ['orders' => $data]);
        } catch (ConnectException $e) {
            $statusCode = 500;
            $errorMessage = 'Connection error occurred: ' . $e->getMessage();

            // Display error message
            return redirect()->route('admin.dashboard')->with('error', $errorMessage);
        } catch (\Exception $e) {
            $statusCode = 500;
            $errorMessage = 'Error occurred: ' . $e->getMessage();

            // Display error message
            return redirect()->route('admin.dashboard')->with('error', $errorMessage);
        }
    }

    public function accept_order($id)
    {
        $order = Order::findOrFail($id);
        $userID = Order::where('order_number', $order->order_number)->first();
        $user = User::findOrFail($userID->user_id);
        if($order->payment_method == 'Transfer'){
            $order->order_status = 'Unpaid';
            $order->save();
            $notification = new Notification;
            $notification->user_id = $userID->user_id;
            $notification->message = $user->name.' order has been accepted';
            $notification->type = 'success';
            $notification->order_number = $order->order_number;
            $notification->save();
        }
        if ($order->payment_method == "Cash") {
            $order->order_status = 'Accepted';
            $order->save();
            $notification = new Notification;
            $notification->user_id = $userID->user_id;
            $notification->message = $user->name.' order has been accepted ' . $order->order_number;
            $notification->type = 'success';
            $notification->order_number = $order->order_number;
            $notification->save();
        }
        return redirect()->route('admin.order.index')->with('success','Successfully updated status order');
    }

    public function reject_order($id)
    {
        $user = User::findOrFail($userID->user_id);
        $userID = Order::where('order_number', $order->order_number)->first();
        $user = User::findOrFail($userID->user_id);
        $order->order_status = 'Rejected';
        $order->save();
        $notification = new Notification;
        $notification->user_id = $userID->user_id;
        $notification->message = $user->name.' order has been rejected';
        $notification->type = 'success';
        $notification->order_number = $order->order_number;
        $notification->save();
        return redirect()->route('admin.order.index')->with('success','Successfully updated status order');
    }

    public function delete($id)
    {
        $order = Order::find($id);
        $order->delete();

        return redirect()->route('admin.order.index')->with('success','Successfully deleted order');
    }

    public function create()
    {
        $product = Product::all();
        return view('pages.admin.order.create', compact('product'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' =>'required|array',
            'product_id.*' => 'exists:product,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
        ]);

        $productIds = $validatedData['product_id'];
        $quantities = $validatedData['quantity'];

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // set karakter yang digunakan
        $order_number = 'QS' . substr(str_shuffle($characters), 0, 10);

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->order_status = 'Accepted';
        $order->payment_method = 'Cash';
        $order->order_number = $order_number;
        $order->order_amount = $request->order_amount;
        $order->save();

        foreach ($productIds as $index => $productId) {
            $product = Product::findOrFail($productId);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->quantity = $quantities[$index];
            $orderItem->order_number = $order_number;
            // $orderItem->total_price = $product->price * $quantities[$index];
            $orderItem->save();
        }

        return redirect()->route('admin.order.index')->with('success', 'Order created successfully');
    }

    public function complete_order($id)
    {

        $order = Order::find($id);
        $userID = Order::where('order_number', $order->order_number)->first();
        $user = User::findOrFail($userID->user_id);
        $order->order_status = 'Completed';

        // Mengurangi stok produk
        foreach ($order->orderItems as $orderItem) {
            $product = Product::findOrFail($orderItem->product_id);
            $product->product_stock -= $orderItem->quantity;
            $product->save();
        }

        $notification = new Notification;
        $notification->user_id = $userID->user_id;
        $notification->message = $user->name.' order has been completed';
        $notification->type = 'success';
        $notification->order_number = $order->order_number;
        $notification->save();
        $order->save();
        return redirect()->route('admin.order.index')->with('success','Successfully updated status order');
    }

}
