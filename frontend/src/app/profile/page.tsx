"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useMemo, useState } from "react";
import {
  BadgeCheck,
  ChevronRight,
  Coins,
  CreditCard,
  Gamepad2,
  Gift,
  History,
  Home,
  IdCard,
  LogOut,
  Mail,
  MessageCircle,
  Phone,
  ShieldCheck,
  TicketPercent,
  UserRound,
  WalletCards,
} from "lucide-react";
import { SiteFooter, SiteHeader } from "@/components/site-chrome";
import {
  getCurrentCustomer,
  getMyOrders,
  logoutCustomer,
  type CustomerUser,
  type OrderItem,
} from "@/lib/api";

type ProfileTab = "overview" | "contact" | "orders" | "payment";

const tabs: Array<{ id: ProfileTab; label: string; icon: typeof UserRound }> = [
  { id: "overview", label: "ข้อมูลผู้ใช้", icon: UserRound },
  { id: "contact", label: "ช่องทางติดต่อ", icon: MessageCircle },
  { id: "orders", label: "ประวัติออเดอร์", icon: History },
  { id: "payment", label: "ช่องทางชำระเงิน", icon: CreditCard },
];

const memberMenu = [
  { label: "หน้าหลัก", href: "/", icon: Home },
  { label: "เติมเกม", href: "/games", icon: Gamepad2 },
  { label: "คูปองของฉัน", href: "/profile", icon: TicketPercent },
  { label: "ออเดอร์ของฉัน", href: "/profile", icon: History },
];

function initials(name: string) {
  return name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join("")
    .toUpperCase();
}

function statusTone(status: string) {
  if (status === "completed") {
    return "border-emerald-400/30 bg-emerald-500/12 text-emerald-200";
  }

  if (status === "cancelled") {
    return "border-red-400/25 bg-red-500/12 text-red-200";
  }

  if (status === "processing" || status === "paid") {
    return "border-sky-400/25 bg-sky-500/12 text-sky-200";
  }

  return "border-amber-300/25 bg-amber-400/12 text-amber-100";
}

export default function ProfilePage() {
  const router = useRouter();
  const [user, setUser] = useState<CustomerUser | null>(null);
  const [orders, setOrders] = useState<OrderItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<ProfileTab>("overview");

  useEffect(() => {
    const token = window.localStorage.getItem("gfs_token");

    if (!token) {
      queueMicrotask(() => setLoading(false));
      return;
    }

    Promise.all([getCurrentCustomer(token), getMyOrders(token)])
      .then(([currentUser, currentOrders]) => {
        setUser(currentUser);
        setOrders(currentOrders);
        window.localStorage.setItem("gfs_user", JSON.stringify(currentUser));
      })
      .catch(() => {
        window.localStorage.removeItem("gfs_token");
        window.localStorage.removeItem("gfs_user");
      })
      .finally(() => setLoading(false));
  }, []);

  const orderSummary = useMemo(
    () => ({
      total: orders.length,
      pending: orders.filter((order) => order.status === "pending").length,
      completed: orders.filter((order) => order.status === "completed").length,
    }),
    [orders],
  );

  async function handleLogout() {
    const token = window.localStorage.getItem("gfs_token");
    if (token) {
      await logoutCustomer(token).catch(() => null);
    }
    window.localStorage.removeItem("gfs_token");
    window.localStorage.removeItem("gfs_user");
    router.push("/login");
  }

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/profile" />
      <div className="mint-page-surface min-h-screen pt-28">
        <section className="mx-auto grid max-w-[1480px] gap-6 px-5 pb-16 lg:grid-cols-[292px_minmax(0,1fr)]">
          <aside className="rounded-[28px] border border-white/10 bg-[#121821]/92 p-4 shadow-2xl shadow-black/20 lg:sticky lg:top-28 lg:h-[calc(100vh-8rem)]">
            <div className="flex items-center gap-3 rounded-3xl bg-white/[0.04] p-4">
              <div className="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-emerald-400 text-lg font-bold text-[#06140f]">
                {user ? initials(user.name) : "GF"}
              </div>
              <div className="min-w-0">
                <p className="truncate text-sm font-semibold text-white">
                  {user?.name ?? "Good Friend Member"}
                </p>
                <p className="mt-1 truncate text-xs text-emerald-300">
                  {user?.email ?? "เข้าสู่ระบบเพื่อดูข้อมูลสมาชิก"}
                </p>
              </div>
            </div>

            <div className="mt-4 grid grid-cols-2 gap-3">
              <div className="rounded-2xl border border-yellow-300/20 bg-yellow-400/10 p-3">
                <div className="flex items-center gap-2 text-yellow-200">
                  <Coins size={17} />
                  <span className="text-xs font-semibold">Coin</span>
                </div>
                <p className="mt-2 text-2xl font-bold text-white">0</p>
              </div>
              <div className="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-3">
                <div className="flex items-center gap-2 text-emerald-200">
                  <Gift size={17} />
                  <span className="text-xs font-semibold">Coupon</span>
                </div>
                <p className="mt-2 text-2xl font-bold text-white">0</p>
              </div>
            </div>

            <nav className="mt-5 grid gap-2">
              {memberMenu.map(({ href, icon: Icon, label }) => (
                <Link
                  className="flex h-12 items-center gap-3 rounded-2xl px-4 text-sm font-semibold text-white/70 transition hover:bg-white/[0.06] hover:text-white"
                  href={href}
                  key={label}
                >
                  <Icon size={18} />
                  {label}
                </Link>
              ))}
            </nav>

            <div className="mt-5 border-t border-white/10 pt-4">
              {user ? (
                <button
                  className="flex h-12 w-full items-center gap-3 rounded-2xl px-4 text-left text-sm font-semibold text-white/70 transition hover:bg-red-500/10 hover:text-red-100"
                  onClick={handleLogout}
                  type="button"
                >
                  <LogOut size={18} />
                  ออกจากระบบ
                </button>
              ) : (
                <Link
                  className="flex h-12 items-center justify-center rounded-2xl bg-emerald-500 px-4 text-sm font-semibold text-white"
                  href="/login"
                >
                  เข้าสู่ระบบ
                </Link>
              )}
            </div>
          </aside>

          <div className="min-w-0">
            {loading ? (
              <div className="rounded-[30px] border border-white/10 bg-[#111821]/90 p-8 text-white/70">
                กำลังโหลดข้อมูลสมาชิก...
              </div>
            ) : null}

            {!loading && !user ? (
              <div className="rounded-[30px] border border-[#586c64]/70 bg-[#111821]/90 p-7">
                <p className="text-xs font-medium uppercase tracking-[0.18em] text-emerald-300">
                  Member center
                </p>
                <h1 className="mt-3 text-4xl font-bold">เข้าสู่ระบบสมาชิก</h1>
                <p className="mt-3 max-w-2xl leading-7 text-white/65">
                  เข้าสู่ระบบเพื่อดูโปรไฟล์ ประวัติออเดอร์ และใช้ข้อมูลเดิมตอนสั่งซื้อ
                </p>
                <div className="mt-6 flex flex-wrap gap-3">
                  <Link className="rounded-full bg-emerald-500 px-6 py-3 font-semibold" href="/login">
                    เข้าสู่ระบบ
                  </Link>
                  <Link className="rounded-full border border-white/10 px-6 py-3 font-semibold text-white/85" href="/register">
                    สมัครสมาชิก
                  </Link>
                </div>
              </div>
            ) : null}

            {user ? (
              <>
                <section className="overflow-hidden rounded-[32px] border border-white/10 bg-white text-[#111827] shadow-2xl shadow-black/20">
                  <div className="border-b border-slate-200 bg-[#173c68] px-5 pt-4">
                    <div className="flex flex-wrap gap-2">
                      {tabs.map(({ id, icon: Icon, label }) => (
                        <button
                          className={`flex h-11 items-center gap-2 rounded-t-2xl px-4 text-sm font-semibold transition ${
                            activeTab === id
                              ? "bg-white text-[#173c68]"
                              : "bg-white/10 text-white hover:bg-white/18"
                          }`}
                          key={id}
                          onClick={() => setActiveTab(id)}
                          type="button"
                        >
                          <Icon size={17} />
                          {label}
                        </button>
                      ))}
                    </div>
                  </div>

                  <div className="grid gap-6 p-5 lg:grid-cols-[320px_1fr] lg:p-7">
                    <div className="flex items-center gap-5">
                      <div className="grid h-28 w-28 shrink-0 place-items-center rounded-full bg-gradient-to-br from-emerald-300 to-cyan-300 text-3xl font-bold text-[#062016] shadow-lg">
                        {initials(user.name)}
                      </div>
                      <div className="min-w-0">
                        <h1 className="truncate text-3xl font-bold text-slate-950">{user.name}</h1>
                        <p className="mt-2 text-sm font-semibold text-slate-500">UID</p>
                        <p className="mt-1 break-all text-sm text-slate-600">GF-{String(user.id).padStart(6, "0")}</p>
                      </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-3">
                      <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div className="flex items-center gap-2 text-emerald-600">
                          <BadgeCheck size={18} />
                          <span className="text-xs font-bold uppercase tracking-[0.1em]">Status</span>
                        </div>
                        <p className="mt-2 text-lg font-bold text-slate-950">เปิดใช้งาน</p>
                      </div>
                      <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div className="flex items-center gap-2 text-sky-600">
                          <Mail size={18} />
                          <span className="text-xs font-bold uppercase tracking-[0.1em]">Email</span>
                        </div>
                        <p className="mt-2 truncate text-lg font-bold text-slate-950">{user.email}</p>
                      </div>
                      <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div className="flex items-center gap-2 text-violet-600">
                          <ShieldCheck size={18} />
                          <span className="text-xs font-bold uppercase tracking-[0.1em]">Role</span>
                        </div>
                        <p className="mt-2 text-lg font-bold text-slate-950">Customer</p>
                      </div>
                    </div>
                  </div>
                </section>

                <section className="mt-6 rounded-[32px] border border-white/10 bg-white p-5 text-[#111827] shadow-2xl shadow-black/15 lg:p-7">
                  {activeTab === "overview" ? (
                    <div>
                      <div className="flex items-center justify-between gap-4">
                        <div>
                          <h2 className="text-2xl font-bold">ข้อมูลส่วนตัว</h2>
                          <p className="mt-1 text-sm text-slate-500">ข้อมูลหลักของบัญชีสมาชิก</p>
                        </div>
                        <span className="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-500">
                          แก้ไขเร็ว ๆ นี้
                        </span>
                      </div>

                      <div className="mt-6 grid gap-4 md:grid-cols-2">
                        <InfoField label="ชื่อ" value={user.name} />
                        <InfoField label="อีเมล" value={user.email} />
                        <InfoField label="เบอร์โทร" value={user.phone ?? "-"} />
                        <InfoField label="LINE ID" value={user.line_id ?? "-"} />
                      </div>

                      <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <SummaryCard icon={History} label="ออเดอร์ทั้งหมด" value={String(orderSummary.total)} />
                        <SummaryCard icon={WalletCards} label="รอตรวจสอบ" value={String(orderSummary.pending)} />
                        <SummaryCard icon={BadgeCheck} label="สำเร็จแล้ว" value={String(orderSummary.completed)} />
                      </div>
                    </div>
                  ) : null}

                  {activeTab === "contact" ? (
                    <div>
                      <h2 className="text-2xl font-bold">ช่องทางติดต่อ</h2>
                      <p className="mt-1 text-sm text-slate-500">
                        ใช้สำหรับติดต่อกลับและแจ้งสถานะรายการในอนาคต
                      </p>
                      <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <ContactField icon={Phone} label="เบอร์โทรศัพท์" value={user.phone ?? "-"} />
                        <ContactField icon={Mail} label="อีเมลรับข้อมูล" value={user.email} />
                        <ContactField icon={MessageCircle} label="บัญชี LINE" value={user.line_id ?? "ยังไม่ได้ผูก"} />
                      </div>
                      <div className="mt-5 rounded-2xl bg-amber-50 p-4 text-sm leading-6 text-amber-700">
                        หากต้องชำระผ่านระบบอัตโนมัติหรือ Wepay ภายหลัง ควรเติมเบอร์โทรและ LINE ให้ครบก่อนเปิดใช้งานจริง
                      </div>
                    </div>
                  ) : null}

                  {activeTab === "orders" ? (
                    <div>
                      <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                          <h2 className="text-2xl font-bold">ประวัติออเดอร์</h2>
                          <p className="mt-1 text-sm text-slate-500">รายการสั่งซื้อทั้งหมดของบัญชีนี้</p>
                        </div>
                        <Link
                          className="flex h-11 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white"
                          href="/games"
                        >
                          เติมเกมเพิ่ม
                          <ChevronRight size={17} />
                        </Link>
                      </div>

                      {orders.length === 0 ? (
                        <div className="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                          <p className="text-lg font-bold text-slate-900">ยังไม่มีออเดอร์</p>
                          <p className="mt-2 text-sm text-slate-500">เมื่อสั่งซื้อแล้ว รายการจะมาแสดงตรงนี้</p>
                        </div>
                      ) : (
                        <div className="mt-6 overflow-hidden rounded-3xl border border-slate-200">
                          {orders.map((order) => (
                            <article
                              className="grid gap-4 border-b border-slate-200 p-4 last:border-b-0 md:grid-cols-[1fr_auto_auto]"
                              key={order.order_number}
                            >
                              <div>
                                <p className="font-bold text-slate-950">{order.order_number}</p>
                                <p className="mt-1 text-sm text-slate-500">
                                  {order.game_name} / {order.package_name}
                                </p>
                                <p className="mt-1 text-sm text-slate-400">UID: {order.player_identifier}</p>
                              </div>
                              <div className="md:text-right">
                                <p className="font-bold text-slate-950">
                                  {order.currency} {Number(order.price).toFixed(2)}
                                </p>
                                <p className="mt-1 text-xs text-slate-400">
                                  {order.created_at ? new Date(order.created_at).toLocaleDateString("th-TH") : "-"}
                                </p>
                              </div>
                              <div>
                                <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-bold ${statusTone(order.status)}`}>
                                  {order.status_label}
                                </span>
                              </div>
                            </article>
                          ))}
                        </div>
                      )}
                    </div>
                  ) : null}

                  {activeTab === "payment" ? (
                    <div>
                      <h2 className="text-2xl font-bold">ช่องทางชำระเงิน</h2>
                      <p className="mt-1 text-sm text-slate-500">
                        เตรียมพื้นที่ไว้สำหรับเชื่อม Wepay และข้อมูลชำระเงินในขั้นถัดไป
                      </p>
                      <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <PaymentCard title="PromptPay" status="รอต่อระบบ" />
                        <PaymentCard title="Wepay" status="รอ API" />
                        <PaymentCard title="บัญชีธนาคาร" status="ยังไม่เปิดใช้" />
                      </div>
                    </div>
                  ) : null}
                </section>
              </>
            ) : null}
          </div>
        </section>
        <SiteFooter />
      </div>
    </main>
  );
}

function InfoField({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
      <p className="text-sm font-semibold text-slate-500">{label}</p>
      <p className="mt-2 min-h-6 break-words text-base font-bold text-slate-950">{value}</p>
    </div>
  );
}

function SummaryCard({
  icon: Icon,
  label,
  value,
}: {
  icon: typeof History;
  label: string;
  value: string;
}) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <div className="flex items-center gap-2 text-emerald-600">
        <Icon size={18} />
        <span className="text-sm font-semibold">{label}</span>
      </div>
      <p className="mt-3 text-3xl font-bold text-slate-950">{value}</p>
    </div>
  );
}

function ContactField({
  icon: Icon,
  label,
  value,
}: {
  icon: typeof Phone;
  label: string;
  value: string;
}) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
      <div className="flex items-center gap-2 text-slate-500">
        <Icon size={18} />
        <span className="text-sm font-semibold">{label}</span>
      </div>
      <p className="mt-3 min-h-6 break-words font-bold text-slate-950">{value}</p>
    </div>
  );
}

function PaymentCard({ status, title }: { status: string; title: string }) {
  return (
    <div className="rounded-2xl border border-slate-200 bg-slate-50 p-4">
      <div className="flex items-center justify-between gap-3">
        <div className="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-600">
          <IdCard size={20} />
        </div>
        <span className="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-500">
          {status}
        </span>
      </div>
      <p className="mt-4 text-lg font-bold text-slate-950">{title}</p>
      <p className="mt-1 text-sm text-slate-500">จะเปิดใช้งานเมื่อเชื่อมระบบชำระเงินจริง</p>
    </div>
  );
}
