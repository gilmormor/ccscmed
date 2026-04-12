<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── Rutas públicas (sin autenticación) ───────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('activar', 'Api\AuthApiController@activar');
});

// ── Rutas protegidas con Sanctum ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('auth/logout', 'Api\AuthApiController@logout');
    Route::get('auth/me',     'Api\AuthApiController@me');

    Route::prefix('recibos')->group(function () {
        Route::get('empresas',   'Api\ReportRecEmpApiController@empresas');
        Route::post('periodos',  'Api\ReportRecEmpApiController@periodos');
        Route::get('pdf',        'Api\ReportRecEmpApiController@exportPdf');
        Route::get('constancia', 'Api\ReportRecEmpApiController@constanciaTrabajo');
    });

    Route::prefix('marcaje')->group(function () {
        Route::get('empresa',   'Api\MarcajeApiController@empresa');
        Route::post('registrar','Api\MarcajeApiController@registrar');
        Route::get('hoy',       'Api\MarcajeApiController@hoy');
        Route::get('historial', 'Api\MarcajeApiController@historial');
    });

});
