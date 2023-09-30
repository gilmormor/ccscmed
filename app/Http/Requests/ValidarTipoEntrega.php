<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarTipoEntrega extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => 'required|max:100|unique:tipoentrega,nombre,' . $this->route('id'),
            'abrev' => 'required|max:5|unique:tipoentrega,abrev,' . $this->route('id'),
            'icono' => 'required|max:30|unique:tipoentrega,icono,' . $this->route('id'),
        ];
    }
}
