<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exceptions\UrlException;
use App\Models\Url;

class UrlController extends Controller
{
    /**
     * Callback da rota /url/create
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
     * Gera uma hash de 6 caracteres baseada em uma URL.
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

        # Criptografando a junção da URL com a data e hora atual do servidor.
        $now = Carbon::now()->toDateTimeString();
        $url_encrypted = md5($url.$now);

        # Criando array que soletra a criptografia acima.
        $encryption_letters = str_split($url_encrypted);
        $encryption_length = count($encryption_letters);

        # Mapeando 6 caracteres da criptografia acima, de forma aleatória, e sem repetir os índices.
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
