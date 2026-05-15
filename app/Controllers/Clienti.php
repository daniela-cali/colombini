<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\InterventoModel;
use App\Models\UserModel;
use App\Services\ClientiImportService;
use App\Services\GeocoderService;
use CodeIgniter\Shield\Entities\User;

class Clienti extends BaseController
{
    public function index(): string
    {
        $model = new ClienteModel();
        $q     = $this->request->getGet('q');

        $clienti = $q
            ? $model->ricerca($q)
            : $model->where('stato', 1)->orderBy('ragsoc, cognome')->findAll();

        return view('clienti/index', [
            'title'      => 'Clienti',
            'page_title' => 'Clienti',
            'clienti'    => $clienti,
            'q'          => $q,
        ]);
    }

    public function show(int $id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find($id);

        if (! $cliente) {
            return redirect()->to('clienti')->with('error', 'Cliente non trovato.');
        }

        $utente = null;
        if ($cliente['user_id']) {
            $users  = new UserModel();
            $utente = $users->find($cliente['user_id']);
        }

        return view('clienti/show', [
            'title'        => $model->getNomeDisplay($cliente),
            'page_title'   => 'Scheda Cliente',
            'cliente'      => $cliente,
            'utente'       => $utente,
            'nome_display' => $model->getNomeDisplay($cliente),
        ]);
    }

    public function create(): string
    {
        $model = new ClienteModel();

        return view('clienti/create', [
            'title'      => 'Nuovo Cliente',
            'page_title' => 'Nuovo Cliente',
            'codice_int' => $model->generaCodiceInterno(),
        ]);
    }

    public function store()
    {
        $tipo  = $this->request->getPost('tipo');
        $rules = $this->getValidationRules($tipo);

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new ClienteModel();
        $id    = $model->insert([
            'codice'    => $this->request->getPost('codice'),
            'tipo'      => $tipo,
            'ragsoc'    => $this->request->getPost('ragsoc')    ?: null,
            'nome'      => $this->request->getPost('nome')      ?: null,
            'cognome'   => $this->request->getPost('cognome')   ?: null,
            'piva'      => $this->request->getPost('piva')      ?: null,
            'cfisc'     => $this->request->getPost('cfisc')     ?: null,
            'indirizzo' => $this->request->getPost('indirizzo') ?: null,
            'citta'     => $this->request->getPost('citta')     ?: null,
            'cap'       => $this->request->getPost('cap')       ?: null,
            'provincia' => $this->request->getPost('provincia') ?: null,
            'telefono'  => $this->request->getPost('telefono')  ?: null,
            'email'     => $this->request->getPost('email')     ?: null,
            'note'      => $this->request->getPost('note')      ?: null,
            'stato'     => 1,
        ]);

        $this->geocodifica($model, $id);

        return redirect()->to('clienti/' . $id)->with('success', 'Cliente creato con successo.');
    }

    public function edit(int $id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find($id);

        if (! $cliente) {
            return redirect()->to('clienti')->with('error', 'Cliente non trovato.');
        }

        return view('clienti/edit', [
            'title'      => 'Modifica Cliente',
            'page_title' => 'Modifica Cliente',
            'cliente'    => $cliente,
        ]);
    }

    public function update(int $id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find($id);

        if (! $cliente) {
            return redirect()->to('clienti')->with('error', 'Cliente non trovato.');
        }

        $tipo  = $this->request->getPost('tipo');
        $rules = $this->getValidationRules($tipo, $id);

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'codice'    => $this->request->getPost('codice'),
            'tipo'      => $tipo,
            'ragsoc'    => $this->request->getPost('ragsoc')    ?: null,
            'nome'      => $this->request->getPost('nome')      ?: null,
            'cognome'   => $this->request->getPost('cognome')   ?: null,
            'piva'      => $this->request->getPost('piva')      ?: null,
            'cfisc'     => $this->request->getPost('cfisc')     ?: null,
            'indirizzo' => $this->request->getPost('indirizzo') ?: null,
            'citta'     => $this->request->getPost('citta')     ?: null,
            'cap'       => $this->request->getPost('cap')       ?: null,
            'provincia' => $this->request->getPost('provincia') ?: null,
            'telefono'  => $this->request->getPost('telefono')  ?: null,
            'email'     => $this->request->getPost('email')     ?: null,
            'note'      => $this->request->getPost('note')      ?: null,
        ]);

        $this->geocodifica($model, $id);

        return redirect()->to('clienti/' . $id)->with('success', 'Cliente aggiornato.');
    }

    public function delete(int $id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find($id);

        if (! $cliente) {
            return redirect()->to('clienti')->with('error', 'Cliente non trovato.');
        }

        $count = (new InterventoModel())->where('cliente_id', $id)->countAllResults();
        if ($count > 0) {
            return redirect()->to('clienti/' . $id)
                ->with('error', "Impossibile eliminare: il cliente ha {$count} intervento/i collegato/i. Rimuovere prima i collegamenti o completare gli interventi.");
        }

        $model->delete($id);

        return redirect()->to('clienti')->with('success', 'Cliente eliminato.');
    }

    public function creaPortale(int $id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find($id);

        if (! $cliente) {
            return redirect()->to('clienti')->with('error', 'Cliente non trovato.');
        }

        if ($cliente['user_id']) {
            return redirect()->to('clienti/' . $id)->with('error', 'Questo cliente ha già un accesso portale.');
        }

        $usernameSuggerito = strtolower(str_replace('-', '', $cliente['codice']));

        return view('clienti/crea_portale', [
            'title'              => 'Crea Accesso Portale',
            'page_title'         => 'Crea Accesso Portale',
            'cliente'            => $cliente,
            'nome_display'       => $model->getNomeDisplay($cliente),
            'username_suggerito' => $usernameSuggerito,
        ]);
    }

    public function storePortale(int $id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find($id);

        if (! $cliente || $cliente['user_id']) {
            return redirect()->to('clienti/' . $id);
        }

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

        $nomeUtente    = $cliente['tipo'] === 'persona_fisica' ? ($cliente['nome'] ?? '') : '';
        $cognomeUtente = $cliente['tipo'] === 'persona_fisica'
            ? ($cliente['cognome'] ?? '')
            : ($cliente['ragsoc'] ?? '');

        $users = new UserModel();
        $user  = new User([
            'username' => $username,
            'active'   => true,
            'nome'     => $nomeUtente,
            'cognome'  => $cognomeUtente,
            'ruolo'    => 'cliente',
        ]);

        $users->save($user);
        $user = $users->findById($users->getInsertID());

        $user->createEmailIdentity([
            'email'    => $username . '@portale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup('cliente');
        $model->update($id, ['user_id' => $user->id]);

        return redirect()->to('clienti/' . $id)
            ->with('success', 'Accesso portale creato per "' . $username . '".');
    }

    public function importView(): string
    {
        return view('clienti/import', [
            'title'      => 'Import Clienti',
            'page_title' => 'Import Clienti da CSV',
        ]);
    }

    public function importAnalizza()
    {
        $file = $this->request->getFile('csv_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'File non valido o non caricato.');
        }

        if (! in_array($file->getClientExtension(), ['csv', 'txt'])) {
            return redirect()->back()->with('error', 'Formato non supportato. Carica un file .csv');
        }

        $dest = WRITEPATH . 'uploads/import/';
        if (! is_dir($dest)) mkdir($dest, 0755, true);

        $nomefile = 'clienti_import_' . time() . '.csv';
        $file->move($dest, $nomefile);
        $filePath = $dest . $nomefile;

        $service = new ClientiImportService();
        $headers = $service->leggiHeaders($filePath);

        if (empty($headers)) {
            unlink($filePath);
            return redirect()->back()->with('error', 'Impossibile leggere le intestazioni del file.');
        }

        session()->set('import_file', $filePath);
        session()->set('import_headers', $headers);

        return redirect()->to('clienti/import/mappa');
    }

    public function importMappa(): string
    {
        $filePath = session()->get('import_file');
        $headers  = session()->get('import_headers');

        if (! $filePath || ! $headers) {
            return redirect()->to('clienti/import')->with('error', 'Sessione scaduta, ricarica il file.');
        }

        $savedMapping = setting('Import.clienti_mapping')
            ? json_decode(setting('Import.clienti_mapping'), true)
            : [];

        return view('clienti/import_mappa', [
            'title'        => 'Mappa Colonne CSV',
            'page_title'   => 'Import Clienti — Mappa Colonne',
            'headers'      => $headers,
            'campi_dest'   => ClientiImportService::CAMPI_DEST,
            'saved_mapping'=> $savedMapping,
        ]);
    }

    public function importEsegui()
    {
        $filePath = session()->get('import_file');
        $headers  = session()->get('import_headers');

        if (! $filePath || ! file_exists($filePath)) {
            return redirect()->to('clienti/import')->with('error', 'Sessione scaduta, ricarica il file.');
        }

        // mapping: indice colonna CSV => campo DB
        $rawMapping = $this->request->getPost('mapping') ?? [];
        $mapping = [];
        foreach ($rawMapping as $idx => $dbField) {
            if ($dbField !== '') $mapping[(int)$idx] = $dbField;
        }

        if (! in_array('codice', $mapping)) {
            return redirect()->back()->with('error', 'Devi mappare almeno il campo "Codice".');
        }

        // salva mapping per i prossimi import
        $namedMapping = [];
        foreach ($mapping as $idx => $dbField) {
            $namedMapping[$headers[$idx]] = $dbField;
        }
        setting()->set('Import.clienti_mapping', json_encode($namedMapping));

        $service = new ClientiImportService();
        $result  = $service->import($filePath, $mapping);

        unlink($filePath);
        session()->remove(['import_file', 'import_headers']);

        return view('clienti/import_risultato', [
            'title'      => 'Risultato Import',
            'page_title' => 'Import Clienti — Risultato',
            'result'     => $result,
        ]);
    }

    private function geocodifica(ClienteModel $model, int $id): void
    {
        $indirizzo = $this->request->getPost('indirizzo');
        $citta     = $this->request->getPost('citta');

        if (! $indirizzo || ! $citta) return;

        $geo = (new GeocoderService())->geocode(
            $indirizzo,
            $citta,
            $this->request->getPost('cap') ?? ''
        );

        if ($geo) {
            $model->update($id, [
                'lat'         => $geo['lat'],
                'lng'         => $geo['lng'],
                'geocoded_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function getValidationRules(string $tipo, ?int $excludeId = null): array
    {
        $codiceRule = 'required|max_length[15]|is_unique[clienti.codice'
            . ($excludeId ? ",id,{$excludeId}" : '') . ']';

        $rules = [
            'codice' => $codiceRule,
            'tipo'   => 'required|in_list[societa,persona_fisica]',
            'piva'   => 'permit_empty|max_length[15]',
            'cfisc'  => 'permit_empty|max_length[16]',
            'email'  => 'permit_empty|valid_email',
        ];

        if ($tipo === 'persona_fisica') {
            $rules['cognome'] = 'required|max_length[100]';
            $rules['nome']    = 'permit_empty|max_length[100]';
            $rules['ragsoc']  = 'permit_empty|max_length[255]';
        } else {
            $rules['ragsoc']  = 'required|max_length[255]';
            $rules['cognome'] = 'permit_empty|max_length[100]';
            $rules['nome']    = 'permit_empty|max_length[100]';
        }

        return $rules;
    }
}
