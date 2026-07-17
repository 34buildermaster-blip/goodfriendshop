<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\EnsuresAdminAccess;
use App\Http\Controllers\Controller;
use App\Models\ContentPost;
use App\Models\Game;
use App\Models\GamePackage;
use App\Models\Order;
use App\Models\PremiumApp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use EnsuresAdminAccess;

    public function __invoke(): View
    {
        $this->ensureAdminAccess();
        $today = now();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        return view('admin.dashboard', [
            'metrics' => [
                ['label' => 'เกมทั้งหมด', 'value' => Game::count(), 'note' => 'จัดการได้แล้ว', 'tone' => 'green'],
                ['label' => 'แพ็กเกจทั้งหมด', 'value' => GamePackage::count(), 'note' => 'ผูกกับเกม', 'tone' => 'cyan'],
                ['label' => 'แอพพรีเมียม', 'value' => PremiumApp::count(), 'note' => 'สินค้าแยกจากเกม', 'tone' => 'teal'],
                ['label' => 'ข่าว/กิจกรรม', 'value' => ContentPost::count(), 'note' => 'รอส่งขึ้นหน้าบ้าน', 'tone' => 'lime'],
                ['label' => 'สมาชิกทั้งหมด', 'value' => User::where('role', User::ROLE_CUSTOMER)->count(), 'note' => 'บัญชีลูกค้า', 'tone' => 'lime'],
            ],
            'salesMetrics' => [
                [
                    'label' => 'ยอดขายวันนี้',
                    'value' => $this->formatMoney($this->paidOrderTotal($today->copy()->startOfDay(), $today->copy()->endOfDay())),
                    'note' => 'ออเดอร์ที่ชำระแล้ววันนี้',
                ],
                [
                    'label' => 'ยอดขายสัปดาห์นี้',
                    'value' => $this->formatMoney($this->paidOrderTotal($today->copy()->startOfWeek(), $today->copy()->endOfWeek())),
                    'note' => 'รวมตั้งแต่ต้นสัปดาห์',
                ],
                [
                    'label' => 'ยอดขายเดือนนี้',
                    'value' => $this->formatMoney($this->paidOrderTotal($today->copy()->startOfMonth(), $today->copy()->endOfMonth())),
                    'note' => 'รวมตั้งแต่ต้นเดือน',
                ],
            ],
            'reportMetrics' => [
                [
                    'label' => 'กำไรประมาณการเดือนนี้',
                    'value' => $this->formatMoney($this->paidOrderProfit($monthStart, $monthEnd)),
                    'note' => 'คำนวณจากยอดขายลบต้นทุนที่บันทึกไว้',
                ],
                [
                    'label' => 'ออเดอร์รอดำเนินการ',
                    'value' => number_format($this->pendingOrderCount()),
                    'note' => 'รายการที่ควรตรวจวันนี้',
                ],
                [
                    'label' => 'ยอดเติมเกมเดือนนี้',
                    'value' => $this->formatMoney($this->paidOrderTotalByType($monthStart, $monthEnd, 'game')),
                    'note' => 'เฉพาะออเดอร์เกมที่ชำระแล้ว',
                ],
                [
                    'label' => 'ยอดแอพพรีเมียมเดือนนี้',
                    'value' => $this->formatMoney($this->paidOrderTotalByType($monthStart, $monthEnd, 'premium_app')),
                    'note' => 'เฉพาะออเดอร์แอพที่ชำระแล้ว',
                ],
            ],
            'topProducts' => $this->topProducts($monthStart, $monthEnd),
            'recentOrders' => Order::query()
                ->latest()
                ->take(6)
                ->get(),
            'recentGames' => Game::query()
                ->withCount('packages')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    private function paidOrderTotal(Carbon $start, Carbon $end): float
    {
        return (float) Order::query()
            ->where(function ($query) {
                $query->where('payment_status', Order::PAYMENT_PAID)
                    ->orWhereIn('status', [
                        Order::STATUS_PAID,
                        Order::STATUS_PROCESSING,
                        Order::STATUS_COMPLETED,
                    ]);
            })
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->whereNull('paid_at')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->sum('price');
    }

    private function paidOrderProfit(Carbon $start, Carbon $end): float
    {
        return (float) $this->paidOrderQuery($start, $end)
            ->with(['package', 'premiumApp'])
            ->get()
            ->sum(function (Order $order) {
                $cost = $order->package?->cost ?? $order->premiumApp?->cost ?? 0;

                return (float) $order->price - (float) $cost;
            });
    }

    private function paidOrderTotalByType(Carbon $start, Carbon $end, string $type): float
    {
        return (float) $this->paidOrderQuery($start, $end)
            ->when(
                $type === 'game',
                fn ($query) => $query->whereNotNull('game_package_id'),
                fn ($query) => $query->whereNotNull('premium_app_id'),
            )
            ->sum('price');
    }

    private function pendingOrderCount(): int
    {
        return Order::query()
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PAID, Order::STATUS_PROCESSING])
            ->where('status', '!=', Order::STATUS_COMPLETED)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->count();
    }

    private function topProducts(Carbon $start, Carbon $end)
    {
        return $this->paidOrderQuery($start, $end)
            ->select('package_name', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(price) as total_sales'))
            ->groupBy('package_name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();
    }

    private function paidOrderQuery(Carbon $start, Carbon $end)
    {
        return Order::query()
            ->where(function ($query) {
                $query->where('payment_status', Order::PAYMENT_PAID)
                    ->orWhereIn('status', [
                        Order::STATUS_PAID,
                        Order::STATUS_PROCESSING,
                        Order::STATUS_COMPLETED,
                    ]);
            })
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('paid_at', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->whereNull('paid_at')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            });
    }

    private function formatMoney(float $amount): string
    {
        return 'THB '.number_format($amount, 2);
    }
}
