<?php

namespace App\Controllers;

use App\Models\TipoInterventoModel;
use App\Models\InterventoModel;
use App\Models\TecnicoOrarioModel;
use App\Models\TecnicoCompetenzaModel;
use App\Models\UserModel;
use App\Models\ViaggioModel;
use CodeIgniter\Shield\Entities\User;

class Tecnici extends BaseController
{
    private const COLORI_PRESET = [
        '#93c5fd','#fca5a5','#6ee7b7','#fcd34d','#c4b5fd','#f9a8d4',
        '#67e8f9','#fdba74','#5eead4','#a5b4fc','#bef264','#cbd5e1',
    ];

    private function coloriUsati(int $escludiId = 0): array
    {
        $q = (new UserModel())->whereAssegnabile()->select('colore');
        if ($escludiId) {
            $q->where('id !=', $escludiId);
        }
        return array_filter(array_column($q->findAll(), 'colore'));
    }

    public function index(): string
    {
        $users   = new UserModel();
        $tecnici = $users->whereAssegnabile()->orderBy('cognome', 'ASC')->findAll();

        $rows = db_connect()
            ->table('tecnici_competenze tc')
            ->select('tc.tecnico_id, ti.nome')
            ->join('tipi_intervento ti', 'ti.id = tc.tipo_intervento_id')
            ->where('tc.livello >', 1)
            ->orderBy('ti.ordine')
            ->get()->getResultArray();

        $competenzePerTecnico = [];
        foreach ($rows as $row) {
            $competenzePerTecnico[(int) $row['tecnico_id']][] = $row['nome'];
        }

        return view('tecnici/index', [
            'title'               => 'Tecnici',
            'page_title'          => 'Tecnici',
            'competenzePerTecnico' => $competenzePerTecnico,
            'tecnici'    => $tecnici,
        ]);
    }

    public function create(): string
    {
        return view('tecnici/create', [
            'title'        => 'Nuovo Tecnico',
            'page_title'   => 'Nuovo Tecnico',
            'coloriPreset' => self::COLORI_PRESET,
            'coloriUsati'  => $this->coloriUsati(),
            'tipiTutti'    => (new TipoInterventoModel())->orderBy('ordine')->findAll(),
            'livelliLabel' => TecnicoCompetenzaModel::LIVELLI,
        ]);
    }

    public function store()
    {
        $rules = [
            'username'         => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'nome'             => 'required|max_length[100]',
            'cognome'          => 'required|max_length[100]',
            'telefono'         => 'permit_empty|max_length[30]',
            'colore'           => 'required|is_unique[users.colore]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'username'         => ['is_unique' => 'Questo nome utente è già in uso.'],
            'colore'           => ['is_unique' => 'Questo colore è già usato da un altro tecnico.'],
            'password_confirm' => ['matches'   => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');

        $users = new UserModel();
        $user  = new User([
            'username' => $username,
            'active'   => true,
            'nome'     => $this->request->getPost('nome'),
            'cognome'  => $this->request->getPost('cognome'),
            'telefono' => $this->request->getPost('telefono'),
            'colore'              => $this->request->getPost('colore') ?: null,
            'ruolo'               => 'tecnico',
            'richiede_cambio_auto' => (int) ($this->request->getPost('richiede_cambio_auto') ?? 0),
        ]);

        $users->save($user);
        $newId = $users->getInsertID();
        $user  = $users->findById($newId);

        $user->createEmailIdentity([
            'email'    => $username . '@gestionale.colombini-snc.it',
            'password' => $this->request->getPost('password'),
        ]);

        $user->addGroup('tecnico');

        (new TecnicoCompetenzaModel())->salva($newId, $this->request->getPost());

        return redirect()->to('sistema/tecnici/' . $newId)
            ->with('success', 'Tecnico "' . $username . '" creato con successo.');
    }

    public function show(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || ($tecnico->ruolo !== 'tecnico' && ! $tecnico->assegnabile_interventi)) {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $interventi = new InterventoModel();
        $orariModel      = new TecnicoOrarioModel();
        $competenzeModel = new TecnicoCompetenzaModel();
        $tipiModel       = new TipoInterventoModel();

        $competenze = [];
        foreach ($competenzeModel->perTecnico($id) as $c) {
            $competenze[$c['tipo_intervento_id']] = $c['livello'];
        }

        return view('tecnici/show', [
            'title'          => 'Scheda Tecnico',
            'page_title'     => 'Scheda Tecnico',
            'tecnico'        => $tecnico,
            'interventi'     => $interventi->perTecnico($id),
            'tipi'           => TipoInterventoModel::comeLista(),
            'stati'          => InterventoModel::STATI,
            'orari'          => $orariModel->perTecnico($id),
            'giorni'         => TecnicoOrarioModel::GIORNI,
            'orariDefault'   => [
                'ora_inizio'   => setting('Tecnici.orario_inizio')   ?? '08:00',
                'ora_fine'     => setting('Tecnici.orario_fine')     ?? '17:00',
                'pausa_inizio' => setting('Tecnici.pausa_inizio')    ?? '13:00',
                'pausa_fine'   => setting('Tecnici.pausa_fine')      ?? '14:00',
            ],
            'tipiTutti'      => $tipiModel->orderBy('ordine')->findAll(),
            'competenze'     => $competenze,
            'livelliLabel'   => TecnicoCompetenzaModel::LIVELLI,
        ]);
    }

    public function edit(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || ($tecnico->ruolo !== 'tecnico' && ! $tecnico->assegnabile_interventi)) {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        return view('tecnici/edit', [
            'title'        => 'Modifica Tecnico',
            'page_title'   => 'Modifica Tecnico',
            'tecnico'      => $tecnico,
            'coloriPreset' => self::COLORI_PRESET,
            'coloriUsati'  => $this->coloriUsati($id),
        ]);
    }

    public function update(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || ($tecnico->ruolo !== 'tecnico' && ! $tecnico->assegnabile_interventi)) {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $rules = [
            'nome'    => 'required|max_length[100]',
            'cognome' => 'required|max_length[100]',
            'telefono'=> 'permit_empty|max_length[30]',
            'ruolo'   => 'required|in_list[' . implode(',', UserModel::RUOLI_APP) . ']',
            'colore'  => 'required|is_unique[users.colore,id,' . $id . ']',
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $rules['password']         = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        $messages = [
            'colore'           => ['is_unique' => 'Questo colore è già usato da un altro tecnico.'],
            'password_confirm' => ['matches'   => 'Le password non coincidono.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nuovoRuolo = $this->request->getPost('ruolo');

        $users->update($id, [
            'nome'                => $this->request->getPost('nome'),
            'cognome'             => $this->request->getPost('cognome'),
            'telefono'            => $this->request->getPost('telefono') ?: null,
            'colore'              => $this->request->getPost('colore') ?: null,
            'ruolo'               => $nuovoRuolo,
            'richiede_cambio_auto' => (int) ($this->request->getPost('richiede_cambio_auto') ?? 0),
        ]);

        if ($tecnico->ruolo !== $nuovoRuolo) {
            $tecnico->removeGroup($tecnico->ruolo);
            $tecnico->addGroup($nuovoRuolo);
        }

        if ($password) {
            $tecnico->setPassword($password);
            $users->save($tecnico);
        }

        return redirect()->to('sistema/tecnici/' . $id)->with('success', 'Tecnico aggiornato.');
    }

    public function orariUpdate(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || ($tecnico->ruolo !== 'tecnico' && ! $tecnico->assegnabile_interventi)) {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $orariModel = new TecnicoOrarioModel();
        $orariModel->salva($id, $this->request->getPost());

        return redirect()->to('sistema/tecnici/' . $id)->with('success', 'Orari di lavoro aggiornati.');
    }

    public function competenzeUpdate(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || ($tecnico->ruolo !== 'tecnico' && ! $tecnico->assegnabile_interventi)) {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $competenzeModel = new TecnicoCompetenzaModel();
        $competenzeModel->salva($id, $this->request->getPost());

        return redirect()->to('sistema/tecnici/' . $id)->with('success', 'Competenze aggiornate.');
    }

    public function delete(int $id)
    {
        $users   = new UserModel();
        $tecnico = $users->find($id);

        if (! $tecnico || ($tecnico->ruolo !== 'tecnico' && ! $tecnico->assegnabile_interventi)) {
            return redirect()->to('sistema/tecnici')->with('error', 'Tecnico non trovato.');
        }

        $aperti = (new InterventoModel())
            ->where('tecnico_id', $id)
            ->whereNotIn('stato', ['completato', 'annullato'])
            ->countAllResults();

        if ($aperti > 0) {
            return redirect()->to('sistema/tecnici/' . $id)
                ->with('error', "Impossibile eliminare il tecnico: ha {$aperti} intervento/i aperto/i. Riassegna o chiudi gli interventi prima di procedere.");
        }

        $viaggi = (new ViaggioModel())
            ->where('tecnico_id', $id)
            ->countAllResults();

        if ($viaggi > 0) {
            return redirect()->to('sistema/tecnici/' . $id)
                ->with('error', "Impossibile eliminare il tecnico: è collegato a {$viaggi} viaggio/i. Elimina i viaggi associati prima di procedere.");
        }

        $users->delete($id, true);

        return redirect()->to('sistema/tecnici')->with('success', 'Tecnico eliminato.');
    }
}
