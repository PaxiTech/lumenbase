<?php

/**
 * Created by PhpStorm.
 * Date: 2022-08-01
 * Time: 22:17
 */

namespace App\Microservices\Auth\Services;

use App\Microservices\Auth\Services\ApiService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Microservices\User\Models\User;
use Exception;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use App\Microservices\User\Services\UserService;

class AuthService
{
    const TYPE_LOGIN = 'TYPE_LOGIN';
    const TYPE_LOGIN_TEMP = 'TYPE_LOGIN_TEMP';

    private $jwt;
    private $api;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /***
     * register
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @since: 2022/07/25 23:13
     */
    public function doRegister(array $args): User
    {
        try {
            $ck = app(UserService::class)->findUserBy(['phone'=>$args['phone']]);
            throw_if($ck, throw new \Exception(__('phone_existed')));
            $user = app(UserService::class)->newUser(['password' => Hash::make($args['password']), 'phone' => $args['phone'], 'brand_id'=>$args['brand_id']]);
        } catch (\Exception $e) {
            // if ($e instanceof QueryException && (int)$e->getCode() === 23000) {
                throw new \Exception($e->getMessage());
            // }
            throw $e;
        }

        // send email to confirm here.
        //dispatch(new SendVerificationEmail($user));

        return $user;
    }

    /***
     * doVerifyEmail
     *
     * @param string $token
     *
     * @return bool
     * @throws \Exception
     * @since: 2022/08/02 22:33
     */
    public function doVerifyEmail(string $token): bool
    {
        $user = app(UserService::class)->findUserBy(['email_token' => $token]);
        if ($user->exists) {
            return true;
            // return app(UserService::class)->updateUser($user->id, ['verified' => 1, 'email_token' => 1]);
        }

        throw new \Exception('shopbe_user_not_found');
    }

    /***
     * doLogin
     *
     * @param string $email
     * @param string $password
     *
     * @return array
     * @throws JWTException
     * @since: 2022/08/02 21:47
     */
    public function doLogin(string $phone, string $password): array
    {
        try {
            $user = app(UserService::class)->findUserBy(['phone' => $phone]);
            if (!($user && $user->exists && $user->verified === 1)) {
                throw new \Exception('shopbe_must_verify_email');
            }

            $jwtAttempt = compact('phone', 'password');
            if (!$token = $this->jwt->attempt($jwtAttempt)) {
                throw new \Exception('user_not_found');
            }

            // app(UserService::class)->updateUser($user->id, ['auth_token' => $token]);
            return [self::TYPE_LOGIN, $token];
        } catch (JWTException $e) {
            if ($e instanceof JWTException) {
                throw new \Exception('failed_to_create_token');
            }
            throw $e;
        }
    }

    /***
     * doLogout
     *
     * @param string $token
     *
     * @throws \Exception
     * @since: 2022/08/02 21:56
     */
    public function doLogout(string $token)
    {
        $this->jwt->setToken($token)->invalidate();
    }

    public function logoutById($id)
    {
        $user = app(UserService::class)->findUserBy(['id' => $id]);
        if ($user) {
            $this->jwt->setToken($user->auth_token)->invalidate();
            app(UserService::class)->updateUser($user->id, ['auth_token' => '']);
        }
    }

    public function doRefresh()
    {
        try {
            $token = auth()->refresh();
            return $token;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
