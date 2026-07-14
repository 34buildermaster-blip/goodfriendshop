# Data Model Draft

เอกสารตั้งต้นสำหรับข้อมูลเว็บเติมเกม ฝั่งข้อมูลจริงควรอยู่ใน `backend`

## Core Tables

- `games` - รายชื่อเกม, publisher, slug, รูป, สถานะเปิดขาย
- `game_servers` - server/region ของแต่ละเกม
- `topup_products` - สินค้าหรือแพ็กเกจเติมเกม
- `product_prices` - ราคาแยกตามช่องทาง, โปรโมชัน, วันเริ่ม/สิ้นสุด
- `customers` - ข้อมูลลูกค้าเท่าที่จำเป็นต่อการสั่งซื้อ
- `orders` - คำสั่งซื้อหลัก, ยอดรวม, สถานะ, reference
- `order_items` - รายละเอียดแพ็กเกจที่ซื้อใน order
- `payments` - ช่องทางชำระเงิน, payment reference, สถานะ, webhook payload
- `topup_jobs` - งานส่งเติมไป provider
- `topup_logs` - log การเติม, response จาก provider, error message
- `audit_logs` - log การแก้ไขข้อมูลสำคัญในหลังบ้าน

## Status Draft

- Order: `pending`, `paid`, `processing`, `completed`, `cancelled`, `failed`, `refunded`
- Payment: `created`, `waiting`, `paid`, `expired`, `failed`, `refunded`
- Topup job: `queued`, `sent`, `success`, `retrying`, `failed`

## API Areas

- Public storefront: games, products, prices, order creation
- Payment: payment session, callback/webhook, payment status
- Customer: order lookup, order history
- Admin: CRUD games, servers, products, prices, orders, manual retry
- Provider integration: topup dispatch, callback, reconciliation
