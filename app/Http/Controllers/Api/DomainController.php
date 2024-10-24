<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Domain;

class DomainController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate([
            'domain' => ['required', 'max:255'],
        ], $request->query());

        $defaults = in_array($request->input('domain'), config('domains.available'));
        $db = Domain::where('domain', $request->input('domain'))->exists();

        if ($defaults || $db) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 404);
    }
}
