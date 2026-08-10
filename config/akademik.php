<?php

$adminContactEmail = env('ADMIN_CONTACT_EMAIL', 'adak.direktorat@poltekesbanten.ac.id');

// Kontak publik lama (bukan akun login) — paksa diganti ke email ADAK.
if (in_array(strtolower((string) $adminContactEmail), [
    'kuntum.ekawati@poltekkesbanten.ac.id',
], true)) {
    $adminContactEmail = 'adak.direktorat@poltekesbanten.ac.id';
}

// Key lama dipertahankan untuk kompatibilitas; jangan dipakai di tampilan publik.
$adminContactWhatsappUrl = env('ADMIN_CONTACT_WHATSAPP_URL', '');

return [
    'admin_contact_email' => $adminContactEmail,
    'admin_contact_whatsapp_url' => $adminContactWhatsappUrl,
    'admin_contact' => [
        'email' => $adminContactEmail,
        'whatsapp_url' => $adminContactWhatsappUrl,
        'whatsapp_label' => '',
        'help_message' => 'Jika terdapat kendala, silakan hubungi admin layanan akademik melalui email.',
    ],
];
