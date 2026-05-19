<?php

if (! function_exists('data_ita')) {
    /**
     * Restituisce la data formattata in italiano, es. "lunedì 19/05/2026".
     */
    function data_ita(string $date, bool $conGiorno = true): string
    {
        static $giorni = [
            'Monday'    => 'Lunedì',
            'Tuesday'   => 'Martedì',
            'Wednesday' => 'Mercoledì',
            'Thursday'  => 'Giovedì',
            'Friday'    => 'Venerdì',
            'Saturday'  => 'Sabato',
            'Sunday'    => 'Domenica',
        ];

        $ts = strtotime($date);

        if ($conGiorno) {
            return ($giorni[date('l', $ts)] ?? strtolower(date('l', $ts)))
                   . ' ' . date('d/m/Y', $ts);
        }

        return date('d/m/Y', $ts);
    }
}
