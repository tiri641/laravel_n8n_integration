<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth; // ★ 依存関係を解決するためにインポート

class ApiAuthenticate extends Authenticate
{
    // ★ 修正点: 親クラスのコンストラクタを継承し、依存関係を注入する ★
    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
    }
    
    /**
     * 未認証時にユーザーがリダイレクトされるべきパスを取得します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        // APIリクエスト（またはJSONを期待するリクエスト）の場合、リダイレクトを停止し、
        // 親クラスが AuthenticationException をスローするように null を返します。
        if ($request->is('api/*') || $request->expectsJson()) {
            return null;
        }

        // Webリクエストの場合は、従来の'login'ルートへのリダイレクトを試みる
        return route('login');
    }
}
