<?php

namespace App\Http\Controllers;

use App\Services\ClaudeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IaController extends Controller
{
    /**
     * Tablas permitidas para consultas (solo nm_*)
     * No se permite consultar tablas del sistema.
     */
    protected $tablasPermitidas = [
        'nm_cargos',
        'nm_conceptos',
        'nm_control',
        'nm_empleados',
        'nm_empresa',
        'nm_grupo',
        'nm_movhist',
        'nm_movnomtrab',
        'nm_tiponomina',
        'nm_ubicacion',
        'nm_vacproc',
    ];

    public function index()
    {
        can('listar-ia-asistente');
        return view('ia.index');
    }

    public function preguntar(Request $request)
    {
        $pregunta = trim($request->pregunta);
        if (empty($pregunta)) {
            return response()->json(['error' => 'La pregunta no puede estar vacía.'], 422);
        }

        try {
            $claude  = new ClaudeService();
            $schema  = $this->obtenerSchema();
            $prompt  = $this->construirPrompt($pregunta, $schema);
            $rawResp = $claude->preguntar($prompt);

            // Extraer JSON de la respuesta (Claude puede devolver texto antes/después)
            $json = $this->extraerJson($rawResp);
            if (!$json) {
                return response()->json([
                    'tipo'     => 'texto',
                    'respuesta' => $rawResp,
                ]);
            }

            // Si tiene SQL, validar y ejecutar
            if (!empty($json['sql'])) {
                $validacion = $this->validarSql($json['sql']);
                if ($validacion !== true) {
                    return response()->json(['error' => $validacion], 422);
                }
                $json['datos'] = DB::select($json['sql']);
            }

            return response()->json($json);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al consultar la IA: ' . $e->getMessage()], 500);
        }
    }

    public function exportarExcel(Request $request)
    {
        $sql    = $request->sql;
        $titulo = $request->titulo ?? 'reporte';

        $validacion = $this->validarSql($sql);
        if ($validacion !== true) {
            abort(422, $validacion);
        }

        $datos = DB::select($sql);
        if (empty($datos)) {
            abort(404, 'Sin datos para exportar.');
        }

        $columnas = array_keys((array) $datos[0]);
        $nombreArchivo = $titulo . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($datos, $columnas) {
            $output = fopen('php://output', 'w');
            // BOM para que Excel reconozca UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, $columnas, ';');
            foreach ($datos as $fila) {
                fputcsv($output, (array) $fila, ';');
            }
            fclose($output);
        }, $nombreArchivo, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    // ──────────────────────────────────────────────
    // Métodos privados
    // ──────────────────────────────────────────────

    private function obtenerSchema(): string
    {
        $schema = '';
        foreach ($this->tablasPermitidas as $tabla) {
            try {
                $columnas = DB::select("DESCRIBE `$tabla`");
                $schema  .= "Tabla: $tabla\n";
                foreach ($columnas as $col) {
                    $schema .= "  - {$col->Field} ({$col->Type})\n";
                }
                $schema .= "\n";
            } catch (\Exception $e) {
                // Si la tabla no existe en esta BD, se omite
            }
        }
        return $schema;
    }

    private function construirPrompt(string $pregunta, string $schema): string
    {
        return <<<PROMPT
Eres un asistente de análisis de datos de nómina. Solo puedes consultar las siguientes tablas de base de datos MySQL:

$schema

REGLAS IMPORTANTES:
1. Solo puedes generar consultas SELECT.
2. Solo puedes usar las tablas listadas arriba.
3. Nunca uses DROP, DELETE, UPDATE, INSERT, ALTER, CREATE, TRUNCATE ni ninguna instrucción que modifique datos.
4. Si el usuario pide algo que no requiere datos de la BD, responde solo con texto.

Responde ÚNICAMENTE con un objeto JSON válido, sin texto antes ni después, con esta estructura exacta:

{
  "tipo": "texto" | "grafico" | "tabla" | "excel",
  "sql": "SELECT ... (omitir si no se necesitan datos)",
  "respuesta": "respuesta en texto (solo para tipo texto, o mensaje adicional)",
  "grafico": {
    "tipo_grafico": "bar" | "line" | "pie" | "doughnut",
    "titulo": "Título del gráfico",
    "label_col": "nombre_columna_para_etiquetas",
    "value_col": "nombre_columna_para_valores"
  },
  "titulo_excel": "nombre del archivo sin extensión"
}

Notas sobre los tipos:
- "texto": respuesta conversacional sin datos tabulares
- "grafico": requiere sql + grafico.{tipo_grafico, titulo, label_col, value_col}
- "tabla": requiere sql, muestra los datos en una tabla HTML interactiva
- "excel": requiere sql + titulo_excel, genera un archivo CSV/Excel descargable

Pregunta del usuario: $pregunta
PROMPT;
    }

    private function extraerJson(string $texto): ?array
    {
        // Intentar parsear directamente
        $data = json_decode($texto, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        // Buscar el primer { y el último }
        $inicio = strpos($texto, '{');
        $fin    = strrpos($texto, '}');
        if ($inicio !== false && $fin !== false && $fin > $inicio) {
            $jsonStr = substr($texto, $inicio, $fin - $inicio + 1);
            $data    = json_decode($jsonStr, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        return null;
    }

    private function validarSql(string $sql)
    {
        // Quitar punto y coma al final (Claude los agrega a veces, son válidos pero no necesarios)
        $sql = rtrim(trim($sql), ';');

        $sqlLimpio = preg_replace('/\s+/', ' ', strtoupper($sql));

        // Solo SELECT
        if (!str_starts_with_custom(ltrim($sqlLimpio), 'SELECT')) {
            return 'Solo se permiten consultas SELECT.';
        }

        // Sin instrucciones peligrosas — usar \b para que no coincida como subcadena
        // Ej: "DESC" no debe disparar "DELETE", "DESCRIBE" no debe disparar "DROP"
        $prohibidasKeyword = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'ALTER', 'CREATE', 'TRUNCATE', 'EXEC', 'EXECUTE'];
        foreach ($prohibidasKeyword as $palabra) {
            if (preg_match('/\b' . $palabra . '\b/', $sqlLimpio)) {
                return "Instrucción no permitida detectada: $palabra";
            }
        }

        // Comentarios SQL
        if (strpos($sql, '--') !== false || strpos($sql, '/*') !== false) {
            return 'No se permiten comentarios SQL.';
        }

        // Solo tablas nm_*
        preg_match_all('/(?:FROM|JOIN)\s+`?(\w+)`?/i', $sql, $matches);
        $tablasEnSql = $matches[1] ?? [];
        foreach ($tablasEnSql as $tabla) {
            if (!in_array(strtolower($tabla), $this->tablasPermitidas)) {
                return "La tabla '$tabla' no está permitida.";
            }
        }

        return true;
    }
}

// Helper para PHP < 8
if (!function_exists('str_starts_with_custom')) {
    function str_starts_with_custom(string $haystack, string $needle): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
