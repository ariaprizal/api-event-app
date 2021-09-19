<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

use function GuzzleHttp\Promise\all;

class AuthController extends Controller
{


    /**
     * Login
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'password' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Wrong email or password',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('token')->plainTextToken;
            $respone = [
                'status' => Response::HTTP_CREATED,
                'message' => "login success",
                'data' => $user,
                'token' => $token
            ];

            return response()->json($respone, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errorInfo
            ]);
        }
    }

    /**
     * Create new user account
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $users = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required'],
            'password' => ['required', ' confirmed'],
            'phone' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $users['password'] = bcrypt($request->password);
        try {
            $users = User::create($users);
            $token = $users->createToken('token')->plainTextToken;
            $respone = [
                'status' => Response::HTTP_CREATED,
                'message' => "users was created",
                'data' => $users,
                'token' => $token
            ];

            return response()->json($respone, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $e->errorInfo
            ]);
        }
    }

    /**
     * Logged Out
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'logged out as '. $request->user()->name
        ]);

    }
}
