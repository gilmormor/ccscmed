<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidarArea extends FormRequest
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
            'nombre' => 'required|max:100|unique:area,nombre,' . $this->route('id'),
            'abrev' => 'required|max:5|unique:area,abrev,' . $this->route('id'),
            'descripcion' => 'required|max:200'
        ];
    }
}
