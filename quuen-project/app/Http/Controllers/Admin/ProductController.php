<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Make a GET request to the API to fetch the products data
            $response = $client->get('http://localhost:7002/products');

            // Get the response body
            $data = json_decode($response->getBody(), true);

            // Pass the products data to the view
            return view('pages.admin.product.main', ['products' => $data]);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'product_name' => 'required',
            'sku' => 'required',
            'product_image' => 'required',
            'product_description' => '',
            'product_price' => 'required|numeric',
            'product_stock' => 'required|numeric',
        ]);

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Generate a unique file name
            $tujuanFile = 'images/products';
            $image->move($tujuanFile, $imageName);
            $validatedData['product_image'] = $imageName;
        }


        // Create a new Guzzle client
        $client = new Client();

        // Make a POST request to the API
        $validatedData['product_stock'] = intval($validatedData['product_stock']);
        $validatedData['product_price'] = floatval($validatedData['product_price']);

        $response = $client->post('http://localhost:7002/products', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $validatedData,
        ]);

        // Get the response body
        $data = json_decode($response->getBody(), true);

        // Redirect to a route with a success message
        return redirect()->route('admin.product.index')->with('success', 'Product created successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Make a GET request to the API to fetch the product data
            $response = $client->get('http://localhost:7002/products/'.$id);

            // Get the response body
            $data = json_decode($response->getBody(), true);

            // Pass the product data to the view
            return view('pages.admin.product.update', ['product' => $data]);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'product_name' => 'required',
            'sku' => 'required',
            'product_description' => '',
            'product_price' => 'required|numeric',
            'product_stock' => 'required|numeric',
        ]);

        $client = new Client();
        $response = $client->get('http://localhost:7002/products/' . $id);
        $existingProduct = json_decode($response->getBody(), true);

        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Generate a unique file name
            $tujuanFile = 'images/products';
            $file_path = public_path('images/products/'.$existingProduct['product_image']);
            if(file_exists($file_path)){
                unlink($file_path);

            }
            $image->move($tujuanFile, $imageName);
            $validatedData['product_image'] = $imageName;
        }
        $validatedData['product_stock'] = intval($validatedData['product_stock']);
        $validatedData['product_price'] = floatval($validatedData['product_price']);
        // Make a PUT request to the API
        $response = $client->put('http://localhost:7002/products/'.$id, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $validatedData,
        ]);

        // Redirect to a route with a success message
        return redirect()->route('admin.product.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Get the existing product data
            $response = $client->get('http://localhost:7002/products/' . $id);
            $existingProduct = json_decode($response->getBody(), true);

            // Check if the product exists
            if (!$existingProduct) {
                return redirect()->route('admin.product.index')->with('error', 'Product not found');
            }

            // Get the file path of the product image
            $file_path = public_path('images/products/'.$existingProduct['product_image']);

            // Check if the file exists and delete it
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Make a DELETE request to the API
            $response = $client->delete('http://localhost:7002/products/' . $id);

            // Check if the deletion was successful
            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 204) {
                // Deletion successful, handle the response accordingly
                return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully');
            } else {
                // Deletion failed, handle the response accordingly
                return redirect()->route('admin.product.index')->with('error', 'Failed to delete product');
            }
        } catch (ClientException $e) {
            // API request failed, handle the exception
            return redirect()->route('admin.product.index')->with('error', 'API request failed: ' . $e->getMessage());
        }
    }
}
