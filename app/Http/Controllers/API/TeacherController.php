<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = Teacher::all();

        return response()->json([
            'data' => $response
        ]);
    }


    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|min:6',
            'password' => 'required|min:6',
        ]);

        $credentials = [
            'email' => $validate['email'],
            'password' => $validate['password']
        ];

        //echo $credentials;

        if (!Auth::guard('teacher')->attempt($credentials)) {
            return response()->json([
                'data' => 'Invalid credential'
            ]);
        }

        $token = Auth::guard('teacher')->user()->createToken('TeacherToken')->accessToken;

        return response()->json([
            'data' => Auth::guard('teacher')->user(),
            'token' => $token
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate = $request->validate([
                'email' => 'required|min:6|unique:teachers,email',
                'password' => 'required|min:6|confirmed'
            ]);

            $teacher = Teacher::create([
                'email' => $validate['email'],
                'password' => bcrypt($validate['password']),
                'name' => substr($validate['email'], 0, strpos($validate['email'], '@')),
                'type' => 'teacher'
            ]);

            $token = $teacher->createToken('TeacherToken')->accessToken;

            return response()->json([
                'data' => $teacher,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($id != Auth::id()) {
            return 'Wrong id can not access';
        }

        $response = Teacher::find($id);

        $data = $response;

        if ($response == null) {
            $data = 'Not found';
        }

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if ($id != Auth::id()) {
                return 'Wrong id can not access';
            }

            $validate = $request->validate([
                'email' => 'required|min:6|unique:teachers,email',
                'password' => 'required|min:6|confirmed',
                'age' => 'nullable',
                'address' => 'nullable',
                'profile_url' => 'nullable'
            ]);

            $student = Teacher::findOrFail($id)->update([
                'email' => $validate['email'],
                'password' => bcrypt($validate['password']),
                'name' => substr($validate['email'], 0, strpos($validate['email'], '@')),
                'age' => empty($validate['age']) ? null : $validate['age'],
                'address' => empty($validate['address']) ? null : $validate['address'],
                'profile_url' => empty($validate['profile_url']) ? null : $validate['profile_url'],
                'type' => 'teacher'
            ]);

            $data = Teacher::findOrFail($id);

            if ($student == false) {
                $data = 'Not found';
            }

            return response()->json([
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if ($id != Auth::id()) {
                return 'Wrong id can not access';
            }

            Teacher::findOrFail($id)->delete();

            return response()->json([
                'data' => 'Delete success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage()
            ]);
        }
    }
}
