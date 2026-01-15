<?php

return [
    'min' => [
        'string' => 'Este campo deve ter pelo menos :min caracteres.',
    ],
    
    'password' => [
        'letters' => 'A palavra-passe deve conter pelo menos uma letra.',
        'mixed' => 'A palavra-passe deve conter letras maiúsculas e minúsculas.',
        'numbers' => 'A palavra-passe deve conter pelo menos um número.',
        'symbols' => 'A palavra-passe deve conter pelo menos um símbolo.',
        'uncompromised' => 'Esta palavra-passe apareceu numa fuga de dados. Por favor, escolha uma diferente.',
    ],
    
    'passwords' => [
        'sent' => 'Enviamos um link de redefinição de palavra-passe para o seu email.',
        'reset' => 'A sua palavra-passe foi redefinida.',
        'throttled' => 'Por favor, aguarde antes de tentar novamente.',
        'token' => 'Este token de redefinição de palavra-passe é inválido.',
        'user' => 'Não encontramos um utilizador com esse endereço de email.',
    ],
];
