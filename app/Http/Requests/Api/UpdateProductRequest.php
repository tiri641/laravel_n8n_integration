<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdateProductRequest extends CreateProductRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //親クラス (CreateProductRequest) で定義された元のバリデーションルール全てを取得
        $rules = parent::rules();

        //array_map(...): 取得したすべてのルール（$rules）に対して反復処理を行い、ルールを更新
        return array_map(function ($rule) {

            //array_merge(['sometimes'], $rule): 各ルール配列の先頭に 'sometimes' ルールを追加
            return array_merge(['sometimes'], $rule);
        }, $rules);
    }
}
