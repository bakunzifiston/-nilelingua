@include('errors.layout', [
    'code' => 403,
    'messages' => [
        'en' => ['title' => 'Access denied', 'body' => 'You do not have permission to view this page.'],
        'fr' => ['title' => 'Accès refusé', 'body' => 'Vous n’avez pas l’autorisation d’accéder à cette page.'],
    ],
])
