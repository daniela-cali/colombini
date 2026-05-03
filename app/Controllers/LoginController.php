<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Controllers\LoginController as ShieldLoginController;

class LoginController extends ShieldLoginController
{
    public function loginView()
    {
        if (auth()->loggedIn()) {
            return auth()->user()->inGroup('cliente')
                ? redirect()->to(base_url('portale'))
                : redirect()->to(base_url('/'));
        }

        return parent::loginView();
    }

    protected function getValidationRules(): array
    {
        return [
            'username' => [
                'label'  => 'Auth.username',
                'rules'  => ['required', 'min_length[3]', 'max_length[30]'],
            ],
            'password' => [
                'label'  => 'Auth.password',
                'rules'  => ['required'],
            ],
        ];
    }

    public function loginAction(): RedirectResponse
    {
        $response = parent::loginAction();

        if (auth()->loggedIn() && auth()->user()->inGroup('cliente')) {
            return redirect()->to(base_url('portale'));
        }

        return $response;
    }
}
