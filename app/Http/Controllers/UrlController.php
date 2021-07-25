<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exceptions\UrlException;
use App\Models\Url;

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
        $Url->url = $params['url'];
        $Url->hash = $Url->getHash();
        $Url->expires_at = $Url->getExpirationDate();
        $Url->save();

        # Montando estrutura do array de resposta.
        $response = [
            'hash' => $Url->hash,
            'url' => $Url->url,
            'created_at' => $Url->created_at->toDateTimeString(),
            'expires_at' => $Url->expires_at->toDateTimeString()
        ];

        return response()->json($response);
    }
}
