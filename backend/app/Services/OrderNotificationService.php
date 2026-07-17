<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Throwable;

class OrderNotificationService
{
    public function orderCreated(Order $order): void
    {
        $this->send($order, 'notification_order_created');
    }

    public function orderUpdated(Order $order): void
    {
        $this->send($order, 'notification_order_updated');
    }

    private function send(Order $order, string $templateKey): void
    {
        $settings = SiteSetting::values();
        $webhookUrl = trim((string) ($settings['admin_notification_webhook_url'] ?? ''));

        if ($webhookUrl === '') {
            return;
        }

        $message = $this->renderTemplate((string) ($settings[$templateKey] ?? ''), $order);

        try {
            Http::timeout(5)->post($webhookUrl, [
                'text' => $message,
                'message' => $message,
                'order' => [
                    'number' => $order->order_number,
                    'customer' => $order->customer_name,
                    'product' => $order->package_name,
                    'amount' => $order->currency.' '.number_format((float) $order->price, 2),
                    'status' => Order::statusLabels()[$order->status] ?? $order->status,
                    'payment_status' => Order::paymentStatusLabels()[$order->payment_status] ?? $order->payment_status,
                ],
            ]);
        } catch (Throwable) {
            report('Order notification webhook failed for '.$order->order_number);
        }
    }

    private function renderTemplate(string $template, Order $order): string
    {
        return strtr($template, [
            '{order_number}' => $order->order_number,
            '{customer_name}' => $order->customer_name ?: '-',
            '{product}' => $order->package_name,
            '{amount}' => $order->currency.' '.number_format((float) $order->price, 2),
            '{status}' => Order::statusLabels()[$order->status] ?? $order->status,
            '{payment_status}' => Order::paymentStatusLabels()[$order->payment_status] ?? $order->payment_status,
        ]);
    }
}
