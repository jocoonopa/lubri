<?php

namespace App\Http\Requests\FV;

use App\Http\Requests\Request;

class FVSyncListRequest extends Request
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
            'eng_emp_codes'    => 'required_without_all:eng_campaign_cds,eng_assign_date,eng_source_cds',
            'eng_campaign_cds' => 'required_without_all:eng_emp_codes,eng_assign_date,eng_source_cds',
            'eng_assign_date'  => 'required_without_all:eng_campaign_cds,eng_emp_codes,eng_source_cds',
            'eng_source_cds'   => 'required_without_all:eng_campaign_cds,eng_assign_date,eng_emp_codes'
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
        return redirect()->back()->withErrors($errors, $this->errorBag)->withInput();
    }
}
