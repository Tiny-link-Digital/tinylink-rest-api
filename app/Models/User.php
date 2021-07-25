<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use App\Exceptions\UserException;
use Illuminate\Http\Request;

class User extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * Atualiza a chave de login do usuário.
     *
     * @param string $id
     *
     * @return void
     */
    public function updateAppKey(string $id)
    {
        $app_key = uniqid();

        $this->app_key = $app_key;

        $this->save();

        return $this->app_key;
    }

    /**
     * Valida a chave de login do usuário.
     *
     * @param Request $request
     *
     * @return void
     */
    public static function validateAppKey(Request $request): User
    {
        $auth_header = $request->header('Authorization');

        if (empty($auth_header)) {
            throw new UserException('empty_auth_header', 404);
        }

        $explode_auth = explode('Bearer ', $auth_header);

        if (count($explode_auth) !== 2) {
            throw new UserException('wrong_auth_format', 403);
        }

        $app_key = $explode_auth[1];

        $user = self::where('app_key', '=', $app_key)->first();

        if (empty($user)) {
            throw new UserException('user_does_not_exists', 400);
        }

        return $user;
    }
}
