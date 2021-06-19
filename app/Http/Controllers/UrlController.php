<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;

class UrlController extends Controller
{
    public function index(Request $request)
    {
        $Url = Url::all();
        return response()->json($Url);
    }
}
