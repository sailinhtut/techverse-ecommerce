<?php

namespace App\Dashboard\Controllers;

use App\Dashboard\Services\DashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Order\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController
{
    public int $cacheTimeMinutes = 0;

    public function getTotalSaleCountAPI(Request $request)
    {
        try {
            $duration = $request->query('duration', 'today');
            $cacheKey = "dashboard_total_sale_count_{$duration}";
            $data = Cache::remember($cacheKey, now()->addMinutes($this->cacheTimeMinutes), function () use ($duration) {

                return match ($duration) {
                    'today' => DashboardService::getSaleCountToday(),
                    'last_week' => DashboardService::getSaleCountLastWeek(),
                    'last_month' => DashboardService::getSaleCountLastMonth(),
                    'last_year' => DashboardService::getSaleCountLastYear(),
                    default => DashboardService::getSaleCountToday(),
                };
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart'
            ], 500);
        }
    }

    public function getTotalSaleAmountAPI(Request $request)
    {
        try {
            $duration = $request->query('duration', 'today');
            $cacheKey = "dashboard_total_sale_amount_{$duration}";
            $data = Cache::remember($cacheKey, now()->addMinutes($this->cacheTimeMinutes), function () use ($duration) {

                return match ($duration) {
                    'today' => DashboardService::getSaleAmountToday(),
                    'last_week' => DashboardService::getSaleAmountLastWeek(),
                    'last_month' => DashboardService::getSaleAmountLastMonth(),
                    'last_year' => DashboardService::getSaleAmountLastYear(),
                    default => DashboardService::getSaleAmountToday(),
                };
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart'
            ], 500);
        }
    }

    public function getTotalProfitAmountAPI(Request $request)
    {
        try {
            $duration = $request->query('duration', 'today');

            $cacheKey = "dashboard_total_profit_amount_{$duration}";

            $data = Cache::remember($cacheKey, now()->addMinutes($this->cacheTimeMinutes), function () use ($duration) {
                return match ($duration) {
                    'today'      => DashboardService::getProfitToday(),
                    'last_week'  => DashboardService::getProfitLastWeek(),
                    'last_month' => DashboardService::getProfitLastMonth(),
                    'last_year'  => DashboardService::getProfitLastYear(),
                    default      => DashboardService::getProfitToday(),
                };
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load profit chart',
            ], 500);
        }
    }


    public function getSaleProductPieAPI(Request $request)
    {
        try {
            $duration = $request->query('duration', 'today');
            $cacheKey = "dashboard_sale_product_pie_{$duration}";

            $data = Cache::remember($cacheKey, now()->addMinutes($this->cacheTimeMinutes), function () use ($duration) {
                return match ($duration) {
                    'today' => DashboardService::getSaleProductPieToday(),
                    'last_week' => DashboardService::getSaleProductPieLastWeek(),
                    'last_month' => DashboardService::getSaleProductPieLastMonth(),
                    'last_year' => DashboardService::getSaleProductPieLastYear(),
                    default => DashboardService::getSaleProductPieToday(),
                };
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart'
            ], 500);
        }
    }

    public function getSaleCategoryPieAPI(Request $request)
    {
        try {
            $duration = $request->query('duration', 'today');
            $cacheKey = "dashboard_top_category_pie_{$duration}";

            $data = Cache::remember($cacheKey, now()->addMinutes($this->cacheTimeMinutes), function () use ($duration) {
                return match ($duration) {
                    'today' => DashboardService::getSaleCategoryPieToday(),
                    'last_week' => DashboardService::getSaleCategoryPieLastWeek(),
                    'last_month' => DashboardService::getSaleCategoryPieLastMonth(),
                    'last_year' => DashboardService::getSaleCategoryPieLastYear(),
                    default => DashboardService::getSaleCategoryPieToday(),
                };
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load chart'], 500);
        }
    }

    public function getSaleBrandPieAPI(Request $request)
    {
        try {
            $duration = $request->query('duration', 'today');
            $cacheKey = "dashboard_top_brand_pie_{$duration}";

            $data = Cache::remember($cacheKey, now()->addMinutes($this->cacheTimeMinutes), function () use ($duration) {
                return match ($duration) {
                    'today' => DashboardService::getSaleBrandPieToday(),
                    'last_week' => DashboardService::getSaleBrandPieLastWeek(),
                    'last_month' => DashboardService::getSaleBrandPieLastMonth(),
                    'last_year' => DashboardService::getSaleBrandPieLastYear(),
                    default => DashboardService::getSaleBrandPieToday(),
                };
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load chart'], 500);
        }
    }
}
