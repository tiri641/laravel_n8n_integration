<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class N8nController extends Controller
{
    /**
     * リクエストからkeywordを受け取り、環境変数から取得したapplication_idと共に
     * n8nのWebhookにJSONで送信します。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerN8nWorkflow(Request $request): JsonResponse
    {
        // 1. 環境変数から必要な設定値を取得
        $n8nWebhookUrl = env('N8N_WEBHOOK_URL');
        $applicationId = env('RAKUTEN_APPLICATION_ID'); 

        // 2. リクエストからkeywordを検証・取得
        try {
            $validated = $request->validate([
                'keyword' => 'required|string|max:100',
            ]);
        } catch (ValidationException $e) {
            // バリデーションエラーは422 (Unprocessable Entity) で返します
            return response()->json(['message' => 'Validation Failed.', 'errors' => $e->errors()], 422);
        }

        // 3. n8nに送信するJSONペイロードを準備
        // application_id は環境変数から取得した変数 ($applicationId) を使用
        $payload = [
            'application_id' => $applicationId,
            'keyword' => $validated['keyword'],
        ];

        try {
            // 4. n8nのWebhook URLにPOSTリクエストを送信
            // throw() により、4xx/5xx ステータスコードの場合は RequestException がスローされます
            $response = Http::timeout(30)
                            ->throw() 
                            ->post($n8nWebhookUrl, $payload);

            // 成功した場合 (200番台)
            return response()->json([
                'message' => 'n8n workflow successfully triggered.',
                'sent_payload' => $payload,
                'n8n_response' => $response->json() ?? $response->body(),
            ], 200);

        } catch (RequestException $e) {
            // 4xx/5xxエラーが発生した場合
            $status = $e->response?->status() ?? 500;
            $body = $e->response?->body() ?? 'No response body.';

            logger()->error("n8n Webhook Error: Status {$status}. Body: {$body}.");

            // 外部サービスのエラーをクライアントに返す
            return response()->json([
                'message' => 'Failed to trigger n8n workflow. Received error status.',
                'n8n_status' => $status,
                'n8n_error_response' => $body,
            ], $status >= 400 && $status < 500 ? 400 : 500);

        } catch (Exception $e) {
            // ネットワークエラー、タイムアウトなどが発生した場合
            logger()->error('n8n HTTP connection error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred during the HTTP connection to n8n.',
                'error_details' => $e->getMessage(),
            ], 503); // 503 Service Unavailable
        }
    }
}