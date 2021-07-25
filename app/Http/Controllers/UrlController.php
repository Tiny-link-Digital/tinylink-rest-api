<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exceptions\UrlException;
use App\Models\Url;
use App\Models\User;

class UrlController extends Controller
{
    /**
     * Recupera uma URL baseado na sua respectiva hash.
     *
     * @param string $hash
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function get(string $hash)
    {
        $Url = Url::where('hash', '=', $hash)->first();

        return response()->json($Url);
    }

    /**
     * Gera uma nova URL encurtada.
     *
     * @param Request $request
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $User = User::validateAppKey($request);

        $params = [
            'url' => $request->get('url')
        ];

        # Verificando se foi enviada uma URL válida através de Regex.
        preg_match('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $params['url'], $matches);

        if (count($matches) === 0) {
            throw new UrlException('not_an_valid_url', 400);
        }

        # Cadastrando uma nova url encurtada.
        $Url = new Url();
        $Url->user_id = $User->id;
        $Url->url = $params['url'];
        $Url->hash = $Url->getHash();
        $Url->expires_at = $Url->getExpirationDate();
        $Url->save();

        # Montando estrutura do array de resposta.
        return response()->json($Url);
    }

    /**
     * Lista as url's geradas por determinado usuário.
     */
    public function list(Request $request)
    {
        $User = User::validateAppKey($request);

        $urls = Url::where('user_id', '=', $User->id)->paginate(9);

        return response()->json($urls);
    }
}
