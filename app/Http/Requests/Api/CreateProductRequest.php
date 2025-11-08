<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => '商品名は必須です',
            'name.max'          => '商品名は255文字以内で入力してください',
            'description.max'   => '商品名説明は1000文字以内で入力してください',
            'price.required'    => '価格は必須です',
            'price.integer'     => '価格は整数で入力してください',
            'price.min'         => '価格は0以上で入力してください',
            'stock.required'    => '在庫数は必須です',
            'stock.integer'     => '在庫数は整数で入力してください',
            'stock.min'         => '在庫数は0以上で入力してください',
            'is_active.boolean' => '商品状態は真偽値で入力してください',
        ];
    }

    //クライアントから不正なデータ(例: nameがない、priceがい負の数が送られる)
    //LaravelがAPIとして適切な形式（JSON）とステータスコード（422）でエラー情報を返すための処理
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
                
            //このレスポンスを返す際に422 Unprocessable EntityというHTTPステータスコードを使うように指示している
            ],Response::HTTP_UNPROCESSABLE_ENTITY) 
        );
    }
}
