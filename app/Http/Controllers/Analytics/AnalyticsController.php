<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function dashboard(Request $request): JsonResponse
    {
        $stats = $this->analyticsService->getDashboardStats($request->user());

        return response()->json([
            'stats' => $stats,
        ]);
    }

    public function campaign(Request $request, int $id): JsonResponse
    {
        $period   = $request->query('period', 'monthly');
        $insights = $this->analyticsService->getCampaignInsights($id, $request->user(), $period);

        if (empty($insights)) {
            return response()->json(['message' => 'الحملة غير موجودة.'], 404);
        }

        return response()->json(['data' => $insights]);
    }

    public function comparison(Request $request): JsonResponse
    {
        $data = $this->analyticsService->getComparison($request->user());

        return response()->json(['comparison' => $data]);
    }

    public function seedTestData(Request $request): JsonResponse
    {
        $this->analyticsService->seedTestData($request->user());

        return response()->json(['message' => 'تم إنشاء بيانات تجريبية بنجاح.']);
    }
}
