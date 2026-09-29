<?php

namespace App\Http\Controllers;

/**
 * Generador de clave SPG Key.
 *
 * Replica en el navegador el cálculo que hace el sistema local en Visual
 * FoxPro 8:
 *
 *   aux_resul = producto de los códigos ASCII de cada carácter del Serial
 *   aux_clave = ROUND(aux_resul / (día × mes × Código1 × Código2), 0)
 *
 * Día y mes salen de la Fecha de Instalación. El cálculo es aritmética
 * pura sin datos sensibles, así que se hace en JS y no hay endpoint aparte:
 * index() solo entrega la fecha del SERVIDOR (no la del navegador, que el
 * usuario podría adelantar o atrasar) para que día/mes no se puedan alterar
 * desde el cliente.
 */
class SpgKeyController extends Controller
{
    public function index()
    {
        can('listar-spgkey');

        $hoy = now();

        return view('spgkey.index', [
            'fechaTexto' => $hoy->format('d-m-Y'),
            'dia'        => (int) $hoy->format('j'),
            'mes'        => (int) $hoy->format('n'),
        ]);
    }
}
