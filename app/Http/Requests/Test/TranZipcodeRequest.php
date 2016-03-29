<?php

namespace App\Http\Requests\Test;

use App\Http\Requests\Request;

class TranZipcodeRequest extends Request
{
    protected $rules = [
        'file' => 'required|max:5000|mimes:xls' //a required, max 5000kb, xls
    ];

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
        return $this->rules;
    }

    /**
     * If you want to customize what happens on a failed validation,
     * override this method.
     * 
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response 
     */
    public function response(array $errors)
    {
        return redirect()->back()->withErrors($errors, $this->errorBag)->withInput();
    }
}
