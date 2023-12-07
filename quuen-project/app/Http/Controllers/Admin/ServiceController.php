<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

class ServiceController extends Controller
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

        // Make a GET request to the API to fetch the services data
        $response = $client->get('http://localhost:7001/services');

        // Get the response body
        $data = json_decode($response->getBody(), true);

        // Pass the services data to the view
        return view('pages.admin.service.main', ['services' => $data]);
    } catch (ConnectException $e) {
        $statusCode = 500;
        $errorMessage = 'Connection error occurred: ' . $e->getMessage();

        // Tampilkan pesan kesalahan
        return redirect()->route('admin.dashboard')->with('error', $errorMessage);
    } catch (\Exception $e) {
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = $e->getResponse()->getBody()->getContents();
        } else {
            $statusCode = 500;
            $errorMessage = 'Error occurred: ' . $e->getMessage();
        }

        // Tampilkan pesan kesalahan
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
        return view('pages.admin.service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'service_name' => 'required',
            'service_image' => 'required',
            'description' => '',
            'price' => 'required|numeric',
        ]);

        if ($request->hasFile('service_image')) {
            $image = $request->file('service_image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Generate a unique file name
            $tujuanFile = 'images/services';
            $image->move($tujuanFile, $imageName);
            $validatedData['service_image'] = $imageName;
        }

        // Create a new Guzzle client
        $client = new Client();

        // Make a POST request to the API
        $validatedData['price'] = floatval($validatedData['price']);

        $response = $client->post('http://localhost:7001/services', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $validatedData,
        ]);

        // Get the response body
        $data = json_decode($response->getBody(), true);

        // Redirect to a route with a success message
        return redirect()->route('admin.service.index')->with('success', 'Successfully add service');

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Make a GET request to the API to fetch the service data
            $response = $client->get('http://localhost:7001/services/'.$id);

            // Get the response body
            $data = json_decode($response->getBody(), true);

            // Pass the service data to the view
            return view('pages.admin.service.update', ['service' => $data]);
        } catch (ConnectException $e) {
            $statusCode = 500;
            $errorMessage = 'Connection error occurred: ' . $e->getMessage();

            // Tampilkan pesan kesalahan
            return redirect()->route('admin.dashboard')->with('error', $errorMessage);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = $e->getResponse()->getBody()->getContents();
            } else {
                $statusCode = 500;
                $errorMessage = 'Error occurred: ' . $e->getMessage();
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'service_name' => 'required',
            'description' => '',
            'price' => 'required|numeric',
        ]);

        $client = new Client();
        $response = $client->get('http://localhost:7001/services/' . $id);
        $existingservice = json_decode($response->getBody(), true);

        if ($request->hasFile('service_image')) {
            $image = $request->file('service_image');
            $imageName = time() . '_' . $image->getClientOriginalName(); // Generate a unique file name
            $tujuanFile = 'images/services';
            $file_path = public_path('images/services/'.$existingservice['service_image']);
            if(file_exists($file_path)){
                unlink($file_path);
            }
            $image->move($tujuanFile, $imageName);
            $validatedData['service_image'] = $imageName;
        }
        $validatedData['price'] = floatval($validatedData['price']);
        // Make a PUT request to the API
        $response = $client->put('http://localhost:7001/services/'.$id, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $validatedData,
        ]);

        // Redirect to a route with a success message
        return redirect()->route('admin.service.index')->with('success', 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Create a new Guzzle client
            $client = new Client();

            // Get the existing service data
            $response = $client->get('http://localhost:7001/services/' . $id);
            $existingservice = json_decode($response->getBody(), true);

            // Check if the service exists
            if (!$existingservice) {
                return redirect()->route('admin.service.index')->with('error', 'service not found');
            }

            // Get the file path of the service image
            $file_path = public_path('images/services/'.$existingservice['service_image']);

            // Check if the file exists and delete it
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            // Make a DELETE request to the API
            $response = $client->delete('http://localhost:7001/services/' . $id);

            // Check if the deletion was successful
            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 204) {
                // Deletion successful, handle the response accordingly
                return redirect()->route('admin.service.index')->with('success', 'service deleted successfully');
            } else {
                // Deletion failed, handle the response accordingly
                return redirect()->route('admin.service.index')->with('error', 'Failed to delete service');
            }
        } catch (ClientException $e) {
            // API request failed, handle the exception
            return redirect()->route('admin.service.index')->with('error', 'API request failed: ' . $e->getMessage());
        }
    }
}
