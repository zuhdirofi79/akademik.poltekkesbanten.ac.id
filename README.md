# Layanan Akademik Poltekkes Kemenkes Banten

Website layanan akademik untuk **akademik.poltekkesbanten.ac.id**. Mahasiswa dan alumni dapat mengajukan layanan tanpa login. Login hanya untuk **admin** dan **superadmin**.

## Fitur Utama

- 6 layanan akademik (form publik tanpa login)
- Nomor tiket otomatis (`AKD-YYYY-XXXXXX`) dan token progres aman
- Cek progres layanan dengan NIM/email + tiket/token
- Upload file privat (PDF/JPG/PNG, maks. 5 MB)
- Role **admin** dan **superadmin** dengan hak akses berbeda
- Manajemen admin (superadmin only)
- Audit log aktivitas admin
- Ganti password wajib untuk akun password sementara
- Email otomatis (submit, status berubah, file selesai)
- Scheduler command `academic:send-completed-files` setiap 5 menit
- Rate limit pada submit pengajuan dan cek progres

## Role & Hak Akses

| Fitur | Admin | Superadmin |
|-------|:-----:|:----------:|
| Dashboard | ✓ | ✓ |
| Pengajuan layanan | ✓ | ✓ |
| Export pengajuan | ✓ | ✓ |
| Program Studi | | ✓ |
| Template Email | | ✓ |
| Manajemen Admin | | ✓ |
| Test Email | | ✓ |
| Log Aktivitas | | ✓ |

## Instalasi Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### Setting `.env` — Akun Admin

Password **tidak di-hardcode** di kode. Semua dibaca dari environment (isi password hanya di `.env`, **jangan commit**):

```env
SUPERADMIN_EMAIL=qowiyrofi@poltekkesbanten.ac.id
SUPERADMIN_PASSWORD=RAHASIA

SUPERADMIN_1_EMAIL=kuntum.ekawati@poltekkesbanten.ac.id
SUPERADMIN_1_PASSWORD=RAHASIA

DEFAULT_ADMIN_EMAIL=kuntum.ekawati@poltekkesbanten.ac.id
DEFAULT_ADMIN_PASSWORD=RAHASIA

FORCE_RESET_ADMIN_PASSWORD=false
```

| Variabel | Keterangan |
|----------|------------|
| `SUPERADMIN_EMAIL` | Superadmin utama |
| `SUPERADMIN_PASSWORD` | Password sementara superadmin utama |
| `SUPERADMIN_1_EMAIL` | Superadmin tambahan |
| `SUPERADMIN_1_PASSWORD` | Password sementara superadmin tambahan |
| `DEFAULT_ADMIN_EMAIL` | Admin biasa (jika email sama dengan superadmin, role superadmin yang menang) |
| `DEFAULT_ADMIN_PASSWORD` | Password sementara admin |
| `FORCE_RESET_ADMIN_PASSWORD` | `true` = reset password dari env saat seeder dijalankan |

**Prioritas role:** `superadmin` (2) > `admin` (1). Jika email sama muncul sebagai superadmin dan admin, **tidak ada user dobel** — role final tetap **superadmin** dan tidak pernah di-downgrade.

Contoh hasil dengan konfigurasi di atas:
- `qowiyrofi@poltekkesbanten.ac.id` → superadmin
- `kuntum.ekawati@poltekkesbanten.ac.id` → superadmin (bukan duplikat admin)

> **PENTING:** Password awal wajib diganti setelah login pertama. Sistem menampilkan alert di dashboard jika `must_change_password=true`.

### Migrate & Seed

```bash
php artisan migrate --seed
```

atau hanya seeder admin:

```bash
php artisan db:seed --class=AdminUserSeeder
```

Seeder `AdminUserSeeder`:
- Membaca semua env di atas (tanpa hardcode)
- Menggabungkan email duplikat dengan prioritas role tertinggi
- Password di-hash dengan `Hash::make()`
- User baru: `is_active=true`, `must_change_password=true`
- User existing: **tidak** overwrite password jika `FORCE_RESET_ADMIN_PASSWORD=false`
- Role hanya dinaikkan (admin → superadmin), tidak pernah diturunkan
- Minimal 1 superadmin aktif wajib ada setelah seeder

### Reset Password Awal dari Env

```env
FORCE_RESET_ADMIN_PASSWORD=true
```

```bash
php artisan db:seed --class=AdminUserSeeder
```

Kembalikan segera:

```env
FORCE_RESET_ADMIN_PASSWORD=false
```

> **Jangan biarkan `FORCE_RESET_ADMIN_PASSWORD=true` di production** kecuali sedang maintenance reset password.

### Jalankan Server

```bash
php artisan serve
```

Login admin: `http://localhost:8000/admin/login`

## Ganti Password Awal

Setelah login pertama dengan password sementara, sistem akan mengarahkan ke `/admin/change-password`.

Anda juga dapat mengganti password kapan saja dari menu **Ganti Password** di sidebar admin.

Alternatif via tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'email@poltekkesbanten.ac.id')->first();
$user->password = 'PasswordBaruAnda!';
$user->must_change_password = false;
$user->save();
```

## Konfigurasi Email

### Mode Development — `MAIL_MAILER=log`

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@poltekkesbanten.ac.id"
MAIL_FROM_NAME="Layanan Akademik Poltekkes Kemenkes Banten"
```

**Catatan:** `MAIL_MAILER=log` **tidak mengirim email ke inbox**. Email hanya tercatat di `storage/logs/laravel.log`. Cocok untuk development lokal.

### Mode Production — SMTP

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=ISI_HOST_SMTP_KAMPUS
MAIL_PORT=587
MAIL_USERNAME=email@poltekkesbanten.ac.id
MAIL_PASSWORD=ISI_PASSWORD_SMTP_DARI_ENV
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email@poltekkesbanten.ac.id"
MAIL_FROM_NAME="Layanan Akademik Poltekkes Kemenkes Banten"
```

> **JANGAN** commit file `.env` dan password SMTP asli ke repository. Gunakan `.env.example` sebagai template tanpa password nyata.

## Email Otomatis File Hasil

Command:

```bash
php artisan academic:send-completed-files
```

Fungsi:
- Mencari pengajuan status **Selesai / File Tersedia**
- Memiliki file final dari admin
- `ready_to_send_email=true` dan `email_sent_at=null`
- Mengirim email berisi nama, layanan, tiket, link progres, signed download URL (7 hari)
- Sukses: update `email_sent_at`, status **Terkirim Email**
- Gagal: simpan `email_error`, file tetap aman

## Cron Job (Scheduler)

Daftarkan di server:

```cron
* * * * * cd /path/to/laravel-akademik-poltekkesbanten && php artisan schedule:run >> /dev/null 2>&1
```

Command `academic:send-completed-files` berjalan otomatis setiap 5 menit.

## Deploy ke Hostinger

| Path | Keterangan |
|------|------------|
| `laravel-akademik-poltekkesbanten/` | Source code Laravel |
| `public_html/akademik-files/` | File upload privat |
| `public_html/src/css/` | CSS publik |

```bash
php artisan migrate --force
php artisan db:seed --force
```

Set di `.env` production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://akademik.poltekkesbanten.ac.id
ACADEMIC_FILES_PATH=/home/USER/domains/poltekkesbanten.ac.id/public_html/akademik-files
CSS_URL=https://poltekkesbanten.ac.id/src/css/akademik.css
```

File upload **tidak** perlu `storage:link` — disimpan di disk privat `academic`.

## Route Admin

| Method | URL | Akses |
|--------|-----|-------|
| GET/POST | `/admin/login` | Publik |
| GET/POST | `/admin/change-password` | Admin, Superadmin |
| GET | `/admin/dashboard` | Admin, Superadmin |
| GET | `/admin/submissions` | Admin, Superadmin |
| GET/PATCH/POST | `/admin/submissions/{id}/*` | Admin, Superadmin |
| CRUD | `/admin/study-programs` | Superadmin |
| GET/PUT | `/admin/email-templates` | Superadmin |
| CRUD | `/admin/users` | Superadmin |
| GET | `/admin/activity-logs` | Superadmin |

## Keamanan

- File final di storage privat, download via token progres atau signed URL
- Download **tidak** hanya berdasarkan NIM
- Upload: PDF/JPG/JPEG/PNG, maks. 5 MB
- Rate limit: submit layanan (5/menit), cek progres (10/menit), login admin (5/menit)
- Middleware `auth`, `role`, `must.change.password`
- Audit log: login, ubah status, upload file, kirim email, download file, kelola admin

## Lisensi

Project internal Poltekkes Kemenkes Banten.
