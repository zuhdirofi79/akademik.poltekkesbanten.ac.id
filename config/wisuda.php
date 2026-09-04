<?php

return [
    'payment_title' => env('WISUDA_PAYMENT_TITLE', 'Informasi Pembayaran Toga Wisuda'),
    'payment_instruction' => env(
        'WISUDA_PAYMENT_INSTRUCTION',
        'Informasi nominal dan rekening pembayaran akan dikonfirmasi oleh panitia. Silakan unggah bukti pembayaran jika sudah melakukan pembayaran sesuai arahan panitia.'
    ),
    'payment_account_name' => env('WISUDA_PAYMENT_ACCOUNT_NAME'),
    'payment_account_number' => env('WISUDA_PAYMENT_ACCOUNT_NUMBER'),
    'payment_bank_name' => env('WISUDA_PAYMENT_BANK_NAME'),

    'contact_name' => env('WISUDA_CONTACT_NAME', 'Dias Arliadini (HUMAS)'),
    'contact_phone_primary' => env('WISUDA_CONTACT_PHONE_PRIMARY', '+62 851-8777-8838'),
    'contact_phone_secondary' => env('WISUDA_CONTACT_PHONE_SECONDARY', '+62 877-4112-1505'),
    'contact_wa_primary' => env(
        'WISUDA_CONTACT_WA_PRIMARY',
        'https://api.whatsapp.com/send/?phone=6285187778838&text&type=phone_number&app_absent=0'
    ),
    'contact_wa_secondary' => env(
        'WISUDA_CONTACT_WA_SECONDARY',
        'https://api.whatsapp.com/send/?phone=6287741121505&text&type=phone_number&app_absent=0'
    ),

    'event_year' => '2026/2027',
    'event_date' => '30 September 2026',
    'event_location' => 'Auditorium UIN SMH Banten',
    'order_deadline_note' => 'Pelayanan pemesanan dibuka hingga minimal 10 hari sebelum wisuda dilaksanakan.',
    'service_hours' => '08.00 sampai 17.00 WIB',
];
