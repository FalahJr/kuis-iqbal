<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Services\FirebaseService;

class LoginController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        return view('pages.login');
    }

    public function login_action(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('login')->with('failed', 'Lengkapi isian form.');
        }

        // Ambil data user dari Firebase
        $users = $this->firebaseService->getReference('user')->getValue();

        if ($users) {
            foreach ($users as $user) {
                if ($user['email'] === $request->email && $user['password'] === $request->password) {
                    // Set session user
                    Session::put('user', [
                        'email' => $user['email'],
                        'role' => 'Admin', // Sesuaikan role jika ada
                        'nama' => $user['nama'] ?? 'Admin', // Default nama jika tidak ad
                    ]);

                    // dd($user);
                    return redirect('admin/home')->with('success', 'Login berhasil.');
                }
            }
        }

        return redirect('login')->with('failed', 'Email atau password salah.');
    }

    public function logout_action()
    {
        Session::flush();
        return redirect('/')->with('success', 'Berhasil logout.');
    }
}
