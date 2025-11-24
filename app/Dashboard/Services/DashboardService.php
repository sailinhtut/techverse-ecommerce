<?php

namespace App\Dashboard\Services;

use App\Order\Models\Order;
use App\Order\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public static function getSaleCountToday(): array
    {
        $start = Carbon::today();
        $end   = Carbon::now();

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($order) => $order->created_at->format('H:00'));

        $hours = collect(range(0, now()->hour))
            ->map(fn($h) => sprintf('%02d:00', $h));

        return [
            'categories' => $hours->values(),
            'series' => $hours->map(fn($h) => ($orders[$h] ?? collect())->count())->values(),
        ];
    }

    public static function getSaleCountLastWeek(): array
    {
        $start = Carbon::now()->startOfWeek(); // Monday
        $end   = Carbon::now();                // today

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('D'));

        $days = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

        return [
            'categories' => $days,
            'series' => $days->map(fn($d) => ($orders[$d] ?? collect())->count()),
        ];
    }


    public static function getSaleCountLastMonth(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now();

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => 'Week ' . $o->created_at->weekOfMonth);

        $weeks = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']); // include 5th week

        return [
            'categories' => $weeks,
            'series' => $weeks->map(fn($w) => ($orders[$w] ?? collect())->count()),
        ];
    }

    public static function getSaleCountLastYear(): array
    {
        $year = now()->year;

        $orders = Order::whereYear('created_at', $year)
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('M')); // Jan, Feb ...

        $months = collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);

        return [
            'categories' => $months,
            'series' => $months->map(fn($m) => ($orders[$m] ?? collect())->count()),
        ];
    }

    public static function getSaleAmountToday(): array
    {
        $start = Carbon::today();
        $end   = Carbon::now();

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($order) => $order->created_at->format('H:00'));

        $hours = collect(range(0, now()->hour))
            ->map(fn($h) => sprintf('%02d:00', $h));

        return [
            'categories' => $hours->values(),
            'series' => $hours->map(fn($h) => ($orders[$h] ?? collect())->sum('grand_total'))->values(),
        ];
    }

    public static function getSaleAmountLastWeek(): array
    {
        $start = Carbon::now()->startOfWeek(); // Monday
        $end   = Carbon::now();                // today

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('D')); // Mon, Tue, ...

        $days = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

        return [
            'categories' => $days,
            'series' => $days->map(fn($d) => ($orders[$d] ?? collect())->sum('grand_total')),
        ];
    }

    public static function getSaleAmountLastMonth(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now();

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => 'Week ' . $o->created_at->weekOfMonth);

        $weeks = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']); // include 5th week

        return [
            'categories' => $weeks,
            'series' => $weeks->map(fn($w) => ($orders[$w] ?? collect())->sum('grand_total')),
        ];
    }

    public static function getSaleAmountLastYear(): array
    {
        $year = now()->year;

        $orders = Order::whereYear('created_at', $year)
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('M')); // Jan, Feb, ...

        $months = collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);

        return [
            'categories' => $months,
            'series' => $months->map(fn($m) => ($orders[$m] ?? collect())->sum('grand_total')),
        ];
    }

    public static function getProfitToday(): array
    {
        $start = Carbon::today();
        $end   = Carbon::now();


        $orders = Order::with('products.product')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('H:00'));

        $hours = collect(range(0, now()->hour))
            ->map(fn($h) => sprintf('%02d:00', $h));


        return [
            'categories' => $hours,
            'series' => $hours->map(
                fn($h) => ($orders[$h] ?? collect())->sum(fn($o) => $o->getProfit())
            ),
        ];
    }

    public static function getProfitLastWeek(): array
    {
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now();

        $orders = Order::with('products.product')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('D'));

        $days = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);

        return [
            'categories' => $days,
            'series' => $days->map(
                fn($d) => ($orders[$d] ?? collect())->sum(fn($o) => $o->getProfit())
            ),
        ];
    }

    public static function getProfitLastMonth(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now();

        $orders = Order::with('products.product')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($o) => 'Week ' . $o->created_at->weekOfMonth);

        $weeks = collect(['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5']);

        return [
            'categories' => $weeks,
            'series' => $weeks->map(
                fn($w) => ($orders[$w] ?? collect())->sum(fn($o) => $o->getProfit())
            ),
        ];
    }

    public static function getProfitLastYear(): array
    {
        $year = now()->year;

        $orders = Order::with('products.product')
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn($o) => $o->created_at->format('M'));

        $months = collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);

        return [
            'categories' => $months,
            'series' => $months->map(
                fn($m) => ($orders[$m] ?? collect())->sum(fn($o) => $o->getProfit())
            ),
        ];
    }

    public static function getSaleProductPieToday(): array
    {
        $start = Carbon::today();
        $end = Carbon::now();

        return self::getSaleProductPieData($start, $end);
    }

    public static function getSaleProductPieLastWeek(): array
    {
        $start = Carbon::now()->subWeek();
        $end = Carbon::now();

        return self::getSaleProductPieData($start, $end);
    }

    public static function getSaleProductPieLastMonth(): array
    {
        $start = Carbon::now()->subMonth()->startOfMonth();
        $end = Carbon::now();

        return self::getSaleProductPieData($start, $end);
    }

    public static function getSaleProductPieLastYear(): array
    {
        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now();

        return self::getSaleProductPieData($start, $end);
    }

    protected static function getSaleProductPieData($start, $end): array
    {
        $products = OrderProduct::whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get()
            ->groupBy('name'); // Group by product name

        $labels = $products->keys();
        $series = $products->map(fn($items) => $items->sum('quantity'));

        return [
            'labels' => $labels->values(),
            'series' => $series->values(),
        ];
    }

    protected static function getSaleCategoryPieData($start, $end): array
    {
        $items = OrderProduct::whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get()
            ->groupBy(fn($p) => $p->product->category->name ?? 'Unknown Category');

        $labels = $items->keys();
        $series = $items->map(fn($group) => $group->sum('quantity'));

        return [
            'labels' => $labels->values(),
            'series' => $series->values(),
        ];
    }

    public static function getSaleCategoryPieToday()
    {
        return self::getSaleCategoryPieData(Carbon::today(), Carbon::now());
    }
    public static function getSaleCategoryPieLastWeek()
    {
        return self::getSaleCategoryPieData(Carbon::now()->subWeek(), Carbon::now());
    }
    public static function getSaleCategoryPieLastMonth()
    {
        return self::getSaleCategoryPieData(Carbon::now()->subMonth()->startOfMonth(), Carbon::now());
    }
    public static function getSaleCategoryPieLastYear()
    {
        return self::getSaleCategoryPieData(Carbon::now()->subYear()->startOfYear(), Carbon::now());
    }

    protected static function getSaleBrandPieData($start, $end): array
    {
        $items = OrderProduct::whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->get()
            ->groupBy(fn($p) => $p->product->brand->name ?? 'Unknown Brand');

        $labels = $items->keys();
        $series = $items->map(fn($group) => $group->sum('quantity'));

        return [
            'labels' => $labels->values(),
            'series' => $series->values(),
        ];
    }

    public static function getSaleBrandPieToday()
    {
        return self::getSaleBrandPieData(Carbon::today(), Carbon::now());
    }
    public static function getSaleBrandPieLastWeek()
    {
        return self::getSaleBrandPieData(Carbon::now()->subWeek(), Carbon::now());
    }
    public static function getSaleBrandPieLastMonth()
    {
        return self::getSaleBrandPieData(Carbon::now()->subMonth()->startOfMonth(), Carbon::now());
    }
    public static function getSaleBrandPieLastYear()
    {
        return self::getSaleBrandPieData(Carbon::now()->subYear()->startOfYear(), Carbon::now());
    }
}
