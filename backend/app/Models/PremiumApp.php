<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'slug',
    'provider',
    'description',
    'image_path',
    'price',
    'cost',
    'currency',
    'duration_days',
    'delivery_type',
    'customer_required_fields',
    'warranty_days',
    'stock_status',
    'supplier_name',
    'supplier_contact',
    'fulfillment_note',
    'terms',
    'status',
    'sort_order',
])]
class PremiumApp extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_INACTIVE = 'inactive';

    public const DELIVERY_CODE = 'code';

    public const DELIVERY_ACCOUNT_TOPUP = 'account_topup';

    public const DELIVERY_ACCOUNT_INVITE = 'account_invite';

    public const DELIVERY_MANUAL_SERVICE = 'manual_service';

    public const DELIVERY_ACCOUNT_READY = 'account_ready';

    public const STOCK_IN_STOCK = 'in_stock';

    public const STOCK_LOW = 'low_stock';

    public const STOCK_OUT = 'out_of_stock';

    public const STOCK_PREORDER = 'preorder';

    public static function deliveryTypeLabels(): array
    {
        return [
            self::DELIVERY_CODE => 'ส่งโค้ด / Voucher',
            self::DELIVERY_ACCOUNT_TOPUP => 'เติมเข้าบัญชีลูกค้า',
            self::DELIVERY_ACCOUNT_INVITE => 'เชิญเข้ากลุ่ม / Family',
            self::DELIVERY_MANUAL_SERVICE => 'แอดมินดำเนินการเอง',
            self::DELIVERY_ACCOUNT_READY => 'ส่งบัญชีพร้อมใช้งาน',
        ];
    }

    public static function stockStatusLabels(): array
    {
        return [
            self::STOCK_IN_STOCK => 'พร้อมขาย',
            self::STOCK_LOW => 'ใกล้หมด',
            self::STOCK_OUT => 'หมดชั่วคราว',
            self::STOCK_PREORDER => 'รับพรีออเดอร์',
        ];
    }

    public static function customerFieldLabels(): array
    {
        return [
            'account_email' => 'อีเมลบัญชีที่ต้องการใช้งาน',
            'account_password' => 'รหัสผ่านบัญชี (ถ้าจำเป็น)',
            'profile_name' => 'ชื่อโปรไฟล์',
            'line_id' => 'LINE ID สำหรับติดต่อ',
            'phone' => 'เบอร์โทรศัพท์',
            'device' => 'อุปกรณ์ที่ใช้',
        ];
    }

    public function imageUrl(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://', '/'])) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'customer_required_fields' => 'array',
            'duration_days' => 'integer',
            'price' => 'decimal:2',
            'sort_order' => 'integer',
            'warranty_days' => 'integer',
        ];
    }
}
