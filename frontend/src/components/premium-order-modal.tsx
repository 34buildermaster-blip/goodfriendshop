"use client";

import { FormEvent, useEffect, useState } from "react";
import Link from "next/link";
import { CheckCircle2, ShoppingBag, X } from "lucide-react";
import {
  createOrder,
  type CustomerUser,
  type OrderItem,
  type PremiumProductItem,
} from "@/lib/api";
import { useSiteSettings } from "@/components/site-settings-provider";

const fallbackCustomerFieldLabels: Record<string, string> = {
  account_email: "อีเมลบัญชีที่ต้องการใช้งาน",
  account_password: "รหัสผ่านบัญชี (ถ้าจำเป็น)",
  profile_name: "ชื่อโปรไฟล์",
  line_id: "LINE ID สำหรับติดต่อ",
  phone: "เบอร์โทรศัพท์",
  device: "อุปกรณ์ที่ใช้",
};

export function PremiumOrderModal({
  onClose,
  product,
}: {
  onClose: () => void;
  product: PremiumProductItem;
}) {
  const settings = useSiteSettings();
  const [accountIdentifier, setAccountIdentifier] = useState("");
  const [customerName, setCustomerName] = useState("");
  const [customerEmail, setCustomerEmail] = useState("");
  const [customerPhone, setCustomerPhone] = useState("");
  const [customerNote, setCustomerNote] = useState("");
  const [customerInputs, setCustomerInputs] = useState<Record<string, string>>({});
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [order, setOrder] = useState<OrderItem | null>(null);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const requiredFields = product.customer_required_fields ?? [];
  const fieldLabels = {
    ...fallbackCustomerFieldLabels,
    ...(product.customer_field_labels ?? {}),
  };
  const primaryAccountField =
    requiredFields.find((field) => field === "account_email") ?? requiredFields[0];

  useEffect(() => {
    const token = window.localStorage.getItem("gfs_token");
    const storedUser = window.localStorage.getItem("gfs_user");

    queueMicrotask(() => setIsLoggedIn(Boolean(token)));

    if (storedUser) {
      try {
        const user = JSON.parse(storedUser) as CustomerUser;
        queueMicrotask(() => {
          const defaultIdentifier =
            primaryAccountField === "line_id"
              ? user.line_id
              : primaryAccountField === "phone"
                ? user.phone
                : user.email;

          setCustomerName(user.name ?? "");
          setCustomerEmail(user.email ?? "");
          setCustomerPhone(user.phone ?? "");
          setAccountIdentifier(defaultIdentifier ?? "");
          setCustomerInputs((current) => ({
            ...current,
            account_email: user.email ?? current.account_email ?? "",
            phone: user.phone ?? current.phone ?? "",
            line_id: user.line_id ?? current.line_id ?? "",
          }));
        });
      } catch {
        window.localStorage.removeItem("gfs_user");
      }
    }
  }, [primaryAccountField]);

  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        onClose();
      }
    };

    document.addEventListener("keydown", onKeyDown);
    return () => document.removeEventListener("keydown", onKeyDown);
  }, [onClose]);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    setLoading(true);
    setError("");

    try {
      const token = window.localStorage.getItem("gfs_token");
      const createdOrder = await createOrder(
        {
          premium_app_id: product.slug ?? product.id,
          customer_name: customerName,
          customer_email: customerEmail,
          customer_phone: customerPhone,
          player_identifier: accountIdentifier,
          customer_note: customerNote,
          extra_fields: {
            product_type: "premium_app",
            product_title: product.title,
            delivery_label: product.delivery_label,
            customer_inputs: customerInputs,
          },
        },
        token,
      );

      setOrder(createdOrder);
      setCustomerNote("");
      setCustomerInputs({});
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "ไม่สามารถสร้างออเดอร์ได้");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div
      aria-labelledby="premium-order-title"
      aria-modal="true"
      className="fixed inset-0 z-[90] flex items-center justify-center bg-black/72 px-4 py-6 backdrop-blur-md"
      role="dialog"
    >
      <button
        aria-label="ปิดฟอร์มสั่งซื้อ"
        className="absolute inset-0 cursor-default"
        onClick={onClose}
        type="button"
      />
      <form
        className="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[32px] border border-emerald-400/30 bg-[#101923] p-6 text-white shadow-[0_0_60px_rgba(0,207,127,0.2)] md:p-8"
        onSubmit={handleSubmit}
      >
        <button
          aria-label="ปิด"
          className="absolute right-4 top-4 grid h-10 w-10 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
          onClick={onClose}
          type="button"
        >
          <X size={20} />
        </button>

        <p className="text-xs font-medium uppercase tracking-[0.16em] text-emerald-300">
          Premium order
        </p>
        <h2 className="mt-2 pr-10 text-2xl font-semibold" id="premium-order-title">
          สั่งซื้อ {product.title}
        </h2>
        <p className="mt-2 text-sm leading-6 text-white/62">
          กรอกข้อมูลบัญชีหรืออีเมลที่ต้องการใช้งาน ทีมงานจะตรวจสอบและอัปเดตสถานะในหน้าโปรไฟล์
        </p>

        {settings.order_notice ? (
          <div className="mt-4 rounded-2xl border border-amber-300/20 bg-amber-400/10 p-4 text-sm leading-6 text-amber-50/90">
            {settings.order_notice}
          </div>
        ) : null}

        <div className="mt-6 grid gap-4 md:grid-cols-2">
          <label className="grid gap-2 text-sm font-medium text-white/85 md:col-span-2">
            {primaryAccountField ? fieldLabels[primaryAccountField] : "บัญชี / อีเมลที่ต้องการใช้งาน"}
            <input
              className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
              onChange={(event) => {
                setAccountIdentifier(event.target.value);
                if (primaryAccountField) {
                  setCustomerInputs((current) => ({
                    ...current,
                    [primaryAccountField]: event.target.value,
                  }));
                }
              }}
              required
              value={accountIdentifier}
            />
          </label>
          <label className="grid gap-2 text-sm font-medium text-white/85">
            ชื่อลูกค้า
            <input
              className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
              onChange={(event) => setCustomerName(event.target.value)}
              required={!isLoggedIn}
              value={customerName}
            />
          </label>
          <label className="grid gap-2 text-sm font-medium text-white/85">
            เบอร์โทร
            <input
              className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
              onChange={(event) => setCustomerPhone(event.target.value)}
              value={customerPhone}
            />
          </label>
          <label className="grid gap-2 text-sm font-medium text-white/85 md:col-span-2">
            อีเมลรับข้อมูล
            <input
              className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
              onChange={(event) => setCustomerEmail(event.target.value)}
              type="email"
              value={customerEmail}
            />
          </label>
          {requiredFields
            .filter((field) => field !== primaryAccountField)
            .map((field) => (
              <label
                className="grid gap-2 text-sm font-medium text-white/85"
                key={field}
              >
                {fieldLabels[field] ?? field}
                <input
                  className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
                  onChange={(event) =>
                    setCustomerInputs((current) => ({
                      ...current,
                      [field]: event.target.value,
                    }))
                  }
                  required
                  type={field === "account_password" ? "password" : "text"}
                  value={customerInputs[field] ?? ""}
                />
              </label>
            ))}
        </div>

        {(product.delivery_label || product.terms) ? (
          <div className="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-4 text-sm leading-6 text-white/75">
            {product.delivery_label ? (
              <p>
                <span className="font-semibold text-emerald-200">วิธีส่งมอบ:</span>{" "}
                {product.delivery_label}
              </p>
            ) : null}
            {product.terms ? <p className="mt-1">{product.terms}</p> : null}
          </div>
        ) : null}

        <label className="mt-4 grid gap-2 text-sm font-medium text-white/85">
          หมายเหตุ
          <textarea
            className="min-h-24 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 outline-none focus:border-emerald-400"
            onChange={(event) => setCustomerNote(event.target.value)}
            placeholder="เช่น ต้องการใช้งานบนมือถือ / ขอรับข้อมูลทาง LINE"
            value={customerNote}
          />
        </label>

        {error ? (
          <p className="mt-4 rounded-2xl bg-red-500/12 p-3 text-sm text-red-200">
            {error}
          </p>
        ) : null}

        {order ? (
          <div className="mt-4 rounded-2xl border border-emerald-400/30 bg-emerald-500/12 p-4">
            <div className="flex items-start gap-3">
              <CheckCircle2 className="mt-1 text-emerald-300" size={22} />
              <div>
                <p className="font-semibold text-emerald-200">สร้างออเดอร์สำเร็จ</p>
                <p className="mt-1 text-sm text-white/70">
                  เลขออเดอร์ {order.order_number} สถานะ {order.status_label}
                </p>
                <Link
                  className="mt-3 inline-flex h-10 items-center justify-center rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400"
                  href={`/payment?order=${encodeURIComponent(order.order_number)}`}
                >
                  ไปหน้าชำระเงิน
                </Link>
              </div>
            </div>
          </div>
        ) : null}

        <div className="mt-6 flex flex-col gap-4 rounded-3xl bg-emerald-500/10 p-5 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p className="text-sm text-emerald-300">ราคาสินค้า</p>
            <p className="text-3xl font-bold text-[#ffc012]">{product.price}</p>
          </div>
          <button
            className="flex h-12 items-center justify-center gap-2 rounded-full bg-emerald-500 px-7 text-base font-semibold text-white transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60"
            disabled={loading}
            type="submit"
          >
            <ShoppingBag size={18} />
            {loading ? "กำลังส่งออเดอร์..." : "ยืนยันสั่งซื้อ"}
          </button>
        </div>
      </form>
    </div>
  );
}
