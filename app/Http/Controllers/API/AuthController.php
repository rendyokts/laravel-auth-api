<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Tambahkan nama tabel dan kolom
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        // Enkripsi password
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        // Membuat pengguna baru
        $user = User::create($input);

        // Membuat token
        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;

        // Mengembalikan respons berhasil
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar',
            'data' => $success
        ]);
    }

    public function login(request $request){
        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('auth_token')->plainTextToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            $success['created_at'] = Carbon::parse($user->created_at)->format('d-m-Y H:i:s');
            return response()->json([
                'success' => true,
                'message' => 'Berhasil login',
                'data' => $success
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email/Password salah'
            ], 401);
        }
    }
}
