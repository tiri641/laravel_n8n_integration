<?php

use App\Http\Middleware\ApiAuthenticate; // 作成したカスタムミドルウェア
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ★ 修正点: Artisan コマンド実行中は ArgumentCountError を回避するため、
        // カスタムミドルウェアの登録をスキップします。
        if (! app()->runningInConsole()) {
            $middleware->alias([
                // 'auth' のエイリアスをカスタムクラスに差し替え
                // これにより、APIリクエストでのリダイレクトを停止します。
                'auth' => ApiAuthenticate::class,
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ★ 2. withExceptions: AuthenticationException を401 JSONに変換 ★
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            // リダイレクトがスキップされ、例外がスローされた場合に実行されます。
            if ($request->expectsJson()) {
                // 401 Unauthorized のJSONレスポンスを返す
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], Response::HTTP_UNAUTHORIZED);
            }
            return $e;
        });
    })->create();
