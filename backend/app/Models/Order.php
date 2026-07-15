<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'order_number',
    'user_id',
    'game_id',
    'game_package_id',
    'premium_app_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'player_identifier',
    'server_identifier',
    'extra_fields',
    'game_name',
    'package_name',
    'price',
    'currency',
    'payment_method',
    'payment_status',
    'payment_reference',
    'payment_note',
    'paid_at',
    'status',
    'customer_note',
    'admin_note',
])]
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_AWAITING = 'awaiting_payment';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_REFUNDED = 'refunded';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'รอตรวจสอบ',
            self::STATUS_PAID => 'ชำระเงินแล้ว',
            self::STATUS_PROCESSING => 'กำลังดำเนินการ',
            self::STATUS_COMPLETED => 'สำเร็จ',
            self::STATUS_CANCELLED => 'ยกเลิก',
        ];
    }

    public static function paymentStatusLabels(): array
    {
        return [
            self::PAYMENT_UNPAID => 'Unpaid',
            self::PAYMENT_AWAITING => 'Awaiting payment',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_FAILED => 'Failed',
            self::PAYMENT_REFUNDED => 'Refunded',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(GamePackage::class, 'game_package_id');
    }

    public function premiumApp(): BelongsTo
    {
        return $this->belongsTo(PremiumApp::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (filled($order->order_number)) {
                return;
            }

            do {
                $orderNumber = 'GFS-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            } while (self::where('order_number', $orderNumber)->exists());

            $order->order_number = $orderNumber;
        });
    }

    protected function casts(): array
    {
        return [
            'extra_fields' => 'array',
            'price' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }
}
