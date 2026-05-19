<?php

namespace App\Services;

class VrpService
{
    private const ENDPOINT = 'https://api.openrouteservice.org/optimization';

    public function ottimizza(array $payload): array
    {
        $apiKey = env('ORS_API_KEY');

        $ch = curl_init(self::ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . $apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_errno($ch);
        curl_close($ch);

        if ($error !== 0 || $response === false) {
            return ['errore' => 'Errore di connessione al servizio ORS.'];
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200 || ! is_array($data)) {
            $msg = $data['message'] ?? $data['error'] ?? 'Risposta non valida dal servizio ORS.';
            return ['errore' => $msg];
        }

        return $data;
    }
}
