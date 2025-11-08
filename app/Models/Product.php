<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    //複数代入（Mass Assignment）を許可するモデルの属性を定義するための配列
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_active'
    ];

    //$casts: 
    // モデルの属性を、データベースから取得したりモデルに設定したりする際に、
    // 自動的に指定された型に変換（キャスト）するルールを定義する
    //取得時, 設定時に型をinteger, booleanとする
    protected $casts = [
        'price'     => 'integer',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];
}
