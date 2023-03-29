<?php

namespace App\Microservices\User\Controllers;

use App\Base\BaseController;

class UserController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        return response('test');
    }
    //
}
