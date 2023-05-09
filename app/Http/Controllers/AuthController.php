<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => [
            'login', 'register'
        ]]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|min:5|max:100',
                'password' => 'required'
            ]);
            $params = $validator->validate();

            $credential = [
                'username' => $params['username'],
                'password' => $params['password']
            ];

            $token = auth()->attempt($credential);
            if (!$token) {
                throw new \Exception("failed to login, please check your email or password again.", 400);
            }
            
            $user = Auth::guard()->user();
          
            $claim = [
                'username' => $user->username,
                'email' => $user->email,
                'id' => $user->id,
            ];
            
            $token = Auth::claims($claim)->login($user);

            $data = [
                'token' => $token,
                'user' => $user
            ];

            return response()->api($data, 200);
            
        } catch (ValidationException $e) {
            $response = [
                'errors' => [
                    'code' => 422,
                    'message' => $e->errors(),
                ]
            ];
            return response()->api($response, 422);
        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
       
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username'  => 'required|min:5|max:20|unique:users,username',
                'password'  => 'required|min:5',
                'email'     => 'required|string|email:rfc,dns|unique:users,email'
            ]);

            $params = $validator->validate();

            $params['password'] = Hash::make($params['password']);
            
            $results = User::create($params);

            return response()->api($results, 200);

        } catch (ValidationException $e) {
            $response = [
                'errors' => [
                    'code' => 422,
                    'message' => $e->errors(),
                ]
            ];
            return response()->api($response, 422);
        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }
}
