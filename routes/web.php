<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\QueueController;
use App\Http\Controllers\Admin\QueueReportController;
use App\Http\Controllers\Admin\SatisfactionSurveyController as AdminSatisfactionSurveyController;
use App\Http\Controllers\Admin\SurveyDashboardController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\TestEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PublicQueueController;
use App\Http\Controllers\SatisfactionSurveyController;
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
    'surat-keluar-mahasiswa',
    'surat-keterangan-beasiswa',
    'surat-keterangan-berkelakuan-baik',
    'surat-keterangan-verifikasi-ijazah-alumni',
    'surat-rekomendasi',
    'siakad',
    'surat-tugas-mahasiswa',
    'nota-dinas-dispen-kegiatan-mahasiswa',
    'pengaktifan-kembali-mahasiswa',
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
Route::get('/progres-layanan/hasil', [ProgressController::class, 'result'])->name('progress.result');

Route::get('/survei-kepuasan', [SatisfactionSurveyController::class, 'create'])->name('survey.create');
Route::post('/survei-kepuasan', [SatisfactionSurveyController::class, 'store'])
    ->name('survey.store')
    ->middleware('throttle:10,1');

Route::get('/antrean', [PublicQueueController::class, 'index'])->name('queues.index');
Route::post('/antrean/ambil', [PublicQueueController::class, 'take'])
    ->name('queues.take')
    ->middleware('throttle:queue-take');
Route::get('/antrean/display', [PublicQueueController::class, 'display'])->name('queues.display');
Route::get('/antrean/status', [PublicQueueController::class, 'status'])->name('queues.status');
Route::get('/antrean/kiosk', [PublicQueueController::class, 'kiosk'])->name('queues.kiosk');
Route::get('/antrean/{queue}/print', [PublicQueueController::class, 'print'])->name('queues.print');
Route::get('/antrean/{queue}', [PublicQueueController::class, 'ticket'])->name('queues.ticket');

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

    Route::middleware(['auth', 'role:admin,superadmin,admin_jurusan'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change-password.show');
        Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

        Route::middleware('must.change.password')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
            Route::get('/submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
            Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
            Route::patch('/submissions/{submission}/status', [SubmissionController::class, 'updateStatus'])->name('submissions.status');
            Route::post('/submissions/{submission}/verify-jurusan', [SubmissionController::class, 'verifyJurusan'])->name('submissions.verify-jurusan');
            Route::post('/submissions/{submission}/verify-central', [SubmissionController::class, 'verifyCentral'])->name('submissions.verify-central');
            Route::post('/submissions/{submission}/final-files', [SubmissionController::class, 'uploadFinalFile'])->name('submissions.upload-final');
            Route::post('/submissions/{submission}/send-email', [SubmissionController::class, 'sendEmail'])->name('submissions.send-email');

            Route::get('/surveys', [AdminSatisfactionSurveyController::class, 'index'])->name('surveys.index');
            Route::get('/surveys/dashboard', [SurveyDashboardController::class, 'index'])->name('surveys.dashboard');
            Route::get('/surveys/export', [SurveyDashboardController::class, 'export'])->name('surveys.export');
            Route::get('/surveys/print', [SurveyDashboardController::class, 'print'])->name('surveys.print');

            Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
            Route::get('/queues/status', [QueueController::class, 'status'])->name('queues.status');
            Route::post('/queues/call-next', [QueueController::class, 'callNext'])->name('queues.call-next');
            Route::get('/queues/reports', [QueueReportController::class, 'index'])->name('queues.reports');
            Route::get('/queues/reports/export', [QueueReportController::class, 'export'])->name('queues.reports.export');
            Route::get('/queues/reports/print', [QueueReportController::class, 'print'])->name('queues.reports.print');
            Route::post('/queues/{queue}/call', [QueueController::class, 'call'])->name('queues.call');
            Route::post('/queues/{queue}/serve', [QueueController::class, 'serve'])->name('queues.serve');
            Route::post('/queues/{queue}/finish', [QueueController::class, 'finish'])->name('queues.finish');
            Route::post('/queues/{queue}/skip', [QueueController::class, 'skip'])->name('queues.skip');
            Route::post('/queues/{queue}/cancel', [QueueController::class, 'cancel'])->name('queues.cancel');
            Route::post('/queues/{queue}/note', [QueueController::class, 'note'])->name('queues.note');

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
