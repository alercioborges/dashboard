<?php

return [
    [
        'label' => 'Dashboard',
        'icon'  => 'nav-icon bi bi-speedometer2',
        'route' => 'dashboard',
    ],
    [
        'label' => 'Administração',
        'icon'  => 'nav-icon bi bi-database',
        'children' => [
            [
                'label' => 'Usuários',
                'icon'  => 'nav-icon bi bi-people',
                'children' => [
                    [
                        'label' => 'Contas',
                        'icon'  => 'nav-icon bi bi-person',
                        'children' => [
                            [
                                'label' => 'Listar Usuários',
                                'icon'  => 'nav-icon bi bi-circle',
                                'route' => 'users.show'
                            ],
                            [
                                'label' => 'Adicionar usuário',
                                'icon'  => 'nav-icon bi bi-plus-circle',
                                'route' => 'users.create'
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
