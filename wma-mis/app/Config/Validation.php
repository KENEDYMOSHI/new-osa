<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;
use App\Validation\CustomRules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        CustomRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    public $login = [
        // 'username' => [
        //     'label' =>  'Auth.username',
        //     'rules' => 'required|max_length[30]|min_length[3]|regex_match[/\A[a-zA-Z0-9\.]+\z/]',
        // ],
        'email' => [
            'label' =>  'Auth.email',
            'rules' => 'required|max_length[37]|valid_email',
        ],
        'password' => [
            'label' =>  'Auth.password',
            'rules' => 'required|max_length[20]',
        ],
    ];

    public $registration = [
        'first_name' => [
            'label' => 'First Name',
            'rules' => [
                'required',
            ],
            'errors' => [
                'required' => 'This Field is Required'
            ]
        ],
        'last_name' => [
            'label' => 'Last Name',
            'rules' => [
                'required',
            ],
            'errors' => [
                'required' => 'This Field is Required'
            ]
        ],
        'email' => [
            'label' => 'Email',
            'rules' => [
                'required',
                'max_length[32]',
                'valid_email',
                'is_unique[auth_identities.secret]',
            ],
            'errors' => [
                'required' => 'This Field is Required',
                'is_unique' => 'Email Is Already Taken'
            ]
        ],
        'collection_center' => [
            'label' => 'collection Center',
            'rules' => 'required',
            'errors' => [
                'required' => 'This Field is Required'
            ]
        ],


        'userGroup' => [
            'label' => 'userGroup',
            'rules' => 'required',
            'errors' => [
                'required' => 'This Field is Required'
            ]

        ]
    ];
}
