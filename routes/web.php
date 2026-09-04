<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\QueueController;
use App\Http\Controllers\Admin\QueueReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SatisfactionSurveyController as AdminSatisfactionSurveyController;
use App\Http\Controllers\Admin\SurveyDashboardController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\TestEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WisudaOrderController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\PublicQueueController;
use App\Http\Controllers\SatisfactionSurveyController;
use App\Http\Controllers\ServiceFormController;
use App\Http\Controllers\WisudaController;
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

Route::get('/wisuda', [WisudaController::class, 'index'])->name('wisuda.index');
Route::get('/wisuda/pesan', [WisudaController::class, 'createOrder'])->name('wisuda.order.create');
Route::post('/wisuda/pesan', [WisudaController::class, 'storeOrder'])
    ->name('wisuda.order.store')
    ->middleware('throttle:10,1');
Route::get('/wisuda/pesanan/{orderCode}', [WisudaController::class, 'showOrder'])->name('wisuda.order.show');
Route::get('/wisuda/pesanan/{orderCode}/pembayaran', [WisudaController::class, 'payment'])->name('wisuda.payment');
Route::post('/wisuda/pesanan/{orderCode}/upload-bukti-pembayaran', [WisudaController::class, 'uploadPaymentProof'])
    ->name('wisuda.payment.upload')
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

            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/submissions', [ReportController::class, 'submissions'])->name('reports.submissions');
            Route::get('/reports/queues', [ReportController::class, 'queues'])->name('reports.queues');
            Route::get('/reports/surveys', [ReportController::class, 'surveys'])->name('reports.surveys');
            Route::get('/reports/combined', [ReportController::class, 'combined'])->name('reports.combined');
            Route::get('/reports/submissions/export', [ReportController::class, 'exportSubmissions'])->name('reports.submissions.export');
            Route::get('/reports/queues/export', [ReportController::class, 'exportQueues'])->name('reports.queues.export');
            Route::get('/reports/surveys/export', [ReportController::class, 'exportSurveys'])->name('reports.surveys.export');
            Route::get('/reports/combined/export', [ReportController::class, 'exportCombined'])->name('reports.combined.export');
            Route::get('/reports/submissions/print', [ReportController::class, 'printSubmissions'])->name('reports.submissions.print');
            Route::get('/reports/queues/print', [ReportController::class, 'printQueues'])->name('reports.queues.print');
            Route::get('/reports/surveys/print', [ReportController::class, 'printSurveys'])->name('reports.surveys.print');
            Route::get('/reports/combined/print', [ReportController::class, 'printCombined'])->name('reports.combined.print');

            Route::get('/wisuda/orders', [WisudaOrderController::class, 'index'])->name('wisuda.orders.index');
            Route::get('/wisuda/orders/export', [WisudaOrderController::class, 'export'])->name('wisuda.orders.export');
            Route::get('/wisuda/orders/print', [WisudaOrderController::class, 'print'])->name('wisuda.orders.print');
            Route::get('/wisuda/orders/{order}', [WisudaOrderController::class, 'show'])->name('wisuda.orders.show');
            Route::get('/wisuda/orders/{order}/download-payment-proof', [WisudaOrderController::class, 'downloadPaymentProof'])->name('wisuda.orders.download-payment-proof');
            Route::get('/wisuda/orders/{order}/preview-payment-proof', [WisudaOrderController::class, 'previewPaymentProof'])->name('wisuda.orders.preview-payment-proof');
            Route::patch('/wisuda/orders/{order}/verify-payment', [WisudaOrderController::class, 'verifyPayment'])->name('wisuda.orders.verify-payment');
            Route::patch('/wisuda/orders/{order}/reject-payment', [WisudaOrderController::class, 'rejectPayment'])->name('wisuda.orders.reject-payment');
            Route::patch('/wisuda/orders/{order}/complete', [WisudaOrderController::class, 'complete'])->name('wisuda.orders.complete');
            Route::patch('/wisuda/orders/{order}/cancel', [WisudaOrderController::class, 'cancel'])->name('wisuda.orders.cancel');

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
