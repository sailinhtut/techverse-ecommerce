<?php

namespace App\Dashboard\Services;

use App\Order\Models\Order;
use App\Order\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardService
{

    public static function getSaleCount(
        string $period,
        int $years = 1
    ): array {
        $now = now();

        switch ($period) {

            /* ================= TODAY (HOURLY) ================= */
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('H:00'));

                $categories = collect(range(0, $now->hour))
                    ->map(fn($h) => sprintf('%02d:00', $h));

                break;

            /* ================= LAST WEEK (DAILY) ================= */
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('D'));

                $categories = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

                break;

            /* ================= LAST MONTH (WEEKLY) ================= */
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => 'Week ' . $o->created_at->weekOfMonth);

                $categories = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);

                break;

            /* ================= LAST N YEARS (MONTHLY) ================= */
            case 'years':
                $start = $now->copy()->subYears($years)->startOfMonth();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('Y-m'));

                $categories = collect();
                $cursor = $start->copy();

                while ($cursor <= $end) {
                    $categories->push($cursor->format('Y-m'));
                    $cursor->addMonth();
                }

                break;

            default:
                throw new \InvalidArgumentException('Invalid period type');
        }

        $series = $categories
            ->map(fn($key) => ($orders[$key] ?? collect())->count())
            ->values();

        return [
            'total'      => $series->sum(),
            'categories' => $categories->values(),
            'series'     => $series,
        ];
    }

    public static function getSaleAmount(
        string $period,
        int $years = 1
    ): array {
        $now = now();

        switch ($period) {

            /* ================= TODAY (HOURLY) ================= */
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('H:00'));

                $categories = collect(range(0, $now->hour))
                    ->map(fn($h) => sprintf('%02d:00', $h));
                break;

            /* ================= LAST WEEK (DAILY) ================= */
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('D'));

                $categories = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
                break;

            /* ================= LAST MONTH (WEEKLY) ================= */
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => 'Week ' . $o->created_at->weekOfMonth);

                $categories = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);
                break;

            /* ================= LAST N YEARS (MONTHLY) ================= */
            case 'years':
                $start = $now->copy()->subYears($years)->startOfMonth();
                $end   = $now;

                $orders = Order::whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('Y-m'));

                $categories = collect();
                $cursor = $start->copy();

                while ($cursor <= $end) {
                    $categories->push($cursor->format('Y-m'));
                    $cursor->addMonth();
                }
                break;

            default:
                throw new \InvalidArgumentException('Invalid period type');
        }

        $series = $categories
            ->map(fn($key) => ($orders[$key] ?? collect())->sum('grand_total'))
            ->values();

        return [
            'total'      => $series->sum(),   // ✅ TOTAL SALE AMOUNT
            'categories' => $categories->values(),
            'series'     => $series,
        ];
    }

    public static function getProfitAmount(
        string $period,
        int $years = 1
    ): array {
        $now = now();

        switch ($period) {

            /* ================= TODAY (HOURLY) ================= */
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now;

                $orders = Order::with('products.product')
                    ->whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('H:00'));

                $categories = collect(range(0, $now->hour))
                    ->map(fn($h) => sprintf('%02d:00', $h));
                break;

            /* ================= LAST WEEK (DAILY) ================= */
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now;

                $orders = Order::with('products.product')
                    ->whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('D'));

                $categories = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
                break;

            /* ================= LAST MONTH (WEEKLY) ================= */
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end   = $now;

                $orders = Order::with('products.product')
                    ->whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => 'Week ' . $o->created_at->weekOfMonth);

                $categories = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);
                break;

            /* ================= LAST N YEARS (MONTHLY) ================= */
            case 'years':
                $start = $now->copy()->subYears($years)->startOfMonth();
                $end   = $now;

                $orders = Order::with('products.product')
                    ->whereBetween('created_at', [$start, $end])
                    ->get()
                    ->groupBy(fn($o) => $o->created_at->format('Y-m'));

                $categories = collect();
                $cursor = $start->copy();

                while ($cursor <= $end) {
                    $categories->push($cursor->format('Y-m'));
                    $cursor->addMonth();
                }
                break;

            default:
                throw new \InvalidArgumentException('Invalid period type');
        }

        $series = $categories
            ->map(
                fn($key) => ($orders[$key] ?? collect())
                    ->sum(fn($o) => $o->getProfit())
            )
            ->values();

        return [
            'total'      => $series->sum(),   // ✅ TOTAL PROFIT
            'categories' => $categories->values(),
            'series'     => $series,
        ];
    }


    public static function getSaleProductPie(
        string $period,
        int $years = 1
    ): array {
        [$start, $end] = self::resolvePeriod($period, $years);

        $items = OrderProduct::whereHas(
            'order',
            fn($q) => $q->whereBetween('created_at', [$start, $end])
        )
            ->get()
            ->groupBy('name');

        $series = $items->map(fn($group) => $group->sum('quantity'));

        return [
            'total_type'  => $series->count(),        // ✅ TOTAL TYPES
            'total_count'  => $series->sum(),        // ✅ TOTAL COUNT
            'labels' => $items->keys()->values(),
            'series' => $series->values(),
        ];
    }

    public static function getSaleCategoryPie(
        string $period,
        int $years = 1
    ): array {
        [$start, $end] = self::resolvePeriod($period, $years);

        $items = OrderProduct::with('product.category')
            ->whereHas(
                'order',
                fn($q) => $q->whereBetween('created_at', [$start, $end])
            )
            ->get()
            ->groupBy(fn($p) => $p->product->category->name ?? 'Unknown Category');

        $series = $items->map(fn($group) => $group->sum('quantity'));

        return [
            'total_type'  => $series->count(),        // ✅ TOTAL TYPES
            'total_count'  => $series->sum(),        // ✅ TOTAL COUNT
            'labels' => $items->keys()->values(),
            'series' => $series->values(),
        ];
    }

    public static function getSaleBrandPie(
        string $period,
        int $years = 1
    ): array {
        [$start, $end] = self::resolvePeriod($period, $years);

        $items = OrderProduct::with('product.brand')
            ->whereHas(
                'order',
                fn($q) => $q->whereBetween('created_at', [$start, $end])
            )
            ->get()
            ->groupBy(fn($p) => $p->product->brand->name ?? 'Unknown Brand');

        $series = $items->map(fn($group) => $group->sum('quantity'));

        return [
            'total_type'  => $series->count(),        // ✅ TOTAL TYPES
            'total_count'  => $series->sum(),        // ✅ TOTAL COUNT
            'labels' => $items->keys()->values(),
            'series' => $series->values(),
        ];
    }



    protected static function resolvePeriod(
        string $period,
        int $years = 1
    ): array {
        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now],
            'week'  => [$now->copy()->subWeek(), $now],
            'month' => [$now->copy()->subMonth()->startOfMonth(), $now],
            'years' => [$now->copy()->subYears($years)->startOfYear(), $now],
            default => [$now->copy()->startOfDay(), $now],
        };
    }
}
