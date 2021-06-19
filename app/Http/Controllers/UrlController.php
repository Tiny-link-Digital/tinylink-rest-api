<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\UrlException;
use App\Models\Url;

class UrlController extends Controller
{
    /**
     * Realiza o encurtamento da URL de um usuário autenticado.
     *
     * @param Request $request
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        # Verificando se foi enviada uma URL válida através de Regex.
        preg_match('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $request->get('url'), $matches);

        if (count($matches) === 0) {
            throw new UrlException('not_an_valid_url', 400);
        }

        return response()->json(['success' => true]);
    }
}
