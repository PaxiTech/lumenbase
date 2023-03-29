<?php 
namespace App\Microservices\Profile\Services;

use App\Microservices\User\Services\UserService;

class ProfileService
{
    public function __construct()
    {
    }
    public function update($id, $params)
    {
        return app(UserService::class)->updateUser($id, $params);
    }
}
