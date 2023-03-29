<?php

namespace App\Microservices\User\Services;

use App\Base\BaseRepository;
use App\Microservices\User\Models\User;
use Exception;
use Illuminate\Database\QueryException;

class UserService
{
    protected $repository;
    protected $model;
    public function __construct(User $model, BaseRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->makeModel($model);
        $this->model = $model;
    }

    public function newUser($params)
    {
        try {
            $user = new User();
            foreach ($params as $key => $value) {
                $user->{$key} = $value;
            }
            if (!$user->save()) {
                throw new \Exception('shopbe_failure_created_user');
            }
            return $user;
        } catch (\Exception $e) {
            if ($e instanceof QueryException && (int)$e->getCode() === 23000) {
                throw new \Exception($e->getMessage());
            }
            throw $e;
        }
    }

    public function findBy($param)
    {
        try {
            return $this->repository->findWhere($param)->first();
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateUser($id, $param)
    {
        try {
            $u = $this->repository->update($param, $id);
            app('cache')->forget('token_cached_'.$id);
            return $u;
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
