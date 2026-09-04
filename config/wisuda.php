<?php

return [
    'payment_title' => env('WISUDA_PAYMENT_TITLE', 'Informasi Pembayaran Toga Wisuda'),
    'payment_instruction' => env(
        'WISUDA_PAYMENT_INSTRUCTION',
        'Untuk informasi rekening pembayaran, silakan hubungi admin HUMAS. Setelah mendapat informasi rekening dan melakukan pembayaran, unggah bukti pembayaran di halaman ini.'
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
    'order_deadline_note' => 'Pelayanan dibuka hingga minimal 10 hari sebelum wisuda dilaksanakan.',
    'service_hours' => '08.00 sampai 17.00 WIB',

    /*
    |--------------------------------------------------------------------------
    | Galeri produk resmi (file lokal di public/src/img/wisuda)
    |--------------------------------------------------------------------------
    */
    'product_images' => [
        [
            'file' => 'Baju-Toga-Mahasiswa.jpeg',
            'label' => 'Baju Toga Mahasiswa',
            'alt' => 'Baju Toga Mahasiswa Poltekkes Kemenkes Banten',
        ],
        [
            'file' => 'Topi-Toga-Segi-Lima.jpeg',
            'label' => 'Topi Toga Segi Lima',
            'alt' => 'Topi Toga Segi Lima Poltekkes Kemenkes Banten',
        ],
        [
            'file' => 'sleber-kerah-wisuda.jpeg',
            'label' => 'Slebber / Kerah Wisuda',
            'alt' => 'Slebber Kerah Wisuda Poltekkes Kemenkes Banten',
        ],
        [
            'file' => 'kerah-dan-kalung-wisuda-keperawatan.jpeg',
            'label' => 'Kerah & Kalung Keperawatan',
            'alt' => 'Kerah dan Kalung Wisuda Keperawatan',
            'prodi' => 'keperawatan',
        ],
        [
            'file' => 'kerah-dan-kalung-wisuda-kebidanan.jpeg',
            'label' => 'Kerah & Kalung Kebidanan',
            'alt' => 'Kerah dan Kalung Wisuda Kebidanan',
            'prodi' => 'kebidanan',
        ],
        [
            'file' => 'kerah-dan-kalung-wisuda-tlm.jpeg',
            'label' => 'Kerah & Kalung TLM',
            'alt' => 'Kerah dan Kalung Wisuda TLM',
            'prodi' => 'tlm',
        ],
        [
            'file' => 'kalung-wisuda-keperawatan.jpeg',
            'label' => 'Kalung Keperawatan',
            'alt' => 'Kalung Wisuda Keperawatan',
            'prodi' => 'keperawatan',
        ],
        [
            'file' => 'kalung-wisuda-kebidanan.jpeg',
            'label' => 'Kalung Kebidanan',
            'alt' => 'Kalung Wisuda Kebidanan',
            'prodi' => 'kebidanan',
        ],
        [
            'file' => 'kalung-wisuda-tlm.jpeg',
            'label' => 'Kalung TLM',
            'alt' => 'Kalung Wisuda TLM',
            'prodi' => 'tlm',
        ],
    ],

    'prodi_variants' => [
        'keperawatan' => [
            'label' => 'Keperawatan',
            'note' => 'Atribut kerah dan kalung sesuai ketentuan Jurusan Keperawatan.',
            'primary' => 'kerah-dan-kalung-wisuda-keperawatan.jpeg',
            'secondary' => 'kalung-wisuda-keperawatan.jpeg',
        ],
        'kebidanan' => [
            'label' => 'Kebidanan',
            'note' => 'Atribut kerah dan kalung sesuai ketentuan Jurusan Kebidanan.',
            'primary' => 'kerah-dan-kalung-wisuda-kebidanan.jpeg',
            'secondary' => 'kalung-wisuda-kebidanan.jpeg',
        ],
        'tlm' => [
            'label' => 'TLM',
            'note' => 'Atribut kerah dan kalung sesuai ketentuan Jurusan Teknologi Laboratorium Medis.',
            'primary' => 'kerah-dan-kalung-wisuda-tlm.jpeg',
            'secondary' => 'kalung-wisuda-tlm.jpeg',
        ],
    ],
];
