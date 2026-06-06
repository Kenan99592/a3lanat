<?php

namespace App\Services\Campaign;

use App\Models\Ad;
use App\Models\AdSet;
use App\Models\User;

class AdService
{
    public function create(AdSet $adSet, User $user, array $data): Ad
    {
        return Ad::create([
            'ad_set_id'      => $adSet->id,
            'tenant_id'      => $user->tenant_id,
            'name'           => $data['name'],
            'status'         => 'PAUSED',
            'format'         => $data['format'] ?? 'image',
            'headline'       => $data['headline'] ?? null,
            'body'           => $data['body'] ?? null,
            'link_url'       => $data['link_url'] ?? null,
            'image_url'      => $data['image_url'] ?? null,
            'video_url'      => $data['video_url'] ?? null,
            'call_to_action' => $data['call_to_action'] ?? 'LEARN_MORE',
        ]);
    }

    public function update(Ad $ad, array $data): Ad
    {
        $ad->update([
            'name'           => $data['name'] ?? $ad->name,
            'status'         => $data['status'] ?? $ad->status,
            'headline'       => $data['headline'] ?? $ad->headline,
            'body'           => $data['body'] ?? $ad->body,
            'link_url'       => $data['link_url'] ?? $ad->link_url,
            'image_url'      => $data['image_url'] ?? $ad->image_url,
            'call_to_action' => $data['call_to_action'] ?? $ad->call_to_action,
        ]);

        return $ad->fresh();
    }

    public function delete(Ad $ad): bool
    {
        $ad->update(['status' => 'DELETED']);
        return $ad->delete();
    }
}
