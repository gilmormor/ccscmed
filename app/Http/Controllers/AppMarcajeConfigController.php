<?php

namespace App\Http\Controllers;

use App\Models\NmEmpresa;
use App\Models\NmGrupo;
use App\Models\AppMarcajeExento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppMarcajeConfigController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    //  CONFIGURACIÓN DE PERÍMETRO POR EMPRESA
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        can('listar-appconfig-marcaje');

        $empresas = NmEmpresa::orderBy('emp_nombre')->get();
        $grupos   = NmGrupo::orderBy('gru_nombre')->get();

        return view('appconfig.marcaje.index', compact('empresas', 'grupos'));
    }

    public function editEmpresa($emp_codh)
    {
        can('listar-appconfig-marcaje');

        $empresa = NmEmpresa::findOrFail($emp_codh);

        return view('appconfig.marcaje.empresa', compact('empresa'));
    }

    public function updateEmpresa(Request $request, $emp_codh)
    {
        can('listar-appconfig-marcaje');

        $request->validate([
            'marc_valida_perimetro' => 'required|in:0,1',
            'marc_lat'              => 'nullable|numeric|between:-90,90',
            'marc_lng'              => 'nullable|numeric|between:-180,180',
            'marc_radio'            => 'required|integer|min:10|max:5000',
            'marc_tolerancia'       => 'required|integer|min:0|max:500',
        ]);

        $empresa = NmEmpresa::findOrFail($emp_codh);
        $empresa->update([
            'marc_valida_perimetro' => $request->marc_valida_perimetro,
            'marc_lat'              => $request->marc_lat,
            'marc_lng'              => $request->marc_lng,
            'marc_radio'            => $request->marc_radio,
            'marc_tolerancia'       => $request->marc_tolerancia,
        ]);

        return redirect()->route('appconfig.marcaje')
            ->with('success', "Configuración de {$empresa->emp_nombre} actualizada.");
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  EMPLEADOS EXENTOS
    // ──────────────────────────────────────────────────────────────────────────

    public function exentos()
    {
        can('listar-appconfig-marcaje');

        $exentos  = AppMarcajeExento::orderBy('emp_ced')->get();
        $empresas = NmEmpresa::orderBy('emp_nombre')->pluck('emp_nombre', 'emp_codh');
        $tipos    = AppMarcajeExento::$tipos;

        return view('appconfig.marcaje.exentos', compact('exentos', 'empresas', 'tipos'));
    }

    public function storeExento(Request $request)
    {
        can('listar-appconfig-marcaje');

        $request->validate([
            'emp_ced'      => 'required|string|max:20',
            'emp_codh'     => 'nullable|string|max:10',
            'tipo_exento'  => 'required|in:T,J,O',
            'descripcion'  => 'nullable|string|max:255',
        ]);

        // Verificar que la cédula existe en nm_empleados
        $existe = DB::table('nm_empleados')->where('emp_ced', $request->emp_ced)->exists();
        if (!$existe) {
            return back()->withErrors(['emp_ced' => 'La cédula no existe en el sistema de nómina.'])->withInput();
        }

        // Evitar duplicados
        $duplicado = AppMarcajeExento::where('emp_ced', $request->emp_ced)
            ->where('emp_codh', $request->emp_codh ?: null)
            ->exists();
        if ($duplicado) {
            return back()->withErrors(['emp_ced' => 'Este empleado ya está registrado como exento para esa empresa.'])->withInput();
        }

        AppMarcajeExento::create($request->only(['emp_ced', 'emp_codh', 'tipo_exento', 'descripcion']));

        return redirect()->route('appconfig.marcaje.exentos')
            ->with('success', 'Empleado exento registrado correctamente.');
    }

    public function destroyExento($id)
    {
        can('listar-appconfig-marcaje');

        AppMarcajeExento::findOrFail($id)->delete();

        return redirect()->route('appconfig.marcaje.exentos')
            ->with('success', 'Registro eliminado.');
    }
}
