<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarNoCAccionCorrectiva;
use App\Http\Requests\ValidarNoCAccionInmediata;
use App\Http\Requests\ValidarNoCAnalisisDeCausa;
use App\Http\Requests\ValidarNoCAprobpaso2;
use App\Http\Requests\ValidarNoCCumplimiento;
use App\Http\Requests\ValidarNoCFechaCompromiso;
use App\Http\Requests\ValidarNoCFechaGuardado;
use App\Http\Requests\ValidarNoCIncumplimiento;
use App\Http\Requests\ValidarNoCobsvalai;
use App\Http\Requests\ValidarPaso4;
use App\Http\Requests\ValidarPaso5;
use App\Mail\MailValidarAccionInmediata;
use App\Models\Certificado;
use App\Models\FormaDeteccionNC;
use App\Models\JefaturaSucursalArea;
use App\Models\MotivoNc;
use App\Models\NoConformidad;
use App\Models\RechazoNC;
use App\Models\RechazoResMedTom;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NoConformidadRecepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        can('listar-recepcion-no-conformidad');
        $usuario = Usuario::with('roles')->findOrFail(auth()->id());
        //$fecha = date("d-m-Y H:i:s",strtotime(noconformidad.fecha . "+ 1 days"));
        $fecha = date("d-m-Y H:i:s");
        //dd($fecha);
        $datas1 = NoConformidad::orderBy('noconformidad.id')
                ->join('noconformidad_responsable', 'noconformidad.id', '=', 'noconformidad_responsable.noconformidad_id')
                ->join('jefatura_sucursal_area', 'noconformidad_responsable.jefatura_sucursal_area_id', '=', 'jefatura_sucursal_area.id')
                ->where('jefatura_sucursal_area.persona_id','=',$usuario->persona->id)
                ->select([
                    'noconformidad.id',
                    'noconformidad.fechahora',
                    'noconformidad.hallazgo'
                    ])
                ->get();
        
        
        /*
        $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
        noconformidad.accioninmediata,accioninmediatafec,
        jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2,
        noconformidad.fechacompromiso,notirecep,notivalai,noticumpl,notiresgi
        FROM noconformidad INNER JOIN noconformidad_responsable
        ON noconformidad.id=noconformidad_responsable.noconformidad_id
        INNER JOIN jefatura_sucursal_area
        ON noconformidad_responsable.jefatura_sucursal_area_id=jefatura_sucursal_area.id
        WHERE jefatura_sucursal_area.persona_id=" . $usuario->persona->id .
        " and noconformidad.deleted_at is null 
        ORDER BY noconformidad.id;";
*/
/*
WHERE jefatura_sucursal_area.persona_id=" .$usuario->persona->id .
" AND (NOW()<=DATE_ADD(fechahora, INTERVAL 1 DAY)
OR (!ISNULL(accioninmediata) and accioninmediata!=''))
*/
/*
        $datas = DB::select($sql);*/
        $datas = consultaSQL(1,$usuario->persona->id);
        //dd($datas);
/*
        $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
        noconformidad.accioninmediata,accioninmediatafec,
        jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2,
        noconformidad.fechacompromiso,notirecep,notivalai,noticumpl,notiresgi
        FROM noconformidad INNER JOIN noconformidad_jefsucarea
        ON noconformidad.id=noconformidad_jefsucarea.noconformidad_id
        INNER JOIN jefatura_sucursal_area
        ON noconformidad_jefsucarea.jefatura_sucursal_area_id=jefatura_sucursal_area.id
        WHERE jefatura_sucursal_area.persona_id=" . $usuario->persona->id .
        " and noconformidad.deleted_at is null 
        ORDER BY noconformidad.id;";

        $arearesps = DB::select($sql); //Area responsable
        */
        $arearesps = consultaSQL(2,$usuario->persona->id);
        //dd($arearesps);


        $motivoncs = MotivoNc::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $formadeteccionncs = FormaDeteccionNC::orderBy('id')->pluck('descripcion', 'id')->toArray();
        $jefaturasucursalareas = JefaturaSucursalArea::orderBy('id')->get();
        $jefaturasucursalareasR = JefaturaSucursalArea::orderBy('id')
                                ->whereNotNull('updated_at')
                                ->get();
        $certificados = Certificado::orderBy('id')->get();
        $usuario_id = $usuario->persona->id;
        $funcvalidarai = '';

        //dd($datas);
        return view('noconformidadrecep.index', compact('datas','arearesps','motivoncs','formadeteccionncs','jefaturasucursalareas','jefaturasucursalareasR','usuario_id','funcvalidarai'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editar($id,$sta_val)
    {
        //can('editar-no-conformidad');
        //dd($sta_val);
        $data = NoConformidad::findOrFail($id);
        $funcvalidarai = $sta_val;
        $directory = "storage/imagenes/noconformidad/";      
        $images = glob($directory . "*.*");
        
        return view('noconformidadrecep.editar',compact('data','funcvalidarai','images'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function buscar(Request $request)
    {
        //dd($request);
        $respuesta = array();
        $respuesta['tablarechazoAI'] = ""; //Tabla Rechazo Acción Inmediata
        $respuesta['tablarechazoMT'] = ""; //Tabla Rechazo Medidas Tomadas
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            
            //dd($noconformidad->jefaturasucursalareas->jefatura->get());
            $jefaturas = array();
            foreach ($noconformidad->jefaturasucursalareas as $jefatura) {
                $jefaturas[] = $jefatura->jefatura->nombre;
            }
            $certificados = array();
            foreach ($noconformidad->certificados as $certificado) {
                $certificados[] = $certificado->descripcion;
            }
            $responsables = array();
            foreach ($noconformidad->jefaturasucursalarearesponsables as $responsable) {
                $responsables[] = $responsable->persona->nombre . " " .$responsable->persona->apellido;
            }
            $feccomp = date("d/m/Y", strtotime( $noconformidad->fechacompromiso));

            $rechazoncs = RechazoNC::where("noconformidad_id","=",$request->id)->get();
            
            //dd($rechazonc);
            //
/*
                        <th>Analisis de Causa</th>
                        <th>Fecha AC</th>
                        <th>Acción Correctiva</th>
                        <th>Fecha AC</th>
                        <th>Fecha Compromiso</th>
                        <th>Fecha C</th>
                        <th>Fecha Guardado</th>
                        <th>Cumplimiento</th>
                        <th>Fecha Cumpl</th>
                        <th>Paso2</th>
                        <th>Fecha Aprob Paso2</th>
                */

            if(count($rechazoncs)>0){
                $respuesta['tablarechazoAI'] .= "
                <div class='table-responsive'>
                <table id='tabla-data' class='table display AllDataTables table-hover table-condensed tablasrech' data-page-length='10'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Accion Inmediata</th>
                        <th>Fecha AI</th>
                    </tr>
                </thead>
                <tbody>";    
                $i = 0;
                foreach ($rechazoncs as $rechazonc) {
                    $respuesta['tablarechazoAI'] .= "
                    <tr>
                        <td>$rechazonc->id</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fecha)) . "</td>
                        <td>$rechazonc->accioninmediata</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->accioninmediatafec)) . "</td>
                    </tr>";
                    $i++;
                }
                $respuesta['tablarechazoAI'] .= "
                </tbody>
                </table>
                </div>";
            }
/*
                        <td>$rechazonc->analisisdecausa</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->analisisdecausafec)) . "</td>
                        <td>$rechazonc->accorrec</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->accorrecfec)) . "</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fechacompromiso)) . "</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fechacompromisofec)) . "</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fechaguardado)) . "</td>
                        <td>$rechazonc->cumplimiento</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fechacumplimiento)) . "</td>
                        <td>$rechazonc->aprobpaso2</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fecaprobpaso2)) . "</td>
*/

            $rechazoncs = RechazoResMedTom::where("noconformidad_id","=",$request->id)->get();
            if(count($rechazoncs)>0){
                $respuesta['tablarechazoMT'] .= "
                <div class='table-responsive'>
                <table id='tabla-data' class='table display AllDataTables table-hover table-condensed tablasrech' data-page-length='10'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>
                <tbody>";    
                $i = 0;
                foreach ($rechazoncs as $rechazonc) {
                    $respuesta['tablarechazoMT'] .= "
                    <tr>
                        <td>$rechazonc->id</td>
                        <td>" . date('d-m-Y', strtotime($rechazonc->fecha)) . "</td>
                        <td>$rechazonc->descripcion</td>
                    </tr>";
                    $i++;
                }
                $respuesta['tablarechazoMT'] .= "
                </tbody>
                </table>
                </div>";
            }
            //dd($respuesta['tabla']);
            return response()->json([
                                        'mensaje' => 'ok',
                                        'noconformidad' => $noconformidad,
                                        'motivonc' => $noconformidad->motivonc->descripcion,
                                        'formadeteccionnc' => $noconformidad->formadeteccionnc->descripcion,
                                        'jefaturas' => $jefaturas,
                                        'certificados' => $certificados,
                                        'responsables' => $responsables,
                                        'feccomp' => $feccomp,
                                        'respuesta' => $respuesta
                                    ]);
        }
    }

    public function actai(ValidarNoCAccionInmediata $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->accioninmediata = $request->accioninmediata;
            $noconformidad->accioninmediatafec = date("Y-m-d H:i:s");
            $noconformidad->usuario_idmp2 = auth()->id();
            $noconformidad->notirecep = 1;

            $noconformidad->stavalai = null;
            $noconformidad->obsvalai = null;
            $noconformidad->fechavalai = null;
            $noconformidad->usuario_idvalai = null;

            if($noconformidad->cumplimiento===0){ //Si es === 0 es porque fue rechazado el cumplimiento de la NC entonces cuando guarda cambia a -1 para la autorizacion de
                $noconformidad->cumplimiento = -1;
            }
            if($noconformidad->aprobpaso2===0){ //Si es === 0 es porque fue rechazada la revision SGI de la NC entonces cuando guarda cambia a -1 para la autorizacion del siguiente valor
                $noconformidad->aprobpaso2 = -1;
            }    

            if ($noconformidad->save()) {
                $sql = "SELECT *
                FROM usuario_rol INNER JOIN usuario
                ON usuario_rol.usuario_id = usuario.id
                WHERE usuario_rol.rol_id=9
                and usuario.deleted_at is null;";
        
                $datas = DB::select($sql);
                $asunto = 'No Conformidad: Validar Acción Inmediata';
                $cuerpo = 'Hola! Has recibido una nueva Validar Acción Inmediata FechaHora: ';
                foreach ($datas as $data1) {
                    Mail::to($data1->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
    
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actobsvalai(ValidarNoCobsvalai $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->stavalai = $request->stavalai;
            $noconformidad->obsvalai = $request->obsvalai;
            $noconformidad->fechavalai = date("Y-m-d H:i:s");
            $noconformidad->usuario_idvalai = auth()->id();
            $noconformidad->notivalai = 1;
            $asunto = 'No Conformidad: Acción Inmediata Validada';
            $cuerpo = 'Hola! Acción Inmediata Validada FechaHora: ';

            if($request->stavalai == "0"){
                $noconformidad->accioninmediata = $noconformidad->accioninmediata . ". " . $request->obsvalai; 
                $noconformidad->notirecep = null;
                $noconformidad->notivalai = null;
                $asunto = 'No Conformidad: Acción Inmediata Rechazada';
                $cuerpo = 'Hola! Acción Inmediata Rechazada FechaHora: ';
            }
            if($noconformidad->cumplimiento===-1){ //Si es === -1 es porque fue aceptada la modificacion de la accion inmediata que habia sido incumplida y pasa al siguiente nivel analisis de causa
                $noconformidad->cumplimiento = -2;
            }
            if($noconformidad->aprobpaso2===-1){ //Si es === -1 es porque fue aceptada la modificacion de la accion inmediata que habia sido rechazada y pasa al siguiente nivel analisis de causa
                $noconformidad->aprobpaso2 = -2;
            }

            if ($noconformidad->save()) {
                foreach($noconformidad->jefaturasucursalarearesponsables as $usuario){
                    Mail::to($usuario->persona->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }

    }

    public function actacausa(ValidarNoCAnalisisDeCausa $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->analisisdecausa = $request->analisisdecausa;
            $noconformidad->analisisdecausafec = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-2){ //Si es === -2 es porque guardo el analisis de causa y pasa al siguiente nivel que es accion correctiva
                $noconformidad->cumplimiento = -3;
            }
            if($noconformidad->aprobpaso2===-2){ //Si es === -2 es porque guardo el analisis de causa y pasa al siguiente nivel que es accion correctiva
                $noconformidad->aprobpaso2 = -3;
            }

            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actacorr(ValidarNoCAccionCorrectiva $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->accorrec = $request->accorrec;
            $noconformidad->accorrecfec = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-3){ //Si es === -3 es porque guardo Accion Correctiva y pasa al siguiente nivel que es fecha de compromiso.
                $noconformidad->cumplimiento = -4;
            }
            if($noconformidad->aprobpaso2===-3){ //Si es === -3 es porque guardo el analisis de causa y pasa al siguiente nivel que es accion correctiva
                $noconformidad->aprobpaso2 = -4;
            }
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actfeccomp(ValidarNoCFechaCompromiso $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $dateInput = explode('/',$request->fechacompromiso);
            $request["fechacompromiso"] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
            $noconformidad->fechacompromiso = $request->fechacompromiso;
            $noconformidad->fechacompromisofec = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-4){ //Si es === -4 es porque guardo fecha de compromiso y pasa al siguiente nivel que es Guardar y terminar edicion de la NC.
                $noconformidad->cumplimiento = -5;
            }
            if($noconformidad->aprobpaso2===-4){ //Si es === -4 es porque guardo fecha de compromiso y pasa al siguiente nivel que es Guardar y terminar edicion de la NC.
                $noconformidad->aprobpaso2 = -5;
            }
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actfechaguardado(ValidarNoCFechaGuardado $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->fechaguardado = date("Y-m-d H:i:s");
            if($noconformidad->cumplimiento===-5){ //Si es === -5 es porque guardo y terminó la edición de la NC y pasa al siguiente nivel que es Validar Cumplimiento que esto lo hace el dueño del la NC .
                $noconformidad->cumplimiento = -6;
            }
            if($noconformidad->aprobpaso2===-5){ //Si es === -5 es porque guardo y terminó la edición de la NC y pasa al siguiente nivel que es Validar Cumplimiento que esto lo hace el dueño del la NC .
                $noconformidad->aprobpaso2 = -6;
            }

            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function actvalai(Request $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->stavalai = $request->stavalai;
            $noconformidad->obsvalai = $request->obsvalai;
            $noconformidad->fechavalai = date("Y-m-d H:i:s");
            $noconformidad->usuario_idvalai = auth()->id();
            if ($noconformidad->save()) {
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function cumplimiento(ValidarNoCCumplimiento $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->cumplimiento = $request->cumplimiento;
            $noconformidad->fechacumplimiento = date("Y-m-d H:i:s");
            $noconformidad->noticumpl = 1;
            if($noconformidad->aprobpaso2===-6){ //Si es === -6 es porque guardo y paso el cumplimiento y pasa al siguiente nivel que es aceptacion o rechaso de revición SGI .
                $noconformidad->aprobpaso2 = -7;
            }
            if ($noconformidad->save()) {
                $sql = "SELECT *
                FROM usuario_rol INNER JOIN usuario
                ON usuario_rol.usuario_id = usuario.id
                WHERE usuario_rol.rol_id=9
                and usuario.deleted_at is null;";
        
                $datas = DB::select($sql);
                $asunto = 'No Conformidad: Cumplimiento Validado';
                $cuerpo = 'Hola! Se Valido Cumplimiento No Conformidad FechaHora ';
                if($request->cumplimiento == "0"){
                    $asunto = 'No Conformidad:  Incumplimiento Validado';
                    $cuerpo = 'Hola! Se Valido Incumplimiento No Conformidad FechaHora: ';
                }

                foreach ($datas as $data1) {
                    Mail::to($data1->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function incumplimiento(ValidarNoCIncumplimiento $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->cumplimiento = 0;
            $noconformidad->fechacumplimiento = date("Y-m-d H:i:s");
            if ($noconformidad->save()) {
                $sql = "SELECT *
                FROM usuario_rol INNER JOIN usuario
                ON usuario_rol.usuario_id = usuario.id
                WHERE usuario_rol.rol_id=9
                and usuario.deleted_at is null;";
                $datas = DB::select($sql);
                $asunto = 'No Conformidad: Incumplimiento Validado';
                $cuerpo = 'Hola! Se Valido Incumplimiento No Conformidad FechaHora: ';
                foreach ($datas as $data1) {
                    Mail::to($data1->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function consvalai(Request $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            return response()->json([
                                        'mensaje' => 'ok',
                                        'noconformidad' => $noconformidad
                                    ]);
        } else {
            abort(404);
        }
    }

    public function aprobpaso2(ValidarNoCAprobpaso2 $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->aprobpaso2 = $request->aprobpaso2;
            $noconformidad->fecaprobpaso2 = date("Y-m-d H:i:s");
            $noconformidad->notiresgi = 1;
            $asunto = 'No Conformidad: Revisión apropada SGI';
            $cuerpo = 'Hola! Revisión apropada SGI FechaHora: ';
            if($request->aprobpaso2 == 0){
                $asunto = 'No Conformidad: Revisión no apropada SGI';
                $cuerpo = 'Hola! Revisión no apropada SGI: ';

                $rechazonc = new RechazoNC;
                $rechazonc->accioninmediata    = $noconformidad->accioninmediata;
                $rechazonc->accioninmediatafec = $noconformidad->accioninmediatafec;
                $rechazonc->analisisdecausa    = $noconformidad->analisisdecausa;
                $rechazonc->analisisdecausafec = $noconformidad->analisisdecausafec;
                $rechazonc->accorrec           = $noconformidad->accorrec;
                $rechazonc->accorrecfec        = $noconformidad->accorrecfec;
                $rechazonc->fechacompromiso    = $noconformidad->fechacompromiso;
                $rechazonc->fechacompromisofec = $noconformidad->fechacompromisofec;
                $rechazonc->fechaguardado      = $noconformidad->fechaguardado;
                $rechazonc->cumplimiento       = $noconformidad->cumplimiento;
                $rechazonc->fechacumplimiento  = $noconformidad->fechacumplimiento;
                $rechazonc->aprobpaso2         = $noconformidad->aprobpaso2;
                $rechazonc->fecaprobpaso2      = $noconformidad->fecaprobpaso2;
                $rechazonc->noconformidad_id   = $request->id;

                $noconformidad->accioninmediata = $request->accioninmediata;
                $noconformidad->analisisdecausa = $request->analisisdecausa;
                $noconformidad->accorrec = $request->accorrec;
                $noconformidad->cumplimiento = null;
                $noconformidad->fechacumplimiento = null;
                $noconformidad->notirecep = null;
                $noconformidad->notivalai = null;
                $noconformidad->noticumpl = null;
                $noconformidad->notiresgi = null;
                $noconformidad->obsvalai = null;
                $noconformidad->stavalai = 0;
            }

            if ($noconformidad->save()) {
                foreach($noconformidad->jefaturasucursalarearesponsables as $usuario){
                    Mail::to($usuario->persona->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
                if($request->aprobpaso2 == 0){
                    $rechazonc->save();
                }
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
/*
    public function notificaciones(Request $request)
    {
        if ($request->ajax()) {
*/


    public function paso4(ValidarPaso4 $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->resmedtom = $request->resmedtom;
            $noconformidad->fecharesmedtom = date("Y-m-d H:i:s");
            $noconformidad->acepresmedtom = $request->acepresmedtom;
            $asunto = 'No Conformidad: Se aceptaron las medidas tomadas y documentos';
            $cuerpo = 'Hola! Se aceptaron las medidas tomadas y documentos: ';
            if($request->acepresmedtom == 0){
                $asunto = 'No Conformidad: No se aceptaron las medidas tomadas y documentos';
                $cuerpo = 'Hola! No se aceptaron las medidas tomadas y documentos: ';
                $rechazoresmedtom = new RechazoResMedTom();
                $rechazoresmedtom->accorrec           = $noconformidad->accorrec;
                $rechazoresmedtom->accorrecfec        = $noconformidad->accorrecfec;
                $rechazoresmedtom->fechacompromiso    = $noconformidad->fechacompromiso;
                $rechazoresmedtom->fechacompromisofec = $noconformidad->fechacompromisofec;
                $rechazoresmedtom->fechaguardado      = $noconformidad->fechaguardado;
                $rechazoresmedtom->cumplimiento       = $noconformidad->cumplimiento;
                $rechazoresmedtom->fechacumplimiento  = $noconformidad->fechacumplimiento;
                $rechazoresmedtom->aprobpaso2         = $noconformidad->aprobpaso2;
                $rechazoresmedtom->fecaprobpaso2      = $noconformidad->fecaprobpaso2;
                $rechazoresmedtom->resmedtom          = $noconformidad->resmedtom;
                $rechazoresmedtom->fecharesmedtom     = $noconformidad->fecharesmedtom;
                $rechazoresmedtom->fecha              = $noconformidad->fecharesmedtom;
                $rechazoresmedtom->descripcion        = $noconformidad->resmedtom;
                $rechazoresmedtom->noconformidad_id   = $request->id;

                $noconformidad->resmedtom = null;
                $noconformidad->fecharesmedtom = null;
                $noconformidad->acepresmedtom = null;
                $noconformidad->fecaprobpaso2 = null;
                $noconformidad->aprobpaso2 = null;
                $noconformidad->fechacumplimiento = null;
                $noconformidad->cumplimiento = null;
                $noconformidad->fechaguardado = null;
                $noconformidad->fechacompromisofec = null;
                $noconformidad->fechacompromiso = null;
                $noconformidad->adjaccorrec = null;
                $noconformidad->accorrecfec = null;
                $noconformidad->accorrec = null;
                $noconformidad->noticumpl = null;
                $noconformidad->notiresgi = null;
            }

            if ($noconformidad->save()) {
                foreach($noconformidad->jefaturasucursalarearesponsables as $usuario){
                    Mail::to($usuario->persona->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
                if($request->acepresmedtom == 0){
                    $rechazoresmedtom->save();
                }
                return response()->json(['mensaje' => 'ok']);
            } else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }

    public function paso5(ValidarPaso5 $request)
    {
        if ($request->ajax()) {
            $noconformidad = NoConformidad::findOrFail($request->id);
            $noconformidad->cierreaccorr = $request->cierreaccorr;
            $noconformidad->feccierreaccorr = date("Y-m-d H:i:s");
            $asunto = 'No Conformidad: Cierre de la eficacia de la accion correctiva';
            $cuerpo = 'Hola! Cierre de la eficacia de la accion correctiva: ';
            if ($noconformidad->save()) {
                foreach($noconformidad->jefaturasucursalarearesponsables as $usuario){
                    Mail::to($usuario->persona->email)->send(new MailValidarAccionInmediata($noconformidad,$asunto,$cuerpo));
                }
                return response()->json(['mensaje' => 'ok']);
            }else {
                return response()->json(['mensaje' => 'ng']);
            }
        } else {
            abort(404);
        }
    }
}


function consultaSQL($idconsulta,$usuario_id){
    switch ($idconsulta) {
        case 1:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
            noconformidad.accioninmediata,accioninmediatafec,
            jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2,
            noconformidad.fechacompromiso,notirecep,notivalai,noticumpl,notiresgi,
            noconformidad.fechaguardado
            FROM noconformidad INNER JOIN noconformidad_responsable
            ON noconformidad.id=noconformidad_responsable.noconformidad_id
            INNER JOIN jefatura_sucursal_area
            ON noconformidad_responsable.jefatura_sucursal_area_id=jefatura_sucursal_area.id
            WHERE jefatura_sucursal_area.persona_id=" . $usuario_id .
            " and noconformidad.deleted_at is null 
            ORDER BY noconformidad.id;";
        break;
        case 2:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
            noconformidad.accioninmediata,accioninmediatafec,
            jefatura_sucursal_area.persona_id,usuario_idmp2,noconformidad.cumplimiento,noconformidad.aprobpaso2,
            noconformidad.fechacompromiso,notirecep,notivalai,noticumpl,notiresgi
            FROM noconformidad INNER JOIN noconformidad_jefsucarea
            ON noconformidad.id=noconformidad_jefsucarea.noconformidad_id
            INNER JOIN jefatura_sucursal_area
            ON noconformidad_jefsucarea.jefatura_sucursal_area_id=jefatura_sucursal_area.id
            WHERE jefatura_sucursal_area.persona_id=" . $usuario_id .
            " and noconformidad.deleted_at is null 
            ORDER BY noconformidad.id;";
        break;
        case 3:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
                noconformidad.accioninmediata,accioninmediatafec,stavalai,
                usuario_idmp2
                FROM noconformidad
                WHERE !(accioninmediata is null) 
                and isnull(noconformidad.notivalai)
                and noconformidad.deleted_at is null 
                ORDER BY noconformidad.id;";
        break;
        case 4:
            $sql = "SELECT noconformidad.id,noconformidad.fechahora,DATE_ADD(fechahora, INTERVAL 1 DAY) AS cfecha,noconformidad.hallazgo,
                noconformidad.accioninmediata,accioninmediatafec,stavalai,notivalai,
                usuario_idmp2,cumplimiento,aprobpaso2
                FROM noconformidad
                WHERE !(accioninmediata is null) 
                and noconformidad.deleted_at is null 
                ORDER BY noconformidad.id;";
        break;
    }

    $datas = DB::select($sql);
    return $datas;

    
}