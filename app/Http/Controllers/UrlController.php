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

        # Gerando uma nova hash
        $hash = "";
        while ($hash === "") {
            $hash = $this->createHash($params['url']);
        }

        # Cadastrando uma nova url encurtada.
        $url = new Url();
        $url->url = $params['url'];
        $url->hash = $hash;
        $url->expires_at = $this->getExpirationDate();
        $url->save();

        # Montando estrutura do array de resposta.
        $response = [
            'hash' => $url->hash,
            'url' => $params['url'],
            'created_at' => $url->created_at->toDateTimeString(),
            'expires_at' => $url->expires_at->toDateTimeString()
        ];

        return response()->json($response);
    }

    /**
     * Lógica de geração da hash da URL encurtada.
     *
     * @param string $url
     * @param int $step
     * @param string $_id
     *
     * @return string
     */
    private function createHash(string $url): string
    {
        $hash = "";

        $now = Carbon::now()->toDateTimeString();
        $url_encrypted = md5($url.$now);

        $encryption_letters = str_split($url_encrypted);
        $encryption_length = count($encryption_letters);

        $rand_index_history = [];
        $counter = 0;
        while ($counter < 6) {
            $rand_index = rand(1, $encryption_length) - 1;

            if (!in_array($rand_index, $rand_index_history)) {
                $hash .= $encryption_letters[$rand_index];

                array_push($rand_index_history, $rand_index);

                $counter++;
            }
        }

        return $this->checkHashUniquity($hash);
    }

    /**
     * Retorna a data de expiração dessa URL.
     *
     * @return Illuminate\Support\Carbon
     */
    private function getExpirationDate(): Carbon
    {
        $expiration_date = new Carbon();

        $expiration_date->addYears(1);

        return $expiration_date;
    }

    /**
     * Verifica se o hash gerado é único.
     *
     * @param string $hash
     *
     * @return string
     */
    private function checkHashUniquity(string $hash): string
    {
        $repeated_hash = Url::where('hash', '=', $hash)->get();

        if (count($repeated_hash) > 0) {
            return "";
        }

        return $hash;
    }
}
