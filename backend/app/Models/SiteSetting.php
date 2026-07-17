<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'label', 'value', 'type', 'group', 'sort_order'])]
class SiteSetting extends Model
{
    public const DEFAULTS = [
        'site_name' => ['label' => 'ชื่อเว็บไซต์', 'value' => 'Good Friend Shop', 'type' => 'text', 'group' => 'general', 'sort_order' => 10],
        'logo_path' => ['label' => 'โลโก้เว็บไซต์', 'value' => '/figma/logo-goodfriend.webp', 'type' => 'image', 'group' => 'general', 'sort_order' => 20],
        'footer_tagline' => ['label' => 'Tagline Footer', 'value' => 'เติมเกมไวเหมือนเพื่อนรู้ใจ ราคาสบายกระเป๋าที่สุด!', 'type' => 'text', 'group' => 'general', 'sort_order' => 30],
        'footer_description' => ['label' => 'คำอธิบาย Footer', 'value' => 'GoodFriendShop คือเพื่อนแท้ของเกมเมอร์ พร้อมสนับสนุนให้คุณเล่นต่อได้ไม่มีสะดุด', 'type' => 'textarea', 'group' => 'general', 'sort_order' => 40],
        'contact_line' => ['label' => 'LINE', 'value' => 'xxxxxxx', 'type' => 'text', 'group' => 'contact', 'sort_order' => 50],
        'contact_email' => ['label' => 'อีเมล', 'value' => 'xxxxxxx@gmail.com', 'type' => 'text', 'group' => 'contact', 'sort_order' => 60],
        'contact_phone' => ['label' => 'เบอร์โทร', 'value' => 'xxx-xxx-xxxx', 'type' => 'text', 'group' => 'contact', 'sort_order' => 70],
        'facebook_label' => ['label' => 'Facebook', 'value' => 'xxxxxx', 'type' => 'text', 'group' => 'contact', 'sort_order' => 80],
        'support_hours' => ['label' => 'เวลาทำการฝ่ายซัพพอร์ต', 'value' => 'พร้อมดูแลทุกวัน 10:00-24:00 น.', 'type' => 'text', 'group' => 'contact', 'sort_order' => 90],
        'order_notice' => ['label' => 'ข้อความแจ้งก่อนสั่งซื้อ', 'value' => 'กรุณาตรวจสอบข้อมูลบัญชีให้ถูกต้องก่อนชำระเงิน หากข้อมูลผิดอาจทำให้ดำเนินการล่าช้า', 'type' => 'textarea', 'group' => 'policy', 'sort_order' => 100],
        'claim_policy' => ['label' => 'เงื่อนไขการเคลม', 'value' => 'สินค้าที่มีประกันสามารถแจ้งเคลมได้ตามระยะเวลาที่ระบุ พร้อมเลขออเดอร์และหลักฐานปัญหา', 'type' => 'textarea', 'group' => 'policy', 'sort_order' => 110],
        'refund_policy' => ['label' => 'นโยบายคืนเงิน', 'value' => 'กรณีร้านไม่สามารถดำเนินการได้ จะคืนเงินตามช่องทางที่ลูกค้าชำระเข้ามา หลังตรวจสอบรายการเรียบร้อย', 'type' => 'textarea', 'group' => 'policy', 'sort_order' => 120],
        'admin_notification_webhook_url' => ['label' => 'Webhook แจ้งเตือนแอดมิน', 'value' => '', 'type' => 'text', 'group' => 'notification', 'sort_order' => 130],
        'notification_order_created' => ['label' => 'ข้อความแจ้งเตือนออเดอร์ใหม่', 'value' => 'มีออเดอร์ใหม่ #{order_number} ยอด {amount} จาก {customer_name}', 'type' => 'textarea', 'group' => 'notification', 'sort_order' => 140],
        'notification_order_updated' => ['label' => 'ข้อความแจ้งเตือนอัปเดตออเดอร์', 'value' => 'ออเดอร์ #{order_number} อัปเดตสถานะเป็น {status}', 'type' => 'textarea', 'group' => 'notification', 'sort_order' => 150],
    ];

    public static function seedDefaults(): void
    {
        self::query()->where('key', 'logo_text')->delete();

        foreach (self::DEFAULTS as $key => $setting) {
            $record = self::firstOrNew(['key' => $key]);

            $record->fill([
                'key' => $key,
                'label' => $setting['label'],
                'type' => $setting['type'],
                'group' => $setting['group'],
                'sort_order' => $setting['sort_order'],
            ]);

            if (! $record->exists) {
                $record->value = $setting['value'];
            }

            $record->save();
        }
    }

    public static function values(): array
    {
        self::seedDefaults();

        return self::query()
            ->whereIn('key', array_keys(self::DEFAULTS))
            ->orderBy('sort_order')
            ->pluck('value', 'key')
            ->all();
    }
}
