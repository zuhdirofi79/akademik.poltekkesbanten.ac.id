<?php

$adminContactEmail = env('ADMIN_CONTACT_EMAIL', 'adak.direktorat@poltekesbanten.ac.id');
$adminContactWhatsappUrl = env(
    'ADMIN_CONTACT_WHATSAPP_URL',
    'https://api.whatsapp.com/send/?phone=6282117170875&text&type=phone_number&app_absent=0'
);

return [
    'admin_contact_email' => $adminContactEmail,
    'admin_contact_whatsapp_url' => $adminContactWhatsappUrl,
    'admin_contact' => [
        'email' => $adminContactEmail,
        'whatsapp_url' => $adminContactWhatsappUrl,
        'whatsapp_label' => 'Hubungi Admin via WhatsApp',
        'help_message' => 'Jika email belum masuk atau mengalami kendala pengunduhan file, silakan hubungi admin layanan akademik.',
    ],
];
