<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Services\Campaign\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(private CampaignService $campaignService) {}

    public function index(Request $request): JsonResponse
    {
        $campaigns = $this->campaignService->getAll($request->user());
        return response()->json(['campaigns' => $campaigns]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meta_account_id' => 'nullable|integer',
            'name'            => 'required|string|max:255',
            'objective'       => 'required|in:AWARENESS,TRAFFIC,ENGAGEMENT,APP_PROMOTION,LEADS,SALES',
            'budget_type'     => 'required|in:daily,lifetime',
            'budget'          => 'required|numeric|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after:start_date',
        ]);

        $campaign = $this->campaignService->create($request->user(), $validated);

        return response()->json([
            'message'  => 'تم إنشاء الحملة بنجاح.',
            'campaign' => $campaign,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $campaign = $this->campaignService->findForTenant($id, $request->user());
        if (!$campaign) {
            return response()->json(['message' => 'الحملة غير موجودة.'], 404);
        }
        return response()->json(['campaign' => $campaign]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $campaign = $this->campaignService->findForTenant($id, $request->user());
        if (!$campaign) {
            return response()->json(['message' => 'الحملة غير موجودة.'], 404);
        }

        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'status'     => 'sometimes|in:ACTIVE,PAUSED',
            'budget'     => 'sometimes|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $campaign = $this->campaignService->update($campaign, $validated);
        return response()->json(['message' => 'تم تحديث الحملة بنجاح.', 'campaign' => $campaign]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $campaign = $this->campaignService->findForTenant($id, $request->user());
        if (!$campaign) {
            return response()->json(['message' => 'الحملة غير موجودة.'], 404);
        }
        $this->campaignService->delete($campaign);
        return response()->json(['message' => 'تم حذف الحملة بنجاح.']);
    }

    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $campaign = $this->campaignService->findForTenant($id, $request->user());
        if (!$campaign) {
            return response()->json(['message' => 'الحملة غير موجودة.'], 404);
        }
        $campaign = $this->campaignService->toggleStatus($campaign);
        return response()->json(['message' => 'تم تغيير حالة الحملة.', 'status' => $campaign->status, 'campaign' => $campaign]);
    }
}
