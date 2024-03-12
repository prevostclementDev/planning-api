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

    // #####################
    // User model validation
    // #####################
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
        'roles' => [
            'rules' => 'required|in_list[1,2,3]',
            'errors' => [
                'required' => 'Le rôle est requis',
                'in_list' => 'Le role n\'existe pas',
            ]
        ]
    ];

    // #################################
    // Unavailabilities model validation
    // #################################
    public array $unavailabilities = [
        'start_date' => [
            'rules' => 'required|valid_date',
            'errors' => [
                'required' => 'La date de départ est obligatoire.',
                'valid_date' => 'Le format de la date est invalide.'
            ]
        ],
        'id_teacher' => [
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'Il doit avoir une liaison avec un utilisateur valide.',
                'integer' => 'La liaison doit être un integer.',
            ]
        ],
        'end_date' => [
            'rules' => 'required|valid_date',
            'errors' => [
                'required' => 'La date de fin est obligatoire.',
                'valid_date' => 'Le format de la date est invalide.'
            ]
        ],
        'weekday' => [
            'rules' => 'required_with[repetition]|permit_empty|in_list[Sun,Mon,Tue,Wed,Thu,Fri,Sat]',
            'errors' => [
                'required_with' => 'Si "repetition" est défini ce champs doit aussi l\'être.',
                'exact_length' => 'Longueur exact de 3 characters requis'
            ]
        ],
        'repetition_end_date' => [
            'rules' => 'permit_empty|valid_date',
            'errors' => [
                'required_with' => 'Si "repetition" est défini ce champs doit aussi l\'être.',
                'valid_date' => 'Le format de date est invalide.'
            ]
        ],
        'repetition' => [
            'rules' => 'required_with[weekday]|permit_empty|in_list[-1,0,1,2]',
            'errors' => [
                'required_with' => 'Si "weekday" est défini ce champs doit aussi l\'être.',
                'in_list' => 'La valeur doit être comprise dans la liste suivante : -1, 0, 1, 2'
            ]
        ], // -1 pas de répétition, 0 répétition tous les jours, 1 répétition toutes les semaines, 2 répétition tous les mois
    ];

    // #####################
    // Users auth validation
    // #####################
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

    // #######################
    // Course model validation
    // #######################
    public array $courses = [
        'name'    => [
            'rules' => 'required|max_length[254]',
            'errors' => [
                'required' => 'le nom est requis',
                'max_length' => 'Longueur max 254 caractères',
            ]
        ],
        'hours_required'    => [
            'rules' => 'permit_empty|regex_match[/([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]/]',
            'errors' => [
                'regex_match' => 'le nombre d\'heure requis doit répondre au format suivant [0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]'
            ]
        ],
        'color'    => [
            'rules' => 'permit_empty|max_length[254]',
            'errors' => [
                'max_length' => 'Longueur max 254 caractères',
            ]
        ],
    ];

    // ########################
    // Program model validation
    // ########################
    public array $programs = [
        'name'    => [
            'rules' => 'required|max_length[254]',
            'errors' => [
                'required' => 'le nom est requis',
                'max_length' => 'Longueur max 254 caractères',
            ]
        ]
    ];

    // ######################
    // Class model validation
    // ######################
    public array $class = [
        'name'    => [
            'rules' => 'required|max_length[254]',
            'errors' => [
                'required' => 'le nom est requis',
                'max_length' => 'Longueur max 254 caractères',
            ]
        ]
    ];

    // ##########################
    // Classroom model validation
    // ##########################
    public array $classroom = [
        'name'    => [
            'rules' => 'required|max_length[254]',
            'errors' => [
                'required' => 'le nom est requis',
                'max_length' => 'Longueur max 254 caractères',
            ]
        ],
        'capacity' => [
            'rules' => 'permit_empty|numeric',
            'errors' => [
                'numeric' => 'La capacité doit être un chiffre',
            ]
        ]
    ];

    // #########################
    // Planning model validation
    // #########################
    public array $plannings = [
        'name'    => [
            'rules' => 'required|max_length[254]',
            'errors' => [
                'required' => 'le nom est requis',
                'max_length' => 'Longueur max 254 caractères',
            ]
        ],
        'status' => [
            'rules' => 'required|in_list[draft,in_validation,publish]',
            'errors' => [
                'required' => 'le status est requis',
                'in_list' => 'la valeur doit être draft ou in_validation ou publish',
            ]
        ],
        'id_class' => [
            'rules' => 'required|numeric',
            'errors' => [
                'required' => 'Vous devez choisir une classe',
                'numeric' => 'l\'id de la classe doit être valide (numeric)'
            ]
        ],
        'id_programs' => [
            'rules' => 'required|numeric',
            'errors' => [
                'required' => 'Vous devez choisir un programme',
                'numeric' => 'l\'id du programme doit être valide (numeric)'
            ]
        ],
    ];

    public array $planningSlots = [
        'teacher_status' => [
            'rules' => 'required|in_list[valid,invalid,waiting,missing]',
            'errors' => [
                'in_list' => 'la valeur doit être valid ou invalid ou waiting ou missing',
                'required' => 'le status du professeur est requis',
            ]
        ],
        'name'    => [
            'rules' => 'max_length[254]',
            'errors' => [
                'max_length' => 'Longueur max 254 caractères',
            ]
        ],
        'start_hour'    => [
            'rules' => 'required|regex_match[/([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]/]',
            'errors' => [
                'regex_match' => 'le format requis doit répondre au format suivant [0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]',
                'required' => 'l\'heure de début est requis',
            ]
        ],
        'end_hour'    => [
            'rules' => 'required|regex_match[/([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]/]',
            'errors' => [
                'regex_match' => 'le format requis doit répondre au format suivant [0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]',
                'required' => 'l\'heure de fin est requis',
            ]
        ],
        'daydate' => [
            'rules' => 'required|valid_date',
            'errors' => [
                'required' => 'La date est obligatoire.',
                'valid_date' => 'Le format de la date est invalide.'
            ]
        ],
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
}
