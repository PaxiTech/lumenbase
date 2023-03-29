<?php

$router->group(['middleware'=>'auth'], function($router){
    $router->get('/', 'ProfileController@index');
    $router->post('/update', 'ProfileController@update');
    $router->post('/change-pass', 'ProfileController@changePass');
});