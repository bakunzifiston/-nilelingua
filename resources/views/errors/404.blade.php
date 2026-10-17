@include('errors.layout', [
    'code' => 404,
    'messages' => [
        'en' => ['title' => 'Page not found', 'body' => 'The page you are looking for does not exist or has moved. Try the homepage, or tell us what you were looking for.'],
        'fr' => ['title' => 'Page introuvable', 'body' => 'La page que vous cherchez n’existe pas ou a été déplacée. Revenez à l’accueil ou dites-nous ce que vous cherchiez.'],
    ],
])
