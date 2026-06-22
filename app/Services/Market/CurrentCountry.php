<?php

namespace App\Services\Market;

use App\Models\Country;
use Illuminate\Http\Request;

class CurrentCountry
{
    public const COOKIE = 'aqari_country';
    public const DEFAULT_ISO2 = 'IQ';

    protected ?Country $country = null;

    public function set(?Country $country): void
    {
        $this->country = $country;
    }

    public function get(): Country
    {
        if ($this->country) {
            return $this->country;
        }

        $this->country = Country::query()
            ->where('iso2', self::DEFAULT_ISO2)
            ->first()
            ?? Country::query()->where('is_active', true)->orderBy('name_en')->first()
            ?? new Country([
                'iso2' => self::DEFAULT_ISO2,
                'currency_code' => 'IQD',
                'name_en' => 'Iraq',
                'name_ar' => 'العراق',
                'is_active' => true,
            ]);

        return $this->country;
    }

    public function resolve(Request $request): Country
    {
        $candidate = $request->query('country')
            ?? $request->query('country_id')
            ?? $request->header('X-Country')
            ?? $request->cookie(self::COOKIE)
            ?? ($request->hasSession() ? $request->session()->get(self::COOKIE) : null);

        $country = $this->findCountry($candidate)
            ?? $this->findCountry(self::DEFAULT_ISO2)
            ?? Country::query()->where('is_active', true)->orderBy('name_en')->first();

        if ($country) {
            $this->set($country);
            return $country;
        }

        return $this->get();
    }

    public function options()
    {
        return Country::query()
            ->where('is_active', true)
            ->whereIn('iso2', ['IQ', 'JO'])
            ->orderByRaw("CASE iso2 WHEN 'IQ' THEN 0 WHEN 'JO' THEN 1 ELSE 2 END")
            ->orderBy('name_en')
            ->get(['id', 'iso2', 'phone_code', 'currency_code', 'name_en', 'name_ar']);
    }

    protected function findCountry(mixed $candidate): ?Country
    {
        if ($candidate === null || $candidate === '') {
            return null;
        }

        $query = Country::query()->where('is_active', true);

        if (is_numeric($candidate)) {
            return $query->whereKey((int) $candidate)->first();
        }

        $code = strtoupper(trim((string) $candidate));

        return $query
            ->where(function ($q) use ($code) {
                $q->where('iso2', $code)->orWhere('iso3', $code);
            })
            ->first();
    }
}
