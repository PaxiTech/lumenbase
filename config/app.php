<?php
return [
    'locale' => 'vi',
    'serviceInternal'=>[
        'user'=>1,
        'brand'=>1,
        'wallet'=>1
    ],
    'serviceHost'=>[
        'user'=>'http://'.env('USER_HOST'),
        'brand'=>'http://'.env('BRAND_HOST'),
        'wallet'=>'http://'.env('WALLET_HOST')
    ],
    'lotte'=>[
        'cities_disabled'=>[100,18]
    ]
];