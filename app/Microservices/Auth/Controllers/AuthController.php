<?php

namespace App\Microservices\Auth\Controllers;

use App\Base\BaseController;
use App\Microservices\Auth\Services\AuthService;
use App\Constants\Message;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    /***
     * register
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @since: 2022/07/25 23:13
     */

    protected $service;

    public function __construct(Request $request, AuthService $service)
    {
        parent::__construct($request);
        $this->service = $service;
    }

    /***
     * register
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @since: 2022/07/25 23:13
     */
    public function register()
    {
        $this->validate($this->request, ['phone' => 'required', 'password' => 'required|min:6']);
        // $email = $this->request->input('email');
        $phone = $this->request->input('phone');
        if (!(str_starts_with($phone, '01') && strlen($phone) == 11 || ((str_starts_with($phone, '09') || str_starts_with($phone, '09')) && strlen($phone) == 10))) {
        }
        if ($this->request->input('password') != $this->request->input('confirm_password')) {
            return $this->falseResponse(__('confirm_password_not_match'));
        }
        try {
            $user = $this->service->doRegister([
                'password' => $this->request->input('password'),
                'phone' => $this->request->input('phone'),
                'brand_id' => $this->request->brand_id
            ]);
        } catch (\Exception $e) {
            return $this->falseResponse($e->getMessage());
        }


        return $this->successResponse($user);
    }

    /***
     * verifyEmail
     *
     * @param $token
     *
     * @return \Illuminate\Http\JsonResponse|void
     * @since: 2022/07/25 23:16
     */
    public function verifyEmail(string $token)
    {
        try {
            $ok = $this->service->doVerifyEmail($token);
            if ($ok) {
                return response()->json([
                    'success' => TRUE,
                    'message' => Message::MSG_SHOPBE_VERIFIED_SUCCESS,
                ]);
            }
            return response()->json([
                'success' => FALSE,
                'message' => Message::MSG_SHOPBE_VERIFIED_FAIL,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => FALSE,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /***
     * login
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @since: 2022/07/25 21:57
     */
    public function login()
    {
        $this->validate($this->request, [
            'phone' => 'required',
            'password' => 'required'
        ]);

        try {
            list($loginType, $token) = $this->service->doLogin($this->request->input('phone'), $this->request->input('password'));

            return $this->successResponse($this->respondWithToken($token));
        } catch (\Exception $e) {
            return $this->falseResponse($e->getMessage());
        }
    }

    /***
     * logout
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @since: 2022/08/02 21:57
     */
    public function logout()
    {
        try {
            $token = substr($this->request->header('Authorization'), 7);
            $this->service->doLogout($token);
        } catch (\Exception $e) {
            return response()->json([
                'success' => FALSE,
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => TRUE,
            'message' => "shopbe_user_logout_success"
        ]);
    }
    public function me()
    {
        return $this->successResponse(auth()->authenticate());
    }


    public function refresh()
    {
        $token = substr($this->request->header('Authorization'), 7);
        try {
            $token = $this->service->doRefresh();
            return $this->successResponse($this->respondWithToken($token));
        } catch (Exception $e) {
            return $this->falseResponse($e->getMessage() . 'Token expired, please login', 401);
        }
    }

    public function respondWithToken($token): JsonResponse
    {
        return response()->json(
            [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'=>$this->user(),
                'expires_in'   => auth()->factory()->getTTL() * 60
            ]
        );
    }

    public function test()
    {
        // dd(auth()->factory()->getTTL() * 60);
        return $this->successResponse(auth()->factory()->getTTL() * 60);
    }
    //
}
