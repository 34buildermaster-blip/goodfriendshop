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
use Illuminate\View\View;

class DashboardController extends Controller
{
    use EnsuresAdminAccess;

    public function __invoke(): View
    {
        $this->ensureAdminAccess();
        $today = now();

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

    private function formatMoney(float $amount): string
    {
        return 'THB '.number_format($amount, 2);
    }
}
