<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\UserException;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Recupera um usuário.
     *
     * @param array $payload
     */
    private function get(array $payload)
    {
        $user = User::where('id', '=', $payload['sub'])->first();

        return $user;
    }

    /**
     * Integra com a API de Login Social do Google, e registra um novo usuário no banco.
     *
     * @see https://developers.google.com/identity/sign-in/web/backend-auth
     */
    public function register(Request $request)
    {
        $Google_Client = new \Google_Client([
            'client_id' => env('GOOGLE_CLIENT_ID')
        ]);

        $payload = $Google_Client->verifyIdToken($request->get('id_token'));

        if (!$payload) {
            throw new UserException('user_not_found', 404);
        }

        if ($payload['aud'] !== env('GOOGLE_CLIENT_ID')) {
            throw new UserException('user_wrong_client_id', 403);
        }

        $user = $this->get($payload);

        if ($user == false) {
            $user = new User();

            $user->id = $payload['sub'];
            $user->email = $payload['email'];
            $user->name = $payload['name'];
            $user->picture = $payload['picture'];

            $user->save();
        }

        return response()->json([
            'success' => true,
            'unique_id' => $user->id
        ]);
    }
}
