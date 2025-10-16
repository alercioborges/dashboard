return [
    [
        'label' => 'Dashboard',
        'icon' => 'nav-icon fas fa-tachometer-alt',
        'route' => 'dashboard',
    ],
    [
        'label' => 'Administração',
        'icon' => 'nav-icon fas fa-database',
        'children' => [
            [
                'label' => 'Usuários',
                'icon' => 'far fa-circle nav-icon',
                'children' => [
                    'label' => 'Contas',
                    'icon' => 'nav-icon fas fa-tachometer-alt',
                    'children' => [
                        'label' => 'Lista de usuários',
                        'icon' => 'nav-icon fas fa-tachometer-alt',
                        'route' => 'users.ahow'
                    ],
                    [
                        'label' => 'Lista de usuários',
                        'icon' => 'nav-icon fas fa-tachometer-alt',
                        'route' => 'users.create'
                    ],                
                ].
            ],
            [
                'label' => 'Clientes',
                'icon' => 'far fa-circle nav-icon',
                'route' => 'clients.index',
            ],
            [
                'label' => 'Produtos',
                'icon' => 'far fa-circle nav-icon',
                'children' => [
                    [
                        'label' => 'Listar Produtos',
                        'icon' => 'far fa-dot-circle nav-icon',
                        'route' => 'products.index',
                    ],
                    [
                        'label' => 'Categorias',
                        'icon' => 'far fa-dot-circle nav-icon',
                        'route' => 'categories.index',
                    ],
                ],
            ],
        ],
    ],
    [
        'label' => 'Relatórios',
        'icon' => 'nav-icon fas fa-chart-bar',
        'children' => [
            [
                'label' => 'Vendas',
                'icon' => 'far fa-circle nav-icon',
                'route' => 'reports.sales',
            ],
            [
                'label' => 'Financeiro',
                'icon' => 'far fa-circle nav-icon',
                'route' => 'reports.financial',
            ],
        ],
    ],
    [
        'label' => 'Configurações',
        'icon' => 'nav-icon fas fa-cog',
        'route' => 'settings',
    ],
];