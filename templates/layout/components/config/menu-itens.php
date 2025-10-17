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
                'label' => 'Contas',
                'icon'  => 'nav-icon bi bi-people',
                'children' => [
                    [
                        'label' => 'Usuários',
                        'icon'  => 'nav-icon bi bi-person',
                        'children' => [
                            [
                                'label' => 'Listar Usuários',
                                'icon'  => 'nav-icon bi bi-circle',
                                'route' => 'users.show',
                            ],
                            [
                                'label' => 'Adicionar Usuário',
                                'icon'  => 'nav-icon bi bi-plus-circle',
                                'route' => 'users.create',
                            ],
                            [
                                'label' => 'Perfil',
                                'icon'  => 'nav-icon bi bi-person-badge',
                                'route' => 'users.profile',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Permissões',
                        'icon'  => 'nav-icon bi bi-shield-lock',
                        'children' => [
                            [
                                'label' => 'Funções',
                                'icon'  => 'nav-icon bi bi-circle',
                                'route' => 'roles.show',
                            ],
                            [
                                'label' => 'Adicionar Função',
                                'icon'  => 'nav-icon bi bi-plus-circle',
                                'route' => 'roles.create',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Configurações',
                'icon'  => 'nav-icon bi bi-gear',
                'children' => [
                    [
                        'label' => 'Geral',
                        'icon'  => 'nav-icon bi bi-circle',
                        'route' => 'settings.general',
                    ],
                    [
                        'label' => 'Sistema',
                        'icon'  => 'nav-icon bi bi-circle',
                        'route' => 'settings.system',
                    ],
                ],
            ],
        ],
    ],
    [
        'label' => 'Relatórios',
        'icon'  => 'nav-icon bi bi-file-earmark-text',
        'children' => [
            [
                'label' => 'Vendas',
                'icon'  => 'nav-icon bi bi-circle',
                'route' => 'reports.sales',
            ],
            [
                'label' => 'Financeiro',
                'icon'  => 'nav-icon bi bi-circle',
                'route' => 'reports.financial',
            ],
        ],
    ],
];