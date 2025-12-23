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
                    'today' => DashboardService::getSaleCount('today'),
                    'last_week' => DashboardService::getSaleCount('week'),
                    'last_month' => DashboardService::getSaleCount('month'),
                    'last_year' => DashboardService::getSaleCount('years', 1),
                    'last_2_year' => DashboardService::getSaleCount('years', 2),
                    'last_3_year' => DashboardService::getSaleCount('years', 3),
                    'last_5_year' => DashboardService::getSaleCount('years', 5),
                    'last_10_year' => DashboardService::getSaleCount('years', 10),
                    default => DashboardService::getSaleCount('today'),
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
                    'today' => DashboardService::getSaleAmount('today'),
                    'last_week' => DashboardService::getSaleAmount('week'),
                    'last_month' => DashboardService::getSaleAmount('month'),
                    'last_year' => DashboardService::getSaleAmount('years', 1),
                    'last_2_year' => DashboardService::getSaleAmount('years', 2),
                    'last_3_year' => DashboardService::getSaleAmount('years', 3),
                    'last_5_year' => DashboardService::getSaleAmount('years', 5),
                    'last_10_year' => DashboardService::getSaleAmount('years', 10),
                    default => DashboardService::getSaleAmount('today'),
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
                    'today' => DashboardService::getProfitAmount('today'),
                    'last_week' => DashboardService::getProfitAmount('week'),
                    'last_month' => DashboardService::getProfitAmount('month'),
                    'last_year' => DashboardService::getProfitAmount('years', 1),
                    'last_2_year' => DashboardService::getProfitAmount('years', 2),
                    'last_3_year' => DashboardService::getProfitAmount('years', 3),
                    'last_5_year' => DashboardService::getProfitAmount('years', 5),
                    'last_10_year' => DashboardService::getProfitAmount('years', 10),
                    default => DashboardService::getProfitAmount('today'),
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
                    'today'        => DashboardService::getSaleProductPie('today'),
                    'last_week'    => DashboardService::getSaleProductPie('week'),
                    'last_month'   => DashboardService::getSaleProductPie('month'),
                    'last_year'    => DashboardService::getSaleProductPie('years', 1),
                    'last_2_year'  => DashboardService::getSaleProductPie('years', 2),
                    'last_3_year'  => DashboardService::getSaleProductPie('years', 3),
                    'last_5_year'  => DashboardService::getSaleProductPie('years', 5),
                    'last_10_year' => DashboardService::getSaleProductPie('years', 10),
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
                    'today'        => DashboardService::getSaleCategoryPie('today'),
                    'last_week'    => DashboardService::getSaleCategoryPie('week'),
                    'last_month'   => DashboardService::getSaleCategoryPie('month'),
                    'last_year'    => DashboardService::getSaleCategoryPie('years', 1),
                    'last_2_year'  => DashboardService::getSaleCategoryPie('years', 2),
                    'last_3_year'  => DashboardService::getSaleCategoryPie('years', 3),
                    'last_5_year'  => DashboardService::getSaleCategoryPie('years', 5),
                    'last_10_year' => DashboardService::getSaleCategoryPie('years', 10),
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
                    'today'        => DashboardService::getSaleBrandPie('today'),
                    'last_week'    => DashboardService::getSaleBrandPie('week'),
                    'last_month'   => DashboardService::getSaleBrandPie('month'),
                    'last_year'    => DashboardService::getSaleBrandPie('years', 1),
                    'last_2_year'  => DashboardService::getSaleBrandPie('years', 2),
                    'last_3_year'  => DashboardService::getSaleBrandPie('years', 3),
                    'last_5_year'  => DashboardService::getSaleBrandPie('years', 5),
                    'last_10_year' => DashboardService::getSaleBrandPie('years', 10),
                };
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load chart'], 500);
        }
    }
}
