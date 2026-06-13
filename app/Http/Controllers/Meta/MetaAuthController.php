<?php

namespace App\Http\Controllers\Meta;

use App\Http\Controllers\Controller;
use App\Services\Meta\MetaAuthService;
use App\Services\Meta\MetaAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetaAuthController extends Controller
{
    public function __construct(
        private MetaAuthService    $metaAuthService,
        private MetaAccountService $metaAccountService
    ) {}

    public function getAuthUrl(Request $request): JsonResponse
    {
        $url = $this->metaAuthService->getAuthUrl();
        return response()->json(['auth_url' => $url]);
    }

    public function callback(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        try {
            $metaAccount = $this->metaAuthService->saveMetaAccount($request->user(), $request->code);
            return response()->json([
                'message'      => 'تم ربط حساب Meta بنجاح.',
                'meta_account' => [
                    'id'           => $metaAccount->id,
                    'account_name' => $metaAccount->account_name,
                    'ad_accounts'  => $metaAccount->ad_accounts,
                    'status'       => $metaAccount->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل ربط الحساب: ' . $e->getMessage()], 500);
        }
    }

    public function handleCallback(Request $request): \Illuminate\Http\RedirectResponse
    {
        $code = $request->query('code');
        if (!$code) {
            return redirect('/dashboard?error=no_code');
        }
        try {
            $this->metaAuthService->saveMetaAccount($request->user(), $code);
            return redirect('/dashboard?success=meta_connected');
        } catch (\Exception $e) {
            return redirect('/dashboard?error=' . urlencode($e->getMessage()));
        }
    }

    public function accounts(Request $request): JsonResponse
    {
        $accounts = $this->metaAccountService->getAccounts($request->user());
        return response()->json(['accounts' => $accounts]);
    }

    public function disconnect(Request $request, int $id): JsonResponse
    {
        $result = $this->metaAccountService->disconnect($request->user(), $id);
        if (!$result) {
            return response()->json(['message' => 'الحساب غير موجود.'], 404);
        }
        return response()->json(['message' => 'تم فصل الحساب بنجاح.']);
    }

    public function refreshAccounts(Request $request, int $id): JsonResponse
    {
        $adAccounts = $this->metaAccountService->refreshAdAccounts($request->user(), $id);
        return response()->json([
            'message'     => 'تم تحديث حسابات الإعلانات.',
            'ad_accounts' => $adAccounts,
        ]);
    }
}
