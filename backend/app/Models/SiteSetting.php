<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'label', 'value', 'type', 'group', 'sort_order'])]
class SiteSetting extends Model
{
    public const DEFAULTS = [
        'site_name' => ['label' => 'ชื่อเว็บไซต์', 'value' => 'Good Friend Shop', 'type' => 'text', 'group' => 'general', 'sort_order' => 10],
        'logo_path' => ['label' => 'โลโก้เว็บไซต์', 'value' => null, 'type' => 'image', 'group' => 'general', 'sort_order' => 20],
        'footer_tagline' => ['label' => 'Tagline Footer', 'value' => 'เติมเกมไวเหมือนเพื่อนรู้ใจ ราคาสบายกระเป๋าที่สุด!', 'type' => 'text', 'group' => 'general', 'sort_order' => 30],
        'footer_description' => ['label' => 'คำอธิบาย Footer', 'value' => 'GoodFriendShop คือเพื่อนแท้ของเกมเมอร์ พร้อมสนับสนุนให้คุณเล่นต่อได้ไม่มีสะดุด', 'type' => 'textarea', 'group' => 'general', 'sort_order' => 40],
        'contact_line' => ['label' => 'LINE', 'value' => 'xxxxxxx', 'type' => 'text', 'group' => 'contact', 'sort_order' => 50],
        'contact_email' => ['label' => 'อีเมล', 'value' => 'xxxxxxx@gmail.com', 'type' => 'text', 'group' => 'contact', 'sort_order' => 60],
        'contact_phone' => ['label' => 'เบอร์โทร', 'value' => 'xxx-xxx-xxxx', 'type' => 'text', 'group' => 'contact', 'sort_order' => 70],
        'facebook_label' => ['label' => 'Facebook', 'value' => 'xxxxxx', 'type' => 'text', 'group' => 'contact', 'sort_order' => 80],
    ];

    public static function seedDefaults(): void
    {
        self::query()->where('key', 'logo_text')->delete();

        foreach (self::DEFAULTS as $key => $setting) {
            self::updateOrCreate(['key' => $key], ['key' => $key, ...$setting]);
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
