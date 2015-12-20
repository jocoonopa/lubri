<?php

namespace App\Http\Requests\Flap\PIS_Goods;

use App\Http\Requests\Request;

class FixCPrefixRequest extends Request
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
            'Codes' => 'required|array'
        ];
    }

    /**
     * If you want to customize what happens on a failed validation,
     * override this method.
     *
     * See what it does natively here:
     * https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/Http/FormRequest.php
     *
     * Much more example can see here:
     * https://mattstauffer.co/blog/laravel-5.0-form-requests
     * 
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response 
     */
    public function response(array $errors)
    {
        return redirect()->route('pis_goods_fix_cprefix_goods_index');
    }
}
