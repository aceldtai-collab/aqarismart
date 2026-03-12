<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HealthController extends Controller
{
    public function ready(): Response
    {
        return response('ok', 200);
    }

    public function live(): Response
    {
        try {
            DB::select('select 1');
            return response('ok', 200);
        } catch (\Throwable $e) {
            return response('db down', 500);
        }
    }
}

