<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{

    public function showLogin()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        // Validasi data masukan
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Membuat instansiasi Guzzle client
        $client = new Client();

        // Mengirim permintaan POST ke API login
        try {
            $response = $client->post('http://localhost:7000/login', [
                'json' => $validatedData,
            ]);

            // Mendapatkan data respons JSON
            $data = json_decode($response->getBody(), true);

            // Mendapatkan token dari respons
            $token = $data['token'];
            $user = $data['user'];

            // Mengambil role ID pengguna
            $roleId = $this->getUserRoleByUserID($user['id']);

            // Mengatur session role
            if ($roleId) {
                $role = $this->getRoleById($roleId);
                session(['role' => $role ? $role['name'] : 'user']);
            } else {
                session(['role' => 'user']);
            }

            // Membuat cookie dengan nama 'auth_token' dan nilai token
            $cookie = Cookie::make('auth_token', $token, 60);
            session()->put('auth_token', $token);
            session()->put('user', $user);

            // Menyimpan cookie dalam respons
            if (session('role') == 'admin') {
                return redirect()->route('admin.dashboard')->withCookie($cookie)->with('success', 'Successfully Login!');
            }

            return redirect()->route('home')->withCookie($cookie)->with('success', 'Successfully Login!');
        } catch (\Exception $e) {
            // Penanganan kesalahan saat mengonsumsi API
            // Misalnya, menampilkan pesan kesalahan atau mengembalikan ke halaman login dengan pesan kesalahan
            return redirect()->route('auth.index')->with('error', 'Failed to login');
        }
    }


    private function getUserRoleByUserID($userID)
    {
        $url = "http://localhost:7005/user-role/{$userID}";

        try {
            $response = Http::get($url);
            $userRole = $response->json();

            // Pastikan respons API mengembalikan data user role
            if (!empty($userRole) && isset($userRole['role_id'])) {
                return $userRole['role_id'];
            }

            return null;
        } catch (\Exception $e) {
            // Penanganan kesalahan saat mengonsumsi API
            // Misalnya, menampilkan pesan kesalahan atau melakukan penanganan lainnya sesuai kebutuhan aplikasi Anda
            return null;
        }
    }

    private function getRoleById($roleId)
    {
        $url = "http://localhost:7003/roles/{$roleId}";

        try {
            $response = Http::get($url);
            $role = $response->json();

            // Pastikan respons API mengembalikan data role
            if (!empty($role)) {
                return $role;
            }

            return null;
        } catch (\Exception $e) {
            // Penanganan kesalahan saat mengonsumsi API
            // Misalnya, menampilkan pesan kesalahan atau melakukan penanganan lainnya sesuai kebutuhan aplikasi Anda
            return null;
        }
    }

    public function logout()
    {
        $response = Http::get('http://localhost:7000/logout');

        if ($response->successful()) {
            // Logout successful
             session()->flush();
            return redirect()->route('home')->with('success', 'Logout successful');
        } else {
            // Logout failed
            return redirect()->back()->with('error', 'Logout failed');
        }
    }

    public function viewSession()
    {
        $sessionData = session()->all();

        return $sessionData;
    }
}
