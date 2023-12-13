<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $input = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $input['email'],
            'password' => $input['password']
        ];

        if (!Auth::attempt($credentials)) {
            return redirect('login');
        }

        $token = auth()->user()->createToken('APIToken')->accessToken;

        return redirect('/')->withCookie('token', $token);
    }

    public function register(Request $request)
    {
        try {
            $input = $request->validate([
                'email' => 'required',
                'password' => 'required'
            ]);

            if (request()->has('photo')) {
                $filename = time() . request()->file('photo')->getClientOriginalName();
                $path = $request->file('photo')->storeAs('images', $filename, 's3');
                $photoPath = Storage::disk('s3')->url($path);
            }


            User::create([
                'email' => $input['email'],
                'password' => bcrypt($input['password']),
                'type' => $request->type,
                'name' => substr($input['email'], 0, strpos($input['email'], '@')),
                'photo' => request()->has('photo') ? $photoPath : ""
            ]);

            $credentials = [
                'email' => $input['email'],
                'password' => $input['password']
            ];

            if (!Auth::attempt($credentials)) {
                return redirect('login');
            }

            $token = auth()->user()->createToken('APIToken')->accessToken;

            return redirect('/')->withCookie('token', $token);

            //return redirect('login')->with('email', $user->email);
        } catch (Exception $e) {
            return redirect('/');
        }
    }

    public function logout()
    {
        auth()->logout();

        return redirect('/')->withCookie('token', 'null');
    }
}
