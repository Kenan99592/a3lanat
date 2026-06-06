<?php

namespace App\Services\Campaign;

use App\Models\AdSet;
use App\Models\Campaign;
use App\Models\User;

class AdSetService
{
    public function create(Campaign $campaign, User $user, array $data): AdSet
    {
        return AdSet::create([
            'campaign_id'       => $campaign->id,
            'tenant_id'         => $user->tenant_id,
            'name'              => $data['name'],
            'status'            => 'PAUSED',
            'daily_budget'      => $data['daily_budget'] ?? null,
            'lifetime_budget'   => $data['lifetime_budget'] ?? null,
            'targeting'         => $data['targeting'] ?? [],
            'billing_event'     => $data['billing_event'] ?? 'IMPRESSIONS',
            'optimization_goal' => $data['optimization_goal'] ?? 'REACH',
            'start_time'        => $data['start_time'] ?? null,
            'end_time'          => $data['end_time'] ?? null,
        ]);
    }

    public function update(AdSet $adSet, array $data): AdSet
    {
        $adSet->update([
            'name'              => $data['name'] ?? $adSet->name,
            'status'            => $data['status'] ?? $adSet->status,
            'daily_budget'      => $data['daily_budget'] ?? $adSet->daily_budget,
            'targeting'         => $data['targeting'] ?? $adSet->targeting,
            'optimization_goal' => $data['optimization_goal'] ?? $adSet->optimization_goal,
        ]);

        return $adSet->fresh();
    }

    public function delete(AdSet $adSet): bool
    {
        $adSet->update(['status' => 'DELETED']);
        return $adSet->delete();
    }
}
