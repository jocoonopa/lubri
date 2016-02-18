<?php

namespace App\Http\Requests\Flap\POS_Member;

use App\Http\Requests\Request;

class ImportContentRequest extends Request
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
            'email'       => 'email',
            'name'        => 'required|min:2|max:6',
            'cellphone'   => 'cellphone|required_without_all:hometel,homeaddress',
            'hometel'     => 'tel|required_without_all:cellphone,homeaddress',
            'officetel'   => 'tel',
            'homeaddress' => 'min:6|max:255|required_without_all:cellphone,hometel',
            'period_at'   => 'date',
            'hospital'    => 'min:2|max:20'
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
        return redirect()->back()->withErrors($errors, $this->errorBag);
    }
}
