<?php

return [
    'label' => 'Siaran',
    'navigation' => 'Siaran',
    'all_users' => 'Semua pengguna',
    'now' => 'Sekarang',
    'open' => 'Buka',

    'actions' => [
        'send' => 'Kirim',
        'resend' => 'Kirim ulang',
    ],

    'section' => [
        'message' => 'Pesan',
        'message_description' => 'Apa yang ingin kamu sampaikan ke pengguna?',
        'audience' => 'Penerima',
        'audience_description' => 'Pilih siapa yang akan menerima siaran ini.',
        'details' => 'Detail',
        'schedule' => 'Jadwal',
        'schedule_description' => 'Kirim sekarang, jadwalkan, atau simpan sebagai draf.',
    ],

    'form' => [
        'title' => 'Judul',
        'roles' => 'Role',
        'users' => 'User',
        'recipients_helper' => 'Kosongkan untuk mengirim ke semua pengguna.',
        'type' => 'Tipe',
        'channel' => 'Kanal',
        'body' => 'Isi',
        'url' => 'URL',
        'url_helper' => 'Opsional. Menambahkan tombol aksi di bawah konten notifikasi.',
        'label' => 'Label tombol',
        'label_helper' => 'Opsional. Teks tombol aksi, misal "Baca selengkapnya".',
        'label_placeholder' => 'Baca selengkapnya',
        'save_as_draft' => 'Simpan sebagai draf',
        'send_now' => 'Kirim sekarang',
        'send_at' => 'Jadwalkan kirim',
        'send_at_helper' => 'Pilih tanggal untuk menjadwalkan siaran.',
    ],

    'type' => [
        'info' => 'Info',
        'warning' => 'Peringatan',
        'success' => 'Sukses',
        'error' => 'Galat',
    ],

    'channel' => [
        'database' => 'Database',
        'mail' => 'Email',
    ],

    'column' => [
        'title' => 'Judul',
        'type' => 'Tipe',
        'status' => 'Status',
        'channels' => 'Kanal',
        'roles' => 'Role',
        'users' => 'User',
        'send_at' => 'Jadwal',
        'sent_at' => 'Terkirim pada',
        'created_at' => 'Dibuat',
    ],

    'status' => [
        'draft' => 'Draf',
        'scheduled' => 'Dijadwalkan',
        'sent' => 'Terkirim',
    ],

    'notifications' => [
        'success' => [
            'title' => 'Siaran dikirim',
            'body' => 'Siaran sedang diproses dan akan segera dikirim ke penerima.',
        ],
        'saved' => [
            'title' => 'Siaran disimpan',
            'body' => 'Siaran kamu sudah disimpan dan dapat dikirim nanti.',
        ],
        'send' => [
            'title' => 'Siaran dikirim',
            'body' => 'Siaran sudah dikirim ke penerimanya.',
        ],
        'resend' => [
            'title' => 'Siaran dikirim ulang',
            'body' => 'Siaran sudah dikirim ulang dengan data terbaru.',
        ],
    ],
];
