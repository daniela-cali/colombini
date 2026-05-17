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
        'ultima_versione_vista',
        'richiede_cambio_auto',
    ];
}
