<?php

namespace App\Http\Requests\Flap\PIS_Goods;

use App\Http\Requests\Request;

class CopyToCometrustRequest extends Request
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

    public function forbiddenResponse()
    {
        return response()->view('errors.403', [], 403);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|min:6'
        ];
    }

    public function response(array $errors)
    {
       dd($errors);
    }
}
