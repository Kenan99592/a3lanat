<?php

namespace App\Services\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CampaignService
{
    public function getAll(User $user): LengthAwarePaginator
    {
        return Campaign::where('tenant_id', $user->tenant_id)
                       ->with(['adSets'])
                       ->latest()
                       ->paginate(10);
    }

    public function create(User $user, array $data): Campaign
    {
        return Campaign::create([
            'user_id'         => $user->id,
            'tenant_id'       => $user->tenant_id,
            'meta_account_id' => $data['meta_account_id'] ?? null,
            'name'            => $data['name'],
            'objective'       => $data['objective'],
            'status'          => 'PAUSED',
            'budget_type'     => $data['budget_type'],
            'budget'          => $data['budget'],
            'start_date'      => $data['start_date'] ?? null,
            'end_date'        => $data['end_date'] ?? null,
        ]);
    }

    public function update(Campaign $campaign, array $data): Campaign
    {
        $campaign->update([
            'name'       => $data['name'] ?? $campaign->name,
            'status'     => $data['status'] ?? $campaign->status,
            'budget'     => $data['budget'] ?? $campaign->budget,
            'start_date' => $data['start_date'] ?? $campaign->start_date,
            'end_date'   => $data['end_date'] ?? $campaign->end_date,
        ]);

        return $campaign->fresh();
    }

    public function delete(Campaign $campaign): bool
    {
        $campaign->update(['status' => 'DELETED']);
        return $campaign->delete();
    }

    public function toggleStatus(Campaign $campaign): Campaign
    {
        $newStatus = $campaign->status === 'ACTIVE' ? 'PAUSED' : 'ACTIVE';
        $campaign->update(['status' => $newStatus]);
        return $campaign->fresh();
    }

    public function findForTenant(int $id, User $user): ?Campaign
    {
        return Campaign::where('id', $id)
                       ->where('tenant_id', $user->tenant_id)
                       ->with(['adSets.ads'])
                       ->first();
    }
}
