<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $users = new UserModel();

        $user = new User([
            'username' => 'admin',
            'active'   => true,
            'nome'     => 'Amministratore',
            'cognome'  => 'Sistema',
            'ruolo'    => 'admin',
        ]);

        $users->save($user);

        $user = $users->findById($users->getInsertID());
        $user->createEmailIdentity([
            'email'    => 'daniela.cali.1981@gmail.com',
            'password' => 'Admin@1234',
        ]);
    }
}
