@include('errors.layout', [
    'code' => 500,
    'messages' => [
        'en' => ['title' => 'Something went wrong', 'body' => 'An unexpected error occurred on our side. Please try again in a moment, or contact us directly.'],
        'fr' => ['title' => 'Une erreur est survenue', 'body' => 'Une erreur inattendue s’est produite de notre côté. Réessayez dans un instant ou contactez-nous directement.'],
    ],
])
