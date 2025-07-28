<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerCert;

Route::middleware('auth')->group(function () {
    // Rotas principais
    Route::get('/', function () {
        return view('index');
    });

    // Rotas de certificados
    Route::prefix('certificados')->group(function () {
        Route::post('/ler-colunas', [ControllerCert::class, 'lerColunasExcel'])->name('certificados.ler-colunas');
        Route::post('/preview', [ControllerCert::class, 'previewCertificado'])->name('certificados.preview');
        Route::post('/gerar', [ControllerCert::class, 'gerarCertificados'])->name('certificados.gerar');
    });

    // Rotas de verificação/download
    Route::get('/verificar_certificado/{hash}', [ControllerCert::class, 'validarCertificado']);
    Route::get('/certificado/{hash}/download', [ControllerCert::class, 'download'])->name('certificados.download');

    // Rotas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';