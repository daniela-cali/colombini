<?php

namespace App\Controllers\Impostazioni;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class Utenti extends BaseController
{
    public function utentiPortale(): string
    {
        $utenti = (new UserModel())
            ->where('ruolo', 'cliente')
            ->orderBy('cognome', 'ASC')
            ->findAll();

        return view('impostazioni/utenti_portale', [
            'title'          => 'Utenti Portale',
            'page_title'     => 'Utenti Portale',
            'utenti_portale' => $utenti,
        ]);
    }

    public function creaUtentePortale(): string
    {
        return view('impostazioni/crea_utente_portale', [
            'title'      => 'Nuovo Utente Portale',
            'page_title' => 'Nuovo Utente Portale',
        ]);
    }

    public function storeUtentePortale()
    {
        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'username'         => ['is_unique' => 'Questo nome utente è già in uso.'],
            'password_confirm' => ['matches'   => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $users    = new UserModel();
        $user     = new User([
            'username' => $username,
            'active'   => true,
            'nome'     => $this->request->getPost('nome'),
            'cognome'  => $this->request->getPost('cognome'),
            'ruolo'    => 'cliente',
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());

        $user->createEmailIdentity([
            'email'    => $username . '@portale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup('cliente');

        return redirect()->to('impostazioni/utenti-portale')
            ->with('success', 'Utente portale "' . $username . '" creato con successo.');
    }

    public function editUtentePortale(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user) {
            return redirect()->to('impostazioni/utenti-portale')->with('error', 'Utente non trovato.');
        }
        if ($user->ruolo !== 'cliente') {
            return redirect()->to('impostazioni/utenti-portale')
                ->with('error', "L'utente ha un ruolo diverso da 'cliente': " . esc($user->ruolo));
        }

        $cliente = (new ClienteModel())->getByUserId($id);
        if (! $cliente) {
            return redirect()->to('impostazioni/utenti-portale')
                ->with('error', 'Nessun cliente collegato a questo utente.');
        }

        $denominazione = ! empty($cliente['ragsoc'])
            ? $cliente['ragsoc']
            : trim($cliente['nome'] . ' ' . $cliente['cognome']);

        return view('impostazioni/edit_utente_portale', [
            'title'      => 'Modifica Utente Portale',
            'page_title' => 'Modifica Utente Portale',
            'cliente'    => $denominazione,
            'utente'     => $user,
        ]);
    }

    public function updateUtentePortale(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user) {
            return redirect()->to('impostazioni/utenti-portale')->with('error', 'Utente non trovato.');
        }
        if ($user->ruolo !== 'cliente') {
            return redirect()->to('impostazioni/utenti-portale')
                ->with('error', "L'utente ha un ruolo diverso da 'cliente': " . esc($user->ruolo));
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username,id,' . $id . ']',
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        $messages = [
            'username'         => [
                'min_length' => 'Il nome utente deve contenere un minimo di 3 caratteri, fino a un massimo di 30.',
                'is_unique'  => 'Questo nome utente è già in uso.',
            ],
            'password_confirm' => ['matches' => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nuovoUsername = $this->request->getPost('username');
        $users->update($id, ['username' => $nuovoUsername]);

        if ($nuovoUsername !== $user->username) {
            $identity         = $user->getEmailIdentity();
            $identity->secret = $nuovoUsername . '@portale.colombini-snc.it';
            model(\CodeIgniter\Shield\Models\UserIdentityModel::class)->save($identity);
        }

        if ($password) {
            $user = $users->findById($id);
            $user->setPassword($password);
            $users->save($user);
        }

        return redirect()->to('impostazioni/utenti-portale')
            ->with('success', 'Utente "' . $nuovoUsername . '" aggiornato.');
    }

    public function deleteUtentePortale(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user) {
            return redirect()->to('impostazioni/utenti-portale')->with('error', 'Utente non trovato.');
        }
        if ($user->ruolo !== 'cliente') {
            return redirect()->to('impostazioni/utenti-portale')
                ->with('error', "L'utente ha un ruolo diverso da 'cliente': " . esc($user->ruolo));
        }

        $clienteRecord = (new ClienteModel())->getByUserId($id);
        if ($clienteRecord) {
            $viaggi = db_connect()->table('interventi')
                ->select('viaggi_tappe.viaggio_id')
                ->distinct()
                ->join('viaggi_tappe', 'viaggi_tappe.intervento_id = interventi.id')
                ->where('interventi.cliente_id', $clienteRecord['id'])
                ->get()->getResultArray();

            if (count($viaggi) > 0) {
                $n     = count($viaggi);
                $nel   = $n === 1 ? 'nel'     : 'nei';
                $v     = $n === 1 ? 'viaggio' : 'viaggi';
                $links = implode(', ', array_map(
                    fn($vid) => '<a href="' . base_url('viaggi/' . (int) $vid) . '" class="alert-link">#' . (int) $vid . '</a>',
                    array_column($viaggi, 'viaggio_id')
                ));

                return redirect()->to('impostazioni/utenti-portale')
                    ->with('error_html', "Impossibile eliminare: il cliente ha interventi inseriti {$nel} {$v} {$links}. Rimuovili prima dai viaggi.");
            }
        }

        $users->delete($id, true);

        return redirect()->to('impostazioni/utenti-portale')
            ->with('success', 'Utente eliminato.');
    }

    // -------------------------------------------------------------------------
    // Utenti App
    // -------------------------------------------------------------------------

    public function utentiApp(): string
    {
        $utenti = (new UserModel())
            ->whereIn('ruolo', UserModel::RUOLI_APP)
            ->orderBy('ruolo', 'ASC')
            ->orderBy('cognome', 'ASC')
            ->findAll();

        return view('impostazioni/utenti_app', [
            'title'      => 'Utenti App',
            'page_title' => 'Utenti App',
            'utenti'     => $utenti,
        ]);
    }

    public function creaUtenteApp(): string
    {
        return view('impostazioni/crea_utente_app', [
            'title'      => 'Nuovo Utente App',
            'page_title' => 'Nuovo Utente App',
        ]);
    }

    public function storeUtenteApp()
    {
        $ruoliValidi = implode(',', UserModel::RUOLI_APP);

        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'nome'             => 'required|max_length[100]',
            'cognome'          => 'required|max_length[100]',
            'ruolo'            => 'required|in_list[' . $ruoliValidi . ']',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'username'         => ['is_unique' => 'Questo nome utente è già in uso.'],
            'password_confirm' => ['matches'   => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $ruolo    = $this->request->getPost('ruolo');
        $users    = new UserModel();
        $user     = new User([
            'username' => $username,
            'active'   => true,
            'nome'     => $this->request->getPost('nome'),
            'cognome'  => $this->request->getPost('cognome'),
            'ruolo'    => $ruolo,
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());

        $user->createEmailIdentity([
            'email'    => $username . '@gestionale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup($ruolo);

        return redirect()->to('impostazioni/utenti-app')
            ->with('success', 'Utente "' . $username . '" creato con successo.');
    }

    public function editUtenteApp(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || ! in_array($user->ruolo, UserModel::RUOLI_APP)) {
            return redirect()->to('impostazioni/utenti-app')->with('error', 'Utente non trovato.');
        }

        return view('impostazioni/edit_utente_app', [
            'title'      => 'Modifica Utente',
            'page_title' => 'Modifica Utente',
            'utente'     => $user,
        ]);
    }

    public function updateUtenteApp(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || ! in_array($user->ruolo, UserModel::RUOLI_APP)) {
            return redirect()->to('impostazioni/utenti-app')->with('error', 'Utente non trovato.');
        }

        $rules = [
            'nome'    => 'required|max_length[100]',
            'cognome' => 'required|max_length[100]',
            'ruolo'   => 'required|in_list[' . implode(',', UserModel::RUOLI_APP) . ']',
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (! $this->validate($rules, ['password_confirm' => ['matches' => 'Le password non coincidono.']])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nuovoRuolo = $this->request->getPost('ruolo');

        $users->update($id, [
            'nome'                   => $this->request->getPost('nome'),
            'cognome'                => $this->request->getPost('cognome'),
            'ruolo'                  => $nuovoRuolo,
            'assegnabile_interventi' => $nuovoRuolo === 'tecnico' ? 0 : (int) $this->request->getPost('assegnabile_interventi'),
        ]);

        if ($user->ruolo !== $nuovoRuolo) {
            $user->removeGroup($user->ruolo);
            $user->addGroup($nuovoRuolo);
        }

        if ($password) {
            $user->setPassword($password);
            $users->save($user);
        }

        return redirect()->to('impostazioni/utenti-app')
            ->with('success', 'Utente "' . $user->username . '" aggiornato.');
    }

    public function deleteUtenteApp(int $id)
    {
        $users = new UserModel();
        $user  = $users->findById($id);

        if (! $user || ! in_array($user->ruolo, UserModel::RUOLI_APP)) {
            return redirect()->to('impostazioni/utenti-app')->with('error', 'Utente non trovato.');
        }

        $users->delete($id, true);

        return redirect()->to('impostazioni/utenti-app')
            ->with('success', 'Utente "' . $user->username . '" eliminato.');
    }
}
