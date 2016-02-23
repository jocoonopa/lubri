<?php

namespace App\Http\Requests\Flap\POS_Member;

use App\Http\Requests\Request;

class ImportTaskRequest extends Request
{
    protected $rules = [
        'name'        => 'required|max:100|unique:pos_member_import_task',
        'category'    => 'required',
        'distinction' => 'required',
        'file'        => 'required|max:5000|mimes:xls' //a required, max 5000kb, xls
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
        if ($this->isMethod('put')) {
            unset($this->rules['file']);

            $this->rules['name'] = 'required|max:100|unique:pos_member_import_task,id,' . $this->route('import_task')->id;
        }

        return $this->rules;
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
