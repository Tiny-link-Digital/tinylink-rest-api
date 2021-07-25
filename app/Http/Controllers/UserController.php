<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\UserException;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Realiza o login social de um usuário, caso seja o primeiro,
     * registra o novo usuário no banco.
     *
     * @see https://developers.google.com/identity/sign-in/web/backend-auth
     */
    public function signIn(Request $request)
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

        $User = User::where('id', '=', $payload['sub'])->first();

        if (empty($User)) {
            $User = new User();

            $User->id = $payload['sub'];
            $User->email = $payload['email'];
            $User->name = $payload['name'];
            $User->picture = $payload['picture'];
            $User->app_key = $User->updateAppKey();

            $User->save();
        }

        return response()->json($User);
    }
}
