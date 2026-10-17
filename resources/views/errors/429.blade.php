@include('errors.layout', [
    'code' => 429,
    'messages' => [
        'en' => ['title' => 'Too many attempts', 'body' => 'You have sent this form several times in a short period. Please wait a minute and try again.'],
        'fr' => ['title' => 'Trop de tentatives', 'body' => 'Vous avez envoyé ce formulaire plusieurs fois en peu de temps. Patientez une minute, puis réessayez.'],
    ],
])
