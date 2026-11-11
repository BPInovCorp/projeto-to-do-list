<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:pt_PT,en'],
        ]);

        $locale = $validated['locale'];

        $request->session()->put('locale', $locale);
        App::setLocale($locale);

        return response()->json([
            'status' => 'sucesso',
            'locale' => $locale,
        ]);
    }
}