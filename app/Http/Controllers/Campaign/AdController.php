<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\AdSet;
use App\Services\Campaign\AdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function __construct(private AdService $adService) {}

    public function store(Request $request, int $campaignId, int $adSetId): JsonResponse
    {
        $adSet = AdSet::where('id', $adSetId)
                      ->where('campaign_id', $campaignId)
                      ->where('tenant_id', $request->user()->tenant_id)
                      ->first();

        if (!$adSet) {
            return response()->json(['message' => 'مجموعة الإعلانات غير موجودة.'], 404);
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'format'         => 'required|in:image,video,carousel',
            'headline'       => 'nullable|string|max:255',
            'body'           => 'nullable|string',
            'link_url'       => 'nullable|url',
            'image_url'      => 'nullable|url',
            'video_url'      => 'nullable|url',
            'call_to_action' => 'nullable|string',
        ]);

        $ad = $this->adService->create($adSet, $request->user(), $validated);

        return response()->json([
            'message' => 'تم إنشاء الإعلان بنجاح.',
            'ad'      => $ad,
        ], 201);
    }

    public function update(Request $request, int $campaignId, int $adSetId, int $adId): JsonResponse
    {
        $ad = \App\Models\Ad::where('id', $adId)
                            ->where('ad_set_id', $adSetId)
                            ->where('tenant_id', $request->user()->tenant_id)
                            ->first();

        if (!$ad) {
            return response()->json(['message' => 'الإعلان غير موجود.'], 404);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'status'         => 'sometimes|in:ACTIVE,PAUSED',
            'headline'       => 'nullable|string|max:255',
            'body'           => 'nullable|string',
            'link_url'       => 'nullable|url',
            'image_url'      => 'nullable|url',
            'call_to_action' => 'nullable|string',
        ]);

        $ad = $this->adService->update($ad, $validated);

        return response()->json([
            'message' => 'تم تحديث الإعلان.',
            'ad'      => $ad,
        ]);
    }

    public function destroy(Request $request, int $campaignId, int $adSetId, int $adId): JsonResponse
    {
        $ad = \App\Models\Ad::where('id', $adId)
                            ->where('ad_set_id', $adSetId)
                            ->where('tenant_id', $request->user()->tenant_id)
                            ->first();

        if (!$ad) {
            return response()->json(['message' => 'الإعلان غير موجود.'], 404);
        }

        $this->adService->delete($ad);

        return response()->json(['message' => 'تم حذف الإعلان.']);
    }
}
