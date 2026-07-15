"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { LogOut } from "lucide-react";
import { SiteFooter, SiteHeader } from "@/components/site-chrome";
import {
  getCurrentCustomer,
  getMyOrders,
  logoutCustomer,
  type CustomerUser,
  type OrderItem,
} from "@/lib/api";

export default function ProfilePage() {
  const router = useRouter();
  const [user, setUser] = useState<CustomerUser | null>(null);
  const [orders, setOrders] = useState<OrderItem[]>([]);
  const [loading, setLoading] = useState(true);

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
      <div className="mint-page-surface min-h-screen pt-32">
        <section className="mx-auto max-w-7xl px-5 pb-16">
          <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
            <div>
              <p className="text-xs font-medium uppercase tracking-[0.18em] text-emerald-400">
                Member profile
              </p>
              <h1 className="mt-4 text-4xl font-bold md:text-6xl">โปรไฟล์ของฉัน</h1>
            </div>
            {user ? (
              <button className="flex h-11 items-center justify-center gap-2 rounded-full border border-white/10 px-5 text-sm font-semibold text-white/80 hover:border-emerald-400" onClick={handleLogout} type="button">
                <LogOut size={17} />
                ออกจากระบบ
              </button>
            ) : null}
          </div>

          {loading ? <p className="mt-10 text-white/70">กำลังโหลดข้อมูล...</p> : null}

          {!loading && !user ? (
            <div className="mt-10 rounded-[28px] border border-[#586c64]/70 bg-[#111821]/90 p-7">
              <h2 className="text-2xl font-semibold">ยังไม่ได้เข้าสู่ระบบ</h2>
              <p className="mt-3 text-white/65">เข้าสู่ระบบเพื่อดูประวัติออเดอร์และใช้ข้อมูลเดิมตอนสั่งซื้อ</p>
              <div className="mt-6 flex flex-wrap gap-3">
                <Link className="rounded-full bg-emerald-500 px-6 py-3 font-semibold" href="/login">เข้าสู่ระบบ</Link>
                <Link className="rounded-full border border-white/10 px-6 py-3 font-semibold text-white/85" href="/register">สมัครสมาชิก</Link>
              </div>
            </div>
          ) : null}

          {user ? (
            <>
              <div className="mt-10 grid gap-4 md:grid-cols-3">
                <div className="rounded-[24px] border border-white/10 bg-white/[0.04] p-5">
                  <p className="text-sm text-emerald-300">ชื่อ</p>
                  <p className="mt-1 text-xl font-semibold">{user.name}</p>
                </div>
                <div className="rounded-[24px] border border-white/10 bg-white/[0.04] p-5">
                  <p className="text-sm text-emerald-300">อีเมล</p>
                  <p className="mt-1 text-xl font-semibold">{user.email}</p>
                </div>
                <div className="rounded-[24px] border border-white/10 bg-white/[0.04] p-5">
                  <p className="text-sm text-emerald-300">เบอร์โทร</p>
                  <p className="mt-1 text-xl font-semibold">{user.phone ?? "-"}</p>
                </div>
              </div>

              <div className="mt-10 rounded-[28px] border border-[#586c64]/70 bg-[#111821]/90 p-5">
                <h2 className="text-2xl font-semibold">ประวัติออเดอร์</h2>
                {orders.length === 0 ? (
                  <p className="mt-4 text-white/65">ยังไม่มีออเดอร์</p>
                ) : (
                  <div className="mt-5 grid gap-3">
                    {orders.map((order) => (
                      <article className="grid gap-3 rounded-2xl border border-white/10 bg-white/[0.04] p-4 md:grid-cols-[1fr_auto]" key={order.order_number}>
                        <div>
                          <p className="font-semibold text-white">{order.order_number}</p>
                          <p className="mt-1 text-sm text-white/65">
                            {order.game_name} / {order.package_name}
                          </p>
                          <p className="mt-1 text-sm text-white/55">UID: {order.player_identifier}</p>
                        </div>
                        <div className="text-left md:text-right">
                          <p className="font-semibold text-[#ffc012]">{order.currency} {Number(order.price).toFixed(2)}</p>
                          <p className="mt-1 text-sm text-emerald-300">{order.status_label}</p>
                        </div>
                      </article>
                    ))}
                  </div>
                )}
              </div>
            </>
          ) : null}
        </section>
        <SiteFooter />
      </div>
    </main>
  );
}
