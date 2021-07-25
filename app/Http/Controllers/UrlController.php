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
        $url = Url::where('hash', '=', $hash)->first();

        return response()->json($url);
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
        $url = new Url();
        $url->url = $params['url'];
        $url->hash = $url->getHash();
        $url->expires_at = $url->getExpirationDate();
        $url->save();

        # Montando estrutura do array de resposta.
        $response = [
            'hash' => $url->hash,
            'url' => $url->url,
            'created_at' => $url->created_at->toDateTimeString(),
            'expires_at' => $url->expires_at->toDateTimeString()
        ];

        return response()->json($response);
    }
}
