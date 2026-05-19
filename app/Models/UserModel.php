<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    public const RUOLI = [
        'admin'      => 'Amministratore',
        'staff'      => 'Staff',
        'tecnico'    => 'Tecnico',
        'operativo'  => 'Operativo',
        'cliente'    => 'Cliente portale',
    ];

    public const RUOLI_APP = ['admin', 'staff', 'tecnico', 'operativo'];

    protected $allowedFields = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'nome',
        'cognome',
        'telefono',
        'colore',
        'ruolo',
        'assegnabile_interventi',
        'ultima_versione_vista',
        'richiede_cambio_auto',
        'veicolo_id',
    ];

    public function whereAssegnabile(): static
    {
        return $this->groupStart()
            ->where('ruolo', 'tecnico')
            ->orWhere('assegnabile_interventi', 1)
            ->groupEnd();
    }
}
