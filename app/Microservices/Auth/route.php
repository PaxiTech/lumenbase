<?php

$router->post('register', 'AuthController@register');
$router->get('register/verification/{token}', 'AuthController@verifyEmail');

$router->post('login', 'AuthController@login');
$router->group(['middleware' => 'auth'], function ($router) {
    $router->get('/me', 'AuthController@me');
    $router->post('refresh', 'AuthController@refresh');
    $router->get('logout', 'AuthController@logout');
    $router->get('test', 'AuthController@test');
});
