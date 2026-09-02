<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Campos que exige la constancia de honorarios profesionales y que no existían.
 *
 * nm_empresa — constantes jurídicas de la constancia
 *   emp_nombrelegal   Razón social completa del membrete. En la base el nombre
 *                     es "HONORARIOS MEDICOS", que es la denominación de la
 *                     nómina, no la que debe salir en el documento.
 *   emp_sociedad      Sociedad mercantil de la que el médico es socio accionista.
 *   emp_sociedadrif   Su RIF. Es distinto al del membrete: en el modelo aprobado
 *                     el membrete lleva J-09008017-1 y la sociedad J-09002096-9.
 *   emp_titulofirma   Título del firmante ("Lcdo.").
 *   emp_cargofirma    Cargo del firmante ("JEFE ADMINISTRACIÓN").
 *   emp_grupofirma    Grupo al que pertenece ("GRUPO CENTRO CLINICO SAN CRISTOBAL").
 *
 * empresa — datos de contacto para encabezados de reportes
 *   telefono, ciudad, estado, website
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nm_empresa', function (Blueprint $t) {
            if (!Schema::hasColumn('nm_empresa', 'emp_nombrelegal')) {
                $t->string('emp_nombrelegal', 150)->nullable()->after('emp_nombre');
            }
            if (!Schema::hasColumn('nm_empresa', 'emp_sociedad')) {
                $t->string('emp_sociedad', 150)->nullable()->after('emp_nombrelegal');
            }
            if (!Schema::hasColumn('nm_empresa', 'emp_sociedadrif')) {
                $t->string('emp_sociedadrif', 20)->nullable()->after('emp_sociedad');
            }
            if (!Schema::hasColumn('nm_empresa', 'emp_titulofirma')) {
                $t->string('emp_titulofirma', 30)->nullable()->after('emp_nombrefirma');
            }
            if (!Schema::hasColumn('nm_empresa', 'emp_cargofirma')) {
                $t->string('emp_cargofirma', 80)->nullable()->after('emp_titulofirma');
            }
            if (!Schema::hasColumn('nm_empresa', 'emp_grupofirma')) {
                $t->string('emp_grupofirma', 120)->nullable()->after('emp_cargofirma');
            }
        });

        Schema::table('empresa', function (Blueprint $t) {
            if (!Schema::hasColumn('empresa', 'telefono')) {
                $t->string('telefono', 100)->nullable()->after('giro');
            }
            if (!Schema::hasColumn('empresa', 'ciudad')) {
                $t->string('ciudad', 100)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('empresa', 'estado')) {
                $t->string('estado', 100)->nullable()->after('ciudad');
            }
            if (!Schema::hasColumn('empresa', 'website')) {
                $t->string('website', 150)->nullable()->after('estado');
            }
        });

        $this->cargarValoresIniciales();
    }

    /**
     * Valores tomados del modelo de constancia aprobado por la clínica.
     * Solo se rellenan las columnas que estén vacías, para no pisar cambios
     * que ya se hayan hecho desde el panel de administración.
     */
    private function cargarValoresIniciales(): void
    {
        DB::table('nm_empresa')->where('emp_codh', 1)->update([
            'emp_nombrelegal' => DB::raw("COALESCE(NULLIF(TRIM(emp_nombrelegal),''), 'CENTRO CLÍNICO SAN CRISTÓBAL HOSPITAL PRIVADO C.A.')"),
            'emp_sociedad'    => DB::raw("COALESCE(NULLIF(TRIM(emp_sociedad),''), 'CENTRO CLÍNICO SAN CRISTÓBAL C.A')"),
            'emp_sociedadrif' => DB::raw("COALESCE(NULLIF(TRIM(emp_sociedadrif),''), 'J-09002096-9')"),
            'emp_titulofirma' => DB::raw("COALESCE(NULLIF(TRIM(emp_titulofirma),''), 'Lcdo.')"),
            'emp_cargofirma'  => DB::raw("COALESCE(NULLIF(TRIM(emp_cargofirma),''), 'JEFE ADMINISTRACIÓN')"),
            'emp_grupofirma'  => DB::raw("COALESCE(NULLIF(TRIM(emp_grupofirma),''), 'GRUPO CENTRO CLINICO SAN CRISTOBAL')"),
        ]);

        DB::table('empresa')->whereNotNull('id')->update([
            'telefono' => DB::raw("COALESCE(NULLIF(TRIM(telefono),''), '(0276) 340.61.99 / 340.61.00')"),
            'ciudad'   => DB::raw("COALESCE(NULLIF(TRIM(ciudad),''), 'San Cristóbal')"),
            'estado'   => DB::raw("COALESCE(NULLIF(TRIM(estado),''), 'Táchira')"),
            'website'  => DB::raw("COALESCE(NULLIF(TRIM(website),''), 'www.centroclinicosc.com')"),
        ]);
    }

    public function down(): void
    {
        Schema::table('nm_empresa', function (Blueprint $t) {
            foreach (['emp_nombrelegal', 'emp_sociedad', 'emp_sociedadrif',
                      'emp_titulofirma', 'emp_cargofirma', 'emp_grupofirma'] as $col) {
                if (Schema::hasColumn('nm_empresa', $col)) {
                    $t->dropColumn($col);
                }
            }
        });

        Schema::table('empresa', function (Blueprint $t) {
            foreach (['telefono', 'ciudad', 'estado', 'website'] as $col) {
                if (Schema::hasColumn('empresa', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
