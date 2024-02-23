<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $users = [
        'mail'    => [
            'rules' => 'required|max_length[254]|valid_email|is_unique[pm_users.mail]',
            'errors' => [
                'is_unique' => 'l\'Email, ou le mot de passe ne correspond pas',
                'required' => 'l\'Email est requis',
                'valid_email' => 'Format de l\'email invalide',
            ]
        ],
        'password' => [
            'rules' => 'required|max_length[255]|min_length[10]',
            'errors' => [
                'required' => 'Le mot de passe est requis',
                'min_length' => 'Le mot de passe doit contenir minimum 10 caractères'
            ]
        ],
        'first_name' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Le prénom est requis',
            ]
        ],
        'last_name' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Le nom est requis',
            ]
        ],
    ];

    public array $usersAuth = [
        'mail'    => [
            'rules' => 'required|max_length[254]|valid_email',
            'errors' => [
                'required' => 'l\'Email est requis',
                'valid_email' => 'Format de l\'email invalide',
            ]
        ],
        'password' => [
            'rules' => 'required|max_length[255]|min_length[10]',
            'errors' => [
                'required' => 'Le mot de passe est requis',
                'min_length' => 'Le mot de passe doit contenir minimum 10 caractères'
            ]
        ],
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
}
