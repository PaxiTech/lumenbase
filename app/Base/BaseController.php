<?php

namespace App\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller;

class BaseController extends Controller
{
    protected $request;
    protected $service;
    protected $partnerCode;
    public function __construct(Request $request, $partnerCode = null)
    {
        $this->request = $request;
        $this->partnerCode = $partnerCode;
    }
    public function falseResponse($data, $status = 400)
    {
        if(is_string($data)){
            return response()->json(['success'=>false, 'msg'=>$data], $status);
        }
    }

    public function successResponse($data, $attr = [])
    {
        $res = ['success'=>true];
        if(is_string($data)){
            $res['msg'] = $data;
        }
        else if(is_array($data) || is_object($data)){
            if(isset(((array)$data)['token'])){
                
            }
            $res['rows']=$data;
        }
        else{
            $res = ['success'=>true, 'data'=>$data];
        }
        return response()->json($res);
    }
    public function user()
    {
        return auth()->authenticate();
    }
}
