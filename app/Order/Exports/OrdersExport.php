<?php

namespace App\Order\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $orders;
    protected bool $totalsAppended = false;

    protected float $subtotalTotal = 0;
    protected float $discountTotal = 0;
    protected float $shippingTotal = 0;
    protected float $taxTotal = 0;
    protected float $grandTotal = 0;
    protected float $profitTotal = 0;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        // Append TOTAL row at the bottom
        if (!$this->totalsAppended) {
            $this->totalsAppended = true;

            $this->orders->push((object) [
                'is_total_row' => true,
            ]);
        }

        return $this->orders;
    }

    public function map($order): array
    {
        // 🔻 TOTAL ROW
        if (!empty($order->is_total_row)) {
            return [
                'TOTAL',
                '',
                '',
                $this->subtotalTotal,
                $this->discountTotal,
                $this->shippingTotal,
                $this->taxTotal,
                $this->grandTotal,
                $this->profitTotal,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        $orderProfit = (float) $order->getProfit();

        // 🔻 Accumulate totals
        $this->subtotalTotal += (float) $order->subtotal;
        $this->discountTotal += (float) $order->discount_total;
        $this->shippingTotal += (float) $order->shipping_total;
        $this->taxTotal      += (float) $order->tax_total;
        $this->grandTotal    += (float) $order->grand_total;
        $this->profitTotal    +=  $orderProfit;

        return [
            $order->id,
            $order->order_number,
            $order->status,
            $order->subtotal,
            $order->discount_total,
            $order->shipping_total,
            $order->tax_total,
            $order->grand_total,
            $orderProfit,
            $order->coupon_code,
            $order->stock_consumed ? 'Yes' : 'No',
            $order->archived ? 'Yes' : 'No',
            optional($order->paymentMethod)->name,
            optional($order->shippingMethod)->name,
            $this->formatAddress($order->billing_address),
            $this->formatAddress($order->shipping_address),
            optional($order->updated_at)?->format('Y-m-d H:i:s'),
            optional($order->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Order Status',
            'Subtotal Total',
            'Discount Total',
            'Shipping Total',
            'Tax Total',
            'Grand Total',
            'Profit Total',
            'Coupon Code',
            'Stock Consumed',
            'Is Archived',
            'Payment Method',
            'Shipping Method',
            'Billing Address',
            'Shipping Address',
            'Updated At',
            'Created At',
        ];
    }

    protected function formatAddress(?array $address): string
    {
        if (!$address) {
            return '-';
        }

        return collect([
            $address['recipient_name'] ?? null,
            $address['phone'] ?? null,
            trim(implode(', ', array_filter([
                $address['street_address'] ?? null,
                $address['city'] ?? null,
                $address['state'] ?? null,
                $address['postal_code'] ?? null,
                $address['country'] ?? null,
            ]))),
        ])
            ->filter()
            ->implode(' | ');
    }
}
