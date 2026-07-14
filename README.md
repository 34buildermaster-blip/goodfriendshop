# Game Topup

โครงโปรเจกต์เว็บเติมเกม แยก `frontend` และ `backend` ชัดเจนเหมือนโปรเจกต์เดิม เพื่อรองรับข้อมูลเกม แพ็กเกจ ราคา ออเดอร์ และประวัติการเติมที่มีจำนวนมาก

## โครงสร้าง

- `frontend` - Next.js 16, React 19, TypeScript, Tailwind CSS สำหรับหน้าร้านและหน้าจัดการ
- `backend` - Laravel 13 API สำหรับข้อมูลหลัก, order, payment, webhook, admin และ integration
- `docs` - เอกสารออกแบบข้อมูลและ flow งาน

## เริ่มงานฝั่ง frontend

```bash
npm --prefix frontend install
npm run dev:frontend
```

เปิด `http://localhost:3001`

## เริ่มงานฝั่ง backend

ต้องมี PHP 8.3+ และ Composer ก่อน

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan serve --host=127.0.0.1 --port=8001
```

API health check อยู่ที่ `http://localhost:8001/api/health`

## แนวทางแยกหน้าที่

- `frontend` ถือเฉพาะ UI, form state, validation เบื้องต้น และการเรียก API
- `backend` เป็นแหล่งข้อมูลจริงทั้งหมด เช่น เกม แพ็กเกจ ราคา โปรโมชัน ออเดอร์ สถานะชำระเงิน และ log การเติม
- ข้อมูลที่เปลี่ยนบ่อยควรอยู่ในฐานข้อมูลหลังบ้าน ไม่ hard-code ในหน้าเว็บ
- integration กับ payment และ provider เติมเกมควรอยู่หลังบ้าน เพื่อเก็บ secret และ webhook ให้ปลอดภัย

## งานต่อที่แนะนำ

- ทำ database schema สำหรับ game, package, product_price, order, transaction, topup_log
- เพิ่ม admin API สำหรับจัดการเกม/แพ็กเกจ/ราคา
- เพิ่ม payment provider และ webhook
- เปลี่ยนข้อมูล mock ใน `frontend` ให้ดึงจาก `backend`
