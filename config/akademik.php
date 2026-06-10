<?php

return [
    'admin_contact' => [
        'email' => env('ADMIN_CONTACT_EMAIL', 'kuntum.ekawati@poltekkesbanten.ac.id'),
        'whatsapp_url' => env(
            'ADMIN_CONTACT_WHATSAPP_URL',
            'https://api.whatsapp.com/send/?phone=6282117170875&text&type=phone_number&app_absent=0'
        ),
        'whatsapp_label' => 'Hubungi Admin via WhatsApp',
        'help_message' => 'Jika email belum masuk atau mengalami kendala pengunduhan file, silakan hubungi admin layanan akademik.',
    ],
];
