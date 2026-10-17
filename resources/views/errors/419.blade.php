@include('errors.layout', [
    'code' => 419,
    'messages' => [
        'en' => ['title' => 'This page has expired', 'body' => 'For your security, the form expired. Go back, refresh the page and send it again.'],
        'fr' => ['title' => 'Cette page a expiré', 'body' => 'Pour votre sécurité, le formulaire a expiré. Revenez en arrière, actualisez la page et renvoyez-le.'],
    ],
])
