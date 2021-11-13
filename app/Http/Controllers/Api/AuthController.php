<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginUser;
use App\Http\Requests\Api\RegisterUser;
use App\RealWorld\Transformers\UserTransformer;
use App\Services\AuthService;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends ApiController
{
    /**
     * AuthController constructor.
     *
     * @param  UserTransformer  $transformer
     */
    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Login user and return the user if successful.
     *
     * @param  LoginUser  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUser $request)
    {
        $credentials = $request->only('user.email', 'user.password');
        $credentials = $credentials['user'];

        if (!Auth::once($credentials)) {
            return $this->respondFailedLogin();
        }

        return $this->respondWithTransformer(auth()->user());
    }

    /**
     * Register a new user and return the user if successful.
     *
     * @param  RegisterUser  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUser $request)
    {
        DB::beginTransaction();
        try {
            $user = (new AuthService())->register([
                'username' => $request->input('user.username'),
                'email' => $request->input('user.email'),
                'password' => bcrypt($request->input('user.password')),
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error("Error on registering user with this error :".$exception->getMessage());
            return $this->respondInternalError();
        }

        return $this->respondWithTransformer($user);
    }
}
