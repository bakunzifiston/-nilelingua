@include('errors.layout', [
    'code' => 503,
    'messages' => [
        'en' => ['title' => 'Back shortly', 'body' => 'The website is being updated. Please try again in a few minutes.'],
        'fr' => ['title' => 'De retour très bientôt', 'body' => 'Le site est en cours de mise à jour. Réessayez dans quelques minutes.'],
    ],
])
