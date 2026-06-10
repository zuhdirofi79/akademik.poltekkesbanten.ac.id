<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\TestEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ServiceFormController;
use App\Support\ServiceFormConfig;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('beranda'));

Route::get('/beranda', [HomeController::class, 'beranda'])->name('beranda');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/tentang', [HomeController::class, 'about'])->name('tentang');

$legacyRedirects = ServiceFormConfig::legacySlugMap();

foreach ($legacyRedirects as $oldSlug => $newSlug) {
    Route::get("/layanan/{$oldSlug}", fn () => redirect("/layanan/{$newSlug}", 301));
}

$serviceRoutes = [
    'perubahan-data-mahasiswa-aktif',
    'surat-keterangan-aktif-kuliah',
    'surat-keterangan-registrasi',
    'surat-cuti-mahasiswa',
    'surat-keterangan-beasiswa',
    'surat-keterangan-berkelakuan-baik',
    'surat-keterangan-verifikasi-ijazah-alumni',
    'surat-rekomendasi',
    'siakad',
];

foreach ($serviceRoutes as $slug) {
    Route::get("/layanan/{$slug}", [ServiceFormController::class, 'show'])
        ->defaults('slug', $slug)
        ->name('services.' . str_replace('-', '_', $slug));
    Route::post("/layanan/{$slug}", [ServiceFormController::class, 'store'])
        ->defaults('slug', $slug)
        ->middleware('throttle:5,1');
}

Route::get('/layanan/{slug}', [ServiceFormController::class, 'show'])->name('services.show');
Route::post('/layanan/{slug}', [ServiceFormController::class, 'store'])
    ->name('services.store')
    ->middleware('throttle:5,1');

Route::get('/pengajuan/sukses/{submission}', [ServiceFormController::class, 'success'])->name('submission.success');

Route::get('/progres-layanan', [ProgressController::class, 'index'])->name('progress.index');
Route::post('/progres-layanan', [ProgressController::class, 'check'])
    ->name('progress.check')
    ->middleware('throttle:10,1');

Route::get('/download/{submission}/{file}', [FileDownloadController::class, 'download'])
    ->name('download.file');
Route::get('/download-signed/{submission}/{file}', [FileDownloadController::class, 'download'])
    ->middleware('signed')
    ->name('download.signed');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit')
        ->middleware('throttle:5,1');

    Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
        Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

        Route::middleware('must.change.password')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
            Route::get('/submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
            Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
            Route::patch('/submissions/{submission}/status', [SubmissionController::class, 'updateStatus'])->name('submissions.status');
            Route::post('/submissions/{submission}/final-files', [SubmissionController::class, 'uploadFinalFile'])->name('submissions.upload-final');
            Route::post('/submissions/{submission}/send-email', [SubmissionController::class, 'sendEmail'])->name('submissions.send-email');

            Route::middleware('role:superadmin')->group(function () {
                Route::resource('study-programs', StudyProgramController::class)->except(['show']);
                Route::get('email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
                Route::get('email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
                Route::put('email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');

                Route::get('/users', [UserController::class, 'index'])->name('users.index');
                Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
                Route::post('/users', [UserController::class, 'store'])->name('users.store');
                Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
                Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
                Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
                Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
                Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

                Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

                Route::get('/test-email', [TestEmailController::class, 'index'])->name('test-email.index');
                Route::post('/test-email', [TestEmailController::class, 'send'])->name('test-email.send');
            });
        });
    });
});
