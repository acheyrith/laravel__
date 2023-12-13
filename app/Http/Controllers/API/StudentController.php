<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $response = Student::all();

            return response()->json([
                'data' => $response
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
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

        if (!Auth::guard('student')->attempt($credentials)) {
            return response()->json([
                'data' => 'Invalid credential'
            ]);
        }

        $token = Auth::guard('student')->user()->createToken('StudentToken')->accessToken;

        return response()->json([
            'data' => Auth::guard('student')->user(),
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
                'email' => 'required|min:6|unique:students,email',
                'password' => 'required|min:6|confirmed',
            ]);

            $student = Student::create([
                'email' => $validate['email'],
                'password' => bcrypt($validate['password']),
                'name' => substr($validate['email'], 0, strpos($validate['email'], '@')),
                'type' => 'student'
            ]);

            $token = $student->createToken('StudentToken')->accessToken;


            return response()->json([
                'data' => $student,
                'token' => $token
            ], 200);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        if ($id != Auth::id()) {
            return 'Wrong id can not access';
        }

        $response = Student::find($id);

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
    public function update(Request $request, int $id)
    {
        try {
            if ($id != Auth::id()) {
                return 'Wrong id can not access';
            }

            $validate = $request->validate([
                'email' => 'required|min:6|unique:students,email',
                'password' => 'required|min:6|confirmed',
                'age' => 'nullable',
                'address' => 'nullable',
                'profile_url' => 'nullable'
            ]);

            $student = Student::findOrFail($id)->update([
                'email' => $validate['email'],
                'password' => bcrypt($validate['password']),
                'name' => substr($validate['email'], 0, strpos($validate['email'], '@')),
                'age' => empty($validate['age']) ? null : $validate['age'],
                'address' => empty($validate['address']) ? null : $validate['address'],
                'profile_url' => empty($validate['profile_url']) ? null : $validate['profile_url'],
                'type' => 'student'
            ]);

            $data = Student::findOrFail($id);

            if ($student == false) {
                $data = 'Not found';
            }

            return response()->json([
                'data' => $data
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            if ($id != Auth::id()) {
                return 'Wrong id can not access';
            }

            Student::findOrFail($id)->delete();

            return response()->json([
                'data' => 'Delete success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => $e->getMessage()
            ]);
        }
    }
}
