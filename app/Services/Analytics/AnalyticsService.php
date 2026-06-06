<?php

namespace App\Services\Analytics;

use App\Models\Campaign;
use App\Models\CampaignInsight;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardStats(User $user): array
    {
        $tenantId = $user->tenant_id;

        $totals = CampaignInsight::where('tenant_id', $tenantId)
            ->selectRaw('
                SUM(impressions) as total_impressions,
                SUM(reach) as total_reach,
                SUM(clicks) as total_clicks,
                SUM(spend) as total_spend,
                AVG(ctr) as avg_ctr,
                AVG(cpm) as avg_cpm,
                AVG(cpc) as avg_cpc
            ')
            ->first();

        $activeCampaigns = Campaign::where('tenant_id', $tenantId)
            ->where('status', 'ACTIVE')
            ->count();

        $totalCampaigns = Campaign::where('tenant_id', $tenantId)
            ->count();

        return [
            'campaigns' => [
                'total'  => $totalCampaigns,
                'active' => $activeCampaigns,
            ],
            'totals' => [
                'impressions' => (int) ($totals->total_impressions ?? 0),
                'reach'       => (int) ($totals->total_reach ?? 0),
                'clicks'      => (int) ($totals->total_clicks ?? 0),
                'spend'       => round($totals->total_spend ?? 0, 2),
            ],
            'averages' => [
                'ctr' => round($totals->avg_ctr ?? 0, 4),
                'cpm' => round($totals->avg_cpm ?? 0, 4),
                'cpc' => round($totals->avg_cpc ?? 0, 4),
            ],
        ];
    }

    public function getCampaignInsights(int $campaignId, User $user, string $period = 'monthly'): array
    {
        $campaign = Campaign::where('id', $campaignId)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (!$campaign) {
            return [];
        }

        $query = CampaignInsight::where('campaign_id', $campaignId);

        switch ($period) {
            case 'daily':
                $query->where('date', '>=', now()->subDays(7));
                break;
            case 'weekly':
                $query->where('date', '>=', now()->subWeeks(4));
                break;
            case 'monthly':
            default:
                $query->where('date', '>=', now()->subMonths(3));
                break;
        }

        $insights = $query->orderBy('date')->get();

        $totals = $insights->reduce(function ($carry, $item) {
            return [
                'impressions' => $carry['impressions'] + $item->impressions,
                'reach'       => $carry['reach'] + $item->reach,
                'clicks'      => $carry['clicks'] + $item->clicks,
                'spend'       => $carry['spend'] + $item->spend,
                'conversions' => $carry['conversions'] + $item->conversions,
            ];
        }, ['impressions' => 0, 'reach' => 0, 'clicks' => 0, 'spend' => 0, 'conversions' => 0]);

        return [
            'campaign' => [
                'id'        => $campaign->id,
                'name'      => $campaign->name,
                'objective' => $campaign->objective,
                'status'    => $campaign->status,
                'budget'    => $campaign->budget,
            ],
            'period'   => $period,
            'totals'   => $totals,
            'insights' => $insights,
        ];
    }

    public function getComparison(User $user): array
    {
        $tenantId = $user->tenant_id;

        $thisMonth = CampaignInsight::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->startOfMonth())
            ->selectRaw('SUM(spend) as spend, SUM(clicks) as clicks, SUM(impressions) as impressions')
            ->first();

        $lastMonth = CampaignInsight::where('tenant_id', $tenantId)
            ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->selectRaw('SUM(spend) as spend, SUM(clicks) as clicks, SUM(impressions) as impressions')
            ->first();

        return [
            'this_month' => [
                'spend'       => round($thisMonth->spend ?? 0, 2),
                'clicks'      => (int) ($thisMonth->clicks ?? 0),
                'impressions' => (int) ($thisMonth->impressions ?? 0),
            ],
            'last_month' => [
                'spend'       => round($lastMonth->spend ?? 0, 2),
                'clicks'      => (int) ($lastMonth->clicks ?? 0),
                'impressions' => (int) ($lastMonth->impressions ?? 0),
            ],
        ];
    }

    public function seedTestData(User $user): void
    {
        $campaigns = Campaign::where('tenant_id', $user->tenant_id)->get();

        foreach ($campaigns as $campaign) {
            for ($i = 30; $i >= 0; $i--) {
                $impressions = rand(1000, 10000);
                $clicks      = rand(50, 500);
                $spend       = rand(10, 100);

                CampaignInsight::updateOrCreate(
                    ['campaign_id' => $campaign->id, 'date' => now()->subDays($i)->toDateString()],
                    [
                        'tenant_id'   => $user->tenant_id,
                        'impressions' => $impressions,
                        'reach'       => (int) ($impressions * 0.8),
                        'clicks'      => $clicks,
                        'spend'       => $spend,
                        'cpm'         => round(($spend / $impressions) * 1000, 4),
                        'cpc'         => round($spend / $clicks, 4),
                        'ctr'         => round(($clicks / $impressions) * 100, 4),
                        'conversions' => rand(1, 20),
                    ]
                );
            }
        }
    }
}
