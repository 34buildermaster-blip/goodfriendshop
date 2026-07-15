import Link from "next/link";
import { ArrowRight, BadgeCheck, CreditCard, FileText, ShieldCheck, WalletCards } from "lucide-react";
import { SiteFooter, SiteHeader } from "@/components/site-chrome";
import { getPaymentMethods } from "@/lib/api";

type PaymentPageProps = {
  searchParams?: Promise<{
    order?: string;
  }>;
};

export const metadata = {
  title: "Payment | Good Friend Shop",
  description: "Payment page prepared for Wepay and manual payment flows.",
};

export default async function PaymentPage({ searchParams }: PaymentPageProps) {
  const params = await searchParams;
  const orderNumber = params?.order;
  const methods = await getPaymentMethods();

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/payment" />
      <div className="mint-page-surface pt-[82px]">
        <section className="mx-auto max-w-6xl px-5 py-12">
          <div className="grid gap-8 lg:grid-cols-[1fr_380px]">
            <div className="rounded-[34px] border border-emerald-300/15 bg-[#111821]/92 p-6 shadow-2xl shadow-black/20 md:p-8">
              <p className="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">
                Payment center
              </p>
              <h1 className="mt-4 text-4xl font-bold leading-tight md:text-5xl">
                หน้าชำระเงิน
              </h1>
              <p className="mt-4 max-w-2xl text-base leading-8 text-white/68">
                หน้านี้เตรียมไว้สำหรับเชื่อม Wepay ในขั้นต่อไป ตอนนี้ระบบสร้างออเดอร์ได้แล้ว
                และสามารถใช้หน้านี้เป็นจุดรวมข้อมูลการชำระเงิน/สถานะการจ่ายได้
              </p>

              {orderNumber ? (
                <div className="mt-6 rounded-3xl border border-emerald-300/20 bg-emerald-400/10 p-5">
                  <p className="text-sm font-semibold text-emerald-200">ออเดอร์ที่กำลังชำระ</p>
                  <p className="mt-2 text-2xl font-bold text-white">{orderNumber}</p>
                </div>
              ) : (
                <div className="mt-6 rounded-3xl border border-amber-300/20 bg-amber-400/10 p-5">
                  <p className="font-semibold text-amber-100">ยังไม่ได้เลือกออเดอร์</p>
                  <p className="mt-1 text-sm leading-6 text-white/65">
                    หลังจากลูกค้าสั่งซื้อ สามารถส่งมาหน้านี้พร้อมเลขออเดอร์ได้
                  </p>
                </div>
              )}

              <div className="mt-8 grid gap-4 md:grid-cols-3">
                <PaymentStep icon={FileText} label="สร้างออเดอร์" state="พร้อมใช้งาน" />
                <PaymentStep icon={WalletCards} label="เลือกช่องทางจ่าย" state="เตรียมไว้แล้ว" />
                <PaymentStep icon={BadgeCheck} label="ยืนยันอัตโนมัติ" state="รอ Wepay" />
              </div>

              <div className="mt-8 grid gap-4 md:grid-cols-3">
                {methods.map((method) => (
                  <article
                    className="rounded-3xl border border-white/10 bg-white/[0.04] p-5"
                    key={method.id}
                  >
                    <div className="flex items-center justify-between gap-3">
                      <div className="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-300">
                        <CreditCard size={22} />
                      </div>
                      <span className="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 text-xs font-bold text-white/58">
                        {method.status}
                      </span>
                    </div>
                    <h2 className="mt-5 text-xl font-bold text-white">{method.name}</h2>
                    <p className="mt-2 min-h-16 text-sm leading-6 text-white/58">
                      {method.description ?? "ช่องทางชำระเงินสำหรับระบบ Good Friend Shop"}
                    </p>
                    <button
                      className="mt-5 flex h-11 w-full items-center justify-center rounded-full border border-white/10 bg-white/[0.05] text-sm font-semibold text-white/55"
                      disabled
                      type="button"
                    >
                      รอเปิดใช้งาน
                    </button>
                  </article>
                ))}
              </div>
            </div>

            <aside className="rounded-[34px] border border-white/10 bg-[#101722]/92 p-6">
              <div className="grid h-14 w-14 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-300">
                <ShieldCheck size={26} />
              </div>
              <h2 className="mt-5 text-2xl font-bold">สิ่งที่พร้อมต่อ Wepay</h2>
              <ul className="mt-5 space-y-4 text-sm leading-6 text-white/66">
                <li>ระบบมีเลขออเดอร์และยอดชำระพร้อมส่งไป payment gateway</li>
                <li>หลังบ้านมีช่องบันทึก payment status, method, reference และเวลาจ่ายเงิน</li>
                <li>เหลือเพิ่ม API key, create payment session และ webhook callback</li>
              </ul>
              <Link
                className="mt-7 flex h-12 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400"
                href="/profile?tab=orders"
              >
                ดูประวัติออเดอร์
                <ArrowRight size={17} />
              </Link>
            </aside>
          </div>
        </section>
        <SiteFooter />
      </div>
    </main>
  );
}

function PaymentStep({
  icon: Icon,
  label,
  state,
}: {
  icon: typeof FileText;
  label: string;
  state: string;
}) {
  return (
    <div className="rounded-3xl border border-white/10 bg-white/[0.04] p-4">
      <div className="flex items-center gap-3">
        <div className="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-300">
          <Icon size={19} />
        </div>
        <div>
          <p className="font-bold text-white">{label}</p>
          <p className="mt-1 text-xs font-semibold text-emerald-300">{state}</p>
        </div>
      </div>
    </div>
  );
}
