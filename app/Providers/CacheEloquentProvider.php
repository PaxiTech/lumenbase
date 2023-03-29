<?php
// Place the class wherever you think is good
namespace App\Providers;

use App\Libraries\ClientDetector;
use Illuminate\Auth\EloquentUserProvider;

class CacheEloquentProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        // dd(app('cache')->getRedis()->keys('*'));
        // implement cache however you like
        // following is for simplicity.
        $key = "token_cached_" . $identifier;
        // app('cache')->forget($key);
        // dd(app('cache')->getRedis()->ttl($key));
        if (!$user = json_decode(app('cache')->get($key))) {
            $user = parent::retrieveById($identifier);
            if (!$user) return false;

            app('cache')->put($key, json_encode($user), 1200);
            $user = json_decode(app('cache')->get($key));
            $cd = new ClientDetector();
            $cd = $cd->result();
            $cd['user_id'] = $user->id;
            app('db')->table('login_histories')->insert($cd);
        }
        return $user;
    }
}
