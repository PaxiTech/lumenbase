<?php

namespace App\Microservices\Profile\Controllers;

use App\Base\BaseController;
use App\Microservices\Profile\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseController
{
    protected $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        return $this->successResponse($this->user());
    }

    public function update(Request $request)
    {
        $params = $request->only(['name', 'username',
        'email', 'phone']);
        foreach ($params as $key => $value) {
            if(!$value) unset($params[$key]);
        }
        unset($params['password']);
        if($this->service->update($this->user()->id, $params)){
            return $this->successResponse(__("update_profile_success"));
        }
    }

    public function changePass(Request $request)
    {
        $old = $request->input('old_password');
        $new = $request->input('password');
        $cnew = $request->input('confirm_password');
        $hash = $this->user()->password;
        if (!Hash::check($old, $hash)) {
            return $this->falseResponse(__("password_not_match"));
        }
        if($new != $cnew){
            return $this->falseResponse(__('confirm_password_not_match'));
        }
        $this->service->update($this->user()->id, ['password'=>Hash::make($new)]);
        return $this->successResponse(__("change_password_success"));
    }
    //
}
