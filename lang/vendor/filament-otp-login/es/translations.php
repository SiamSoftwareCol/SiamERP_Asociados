<?php

return [
    'otp_code' => 'Código OTP',

    'mail' => [
        'subject' => 'Código OTP',
        'greeting' => '¡Hola!',
        'line1' => 'Tu código OTP es: :code',
        'line2' => 'Este código será válido por :seconds segundos.',
        'line3' => 'Si no solicitaste un código, por favor ignora este correo.',
        'salutation' => 'Saludos cordiales, :app_name',
    ],

    'view' => [
        'time_left' => 'segundos restantes',
        'resend_code' => 'Reenviar Código',
        'verify' => 'Verificar',
        'go_back' => 'Regresar',
    ],

    'notifications' => [
        'title' => 'Código OTP Enviado',
        'body' => 'El código de verificación ha sido enviado a tu dirección de correo electrónico. Será válido por :seconds segundos.',
    ],

    'validation' => [
        'invalid_code' => 'El código que ingresaste no es válido.',
        'expired_code' => 'El código que ingresaste ha expirado.',
    ],
];
