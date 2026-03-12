<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function countries()
    {
        $list = Country::where('is_active', true)->orderBy('name_en')->get(['id','iso2','name_en','name_ar']);
        return response()->json($list);
    }

    public function states(Request $request)
    {
        $countryId = (int) $request->query('country_id');
        abort_if(!$countryId, 400);
        $list = State::where('country_id', $countryId)->where('is_active', true)->orderBy('name_en')->get(['id','code','name_en','name_ar']);
        return response()->json($list);
    }

    public function cities(Request $request)
    {
        $stateId = (int) $request->query('state_id');
        $countryId = (int) $request->query('country_id');
        $q = City::query()->where('is_active', true);
        if ($stateId) {
            $q->where('state_id', $stateId);
        } elseif ($countryId) {
            $q->where('country_id', $countryId);
        } else {
            abort(400);
        }
        $list = $q->orderBy('name_en')->get(['id','name_en','name_ar','state_id']);
        return response()->json($list);
    }
}

