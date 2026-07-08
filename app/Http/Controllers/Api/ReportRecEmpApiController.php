<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nm_MovHist;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportRecEmpApiController extends Controller
{
    /**
     * Obtiene la cédula del usuario autenticado vía Sanctum.
     * El campo `usuario` del modelo Usuario almacena la cédula numérica.
     */
    private function cedulaAutenticada(): string
    {
        return auth()->user()->usuario;
    }

    /**
     * GET /api/recibos/empresas
     * Devuelve las empresas donde el empleado autenticado tiene movimientos.
     */
    public function empresas()
    {
        $cedula = $this->cedulaAutenticada();

        $empresas = DB::table('nm_empresa')
            ->join('nm_empleados', 'nm_empresa.emp_codh', '=', 'nm_empleados.emp_codh')
            ->where('nm_empleados.emp_ced', $cedula)
            ->select('nm_empresa.emp_codh', 'nm_empresa.emp_nombre')
            ->groupBy('nm_empresa.emp_codh', 'nm_empresa.emp_nombre')
            ->orderBy('nm_empresa.emp_nombre')
            ->get();

        return response()->json(['data' => $empresas]);
    }

    /**
     * POST /api/recibos/periodos
     * Devuelve los períodos/recibos disponibles para el empleado en una empresa.
     *
     * Body: { emp_codh, cono_monetario, mov_nummon (opcional) }
     */
    public function periodos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_codh'       => 'required',
            'cono_monetario' => 'required|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos incompletos.', 'errors' => $validator->errors()], 422);
        }

        // Inyectar la cédula del usuario autenticado
        $request->merge(['emp_ced' => $this->cedulaAutenticada()]);

        $datas = Nm_MovHist::periodosnompersona($request);

        return response()->json(['data' => $datas]);
    }

    /**
     * GET /api/recibos/pdf
     * Devuelve el PDF del recibo como base64 (para visualizar inline) y como descarga.
     *
     * Query params: emp_codh, mov_nummon, cot_tipo, mov_codcar, mov_codubica
     * Query param opcional: formato=base64 (default) | stream
     */
    public function exportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_codh'    => 'required',
            'mov_nummon'  => 'required',
            'cot_tipo'    => 'required',
            'mov_codcar'  => 'required',
            'mov_codubica'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos incompletos.', 'errors' => $validator->errors()], 422);
        }

        $aux_cedula = $this->cedulaAutenticada();

        $nm_empresa = DB::table('nm_empresa')
            ->select('emp_codh', 'emp_nombre', 'emp_rif', 'logo')
            ->where('emp_codh', $request->emp_codh)
            ->first();

        $nm_movnomtrab = DB::table('nm_movnomtrab')
            ->select('mov_numnom', 'mov_numrec')
            ->where('emp_codh', $request->emp_codh)
            ->where('mov_ced', $aux_cedula)
            ->where('mov_numnom', $request->mov_nummon)
            ->first();

        if (!$nm_movnomtrab) {
            return response()->json(['message' => 'No se encontró el recibo.'], 404);
        }

        $nm_control = DB::table('nm_control')
            ->select('cot_tipo', 'cot_fdesde', 'cot_fhasta')
            ->where('emp_codh', $request->emp_codh)
            ->where('cot_tipo', $request->cot_tipo)
            ->where('cot_numnom', $request->mov_nummon)
            ->first();

        $nm_movhists = DB::table('nm_movhist')
            ->join('nm_conceptos', 'nm_movhist.mov_codcon', '=', 'nm_conceptos.con_cod')
            ->select('nm_movhist.*', DB::raw('TRIM(nm_conceptos.con_desc) as con_desc'))
            ->where('nm_movhist.emp_ced', $aux_cedula)
            ->where('nm_movhist.mov_nummon', $request->mov_nummon)
            ->where('nm_movhist.emp_codh', $request->emp_codh)
            ->get();

        if ($nm_movhists->isEmpty()) {
            return response()->json(['message' => 'No hay datos para este recibo.'], 404);
        }

        $nm_tiponomina = DB::table('nm_tiponomina')
            ->select('tmo_cod', 'tmo_desc')
            ->where('emp_codh', $request->emp_codh)
            ->where('tmo_cod', $request->cot_tipo)
            ->first();

        $nm_cargos = DB::table('nm_cargos')
            ->select('car_desc', 'emp_codh')
            ->where('emp_codh', $request->emp_codh)
            ->where('car_cod', $request->mov_codcar)
            ->first();

        $nm_ubicacion = DB::table('nm_ubicacion')
            ->select('ubi_cod', 'ubi_desc')
            ->where('emp_codh', $request->emp_codh)
            ->where('ubi_cod', $request->mov_codubica)
            ->first();

        $nm_empleado = DB::table('nm_empleados')
            ->where('emp_ced', $aux_cedula)
            ->first();

        $nm_vacproc = null;
        if ($request->cot_tipo == 'V') {
            $nm_vacproc = DB::table('nm_vacproc')
                ->where('emp_codh', $request->emp_codh)
                ->where('vac_ced', $aux_cedula)
                ->where('vac_numrec', $nm_movnomtrab->mov_numrec)
                ->first();
        }

        // Nombre del archivo
        $apellido          = ucwords(strtolower(explode(' ', strtoupper(trim($nm_empleado->emp_ape)))[0]));
        $nombre            = ucwords(strtolower(explode(' ', strtoupper(trim($nm_empleado->emp_nom)))[0]));
        $cedula_formateada = str_pad($nm_empleado->emp_ced, 8, '0', STR_PAD_LEFT);
        $nomach            = 'RecEmp_' . $nm_movnomtrab->mov_numrec . '_' . $cedula_formateada . '_' . $apellido . $nombre;

        $usuario = auth()->user(); // para compatibilidad con la vista Blade existente

        $pdf = PDF::loadView('reportrecemp.listado', compact(
            'nm_control', 'nm_empleado', 'nm_empresa', 'nm_movhists',
            'nm_movnomtrab', 'usuario', 'nm_cargos', 'nm_ubicacion',
            'nm_tiponomina', 'request', 'nm_vacproc'
        ));

        return $this->respuestaPdf($pdf, $nomach, $request->formato ?? 'base64');
    }

    /**
     * GET /api/recibos/constancia
     * Devuelve la constancia de trabajo como PDF.
     *
     * Query params: emp_codh, mov_nummon
     * Query param opcional: formato=base64 (default) | stream
     */
    public function constanciaTrabajo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_codh'   => 'required',
            'mov_nummon' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos incompletos.', 'errors' => $validator->errors()], 422);
        }

        $aux_cedula = $this->cedulaAutenticada();

        $nm_empresa = DB::table('nm_empresa')
            ->select('emp_codh', 'emp_nombre', 'emp_rif', 'emp_nombrefirma', 'ciudad', 'emp_direc', 'emp_telf', 'logo', 'region', 'pais')
            ->where('emp_codh', $request->emp_codh)
            ->first();

        $nm_movnomtrab = DB::table('nm_movnomtrab as m')
            ->join('nm_empleados as e', function ($join) {
                $join->on('m.mov_ced', '=', 'e.emp_ced')
                    ->on('m.emp_codh', '=', 'e.emp_codh')
                    ->where(function ($query) {
                        $query->whereNull('e.emp_fecegre')->orWhere('e.emp_fecegre', '');
                    });
            })
            ->join('nm_cargos as c', function ($join) {
                $join->on('m.mov_codcar', '=', 'c.car_cod')
                    ->on('m.emp_codh', '=', 'c.emp_codh');
            })
            ->select('m.mov_numnom', 'm.mov_numrec', 'm.mov_fecing', 'm.mov_sueldo',
                     'e.emp_ape', 'e.emp_nom', 'e.emp_rif', 'c.car_desc', 'e.emp_ced', 'e.emp_nac')
            ->where('m.emp_codh', $request->emp_codh)
            ->where('m.mov_ced', $aux_cedula)
            ->where('m.mov_numnom', $request->mov_nummon)
            ->first();

        if (!$nm_movnomtrab) {
            return response()->json(['message' => 'No hay datos para esta constancia.'], 404);
        }

        $apellido          = ucwords(strtolower(explode(' ', strtoupper(trim($nm_movnomtrab->emp_ape)))[0]));
        $nombre            = ucwords(strtolower(explode(' ', strtoupper(trim($nm_movnomtrab->emp_nom)))[0]));
        $cedula_formateada = str_pad($nm_movnomtrab->emp_rif, 8, '0', STR_PAD_LEFT);
        $nomach            = 'ConstanciaTrab_' . $cedula_formateada . '_' . $apellido . $nombre;

        $pdf = PDF::loadView('reportrecemp.constanciatrabajo', compact('nm_movnomtrab', 'nm_empresa', 'request'));

        return $this->respuestaPdf($pdf, $nomach, $request->formato ?? 'base64');
    }

    /**
     * Retorna el PDF como base64 (para visualizar en WebView) o como stream de descarga.
     */
    private function respuestaPdf($pdf, string $nomach, string $formato)
    {
        if ($formato === 'stream') {
            return $pdf->stream($nomach . '.pdf');
        }

        // base64: la app puede mostrar inline con WebView y ofrecer descarga
        $contenido = $pdf->output();
        return response()->json([
            'nombre'  => $nomach . '.pdf',
            'base64'  => base64_encode($contenido),
            'mime'    => 'application/pdf',
        ]);
    }
}
