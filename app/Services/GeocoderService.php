<?php

namespace App\Services;

class GeocoderService
{
    private const ENDPOINT   = 'https://nominatim.openstreetmap.org/search';
    private const USER_AGENT = 'ColombiniPiscine/1.0 (daniela.cali.1981@gmail.com)';

    public function geocode(string $indirizzo, string $citta, string $cap = '', string $paese = 'Italia'): ?array
    {
        $query = implode(', ', array_filter([$indirizzo, $cap, $citta, $paese]));

        $url = self::ENDPOINT . '?' . http_build_query([
            'q'              => $query,
            'format'         => 'json',
            'limit'          => 1,
            'addressdetails' => 0,
        ]);

        $raw = $this->curlGet($url);
        if (! $raw) return null;

        $data = json_decode($raw, true);
        if (empty($data[0])) return null;

        return [
            'lat' => (float) $data[0]['lat'],
            'lng' => (float) $data[0]['lon'],
        ];
    }

    private function curlGet(string $url): ?string
    {
        if (! function_exists('curl_init')) return null;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_USERAGENT      => self::USER_AGENT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $error    = curl_errno($ch);
        curl_close($ch);

        return ($error === 0 && $response !== false) ? $response : null;
    }
}
