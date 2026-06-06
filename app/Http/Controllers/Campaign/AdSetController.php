<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\Campaign\AdSetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdSetController extends Controller
{
    public function __construct(private AdSetService $adSetService) {}

    public function store(Request $request, int $campaignId): JsonResponse
    {
        $campaign = Campaign::where('id', $campaignId)
                            ->where('tenant_id', $request->user()->tenant_id)
                            ->first();

        if (!$campaign) {
            return response()->json(['message' => 'الحملة غير موجودة.'], 404);
        }

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'daily_budget'      => 'nullable|numeric|min:1',
            'lifetime_budget'   => 'nullable|numeric|min:1',
            'targeting'         => 'nullable|array',
            'billing_event'     => 'nullable|string',
            'optimization_goal' => 'nullable|string',
            'start_time'        => 'nullable|date',
            'end_time'          => 'nullable|date',
        ]);

        $adSet = $this->adSetService->create($campaign, $request->user(), $validated);

        return response()->json([
            'message' => 'تم إنشاء مجموعة الإعلانات بنجاح.',
            'ad_set'  => $adSet,
        ], 201);
    }

    public function update(Request $request, int $campaignId, int $adSetId): JsonResponse
    {
        $adSet = \App\Models\AdSet::where('id', $adSetId)
                                  ->where('campaign_id', $campaignId)
                                  ->where('tenant_id', $request->user()->tenant_id)
                                  ->first();

        if (!$adSet) {
            return response()->json(['message' => 'مجموعة الإعلانات غير موجودة.'], 404);
        }

        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'status'            => 'sometimes|in:ACTIVE,PAUSED',
            'daily_budget'      => 'nullable|numeric|min:1',
            'targeting'         => 'nullable|array',
            'optimization_goal' => 'nullable|string',
        ]);

        $adSet = $this->adSetService->update($adSet, $validated);

        return response()->json([
            'message' => 'تم تحديث مجموعة الإعلانات.',
            'ad_set'  => $adSet,
        ]);
    }

    public function destroy(Request $request, int $campaignId, int $adSetId): JsonResponse
    {
        $adSet = \App\Models\AdSet::where('id', $adSetId)
                                  ->where('campaign_id', $campaignId)
                                  ->where('tenant_id', $request->user()->tenant_id)
                                  ->first();

        if (!$adSet) {
            return response()->json(['message' => 'مجموعة الإعلانات غير موجودة.'], 404);
        }

        $this->adSetService->delete($adSet);

        return response()->json(['message' => 'تم حذف مجموعة الإعلانات.']);
    }
}
