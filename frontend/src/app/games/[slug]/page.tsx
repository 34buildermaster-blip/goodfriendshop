"use client";

import Image from "next/image";
import Link from "next/link";
import { useParams } from "next/navigation";
import { FormEvent, useEffect, useMemo, useState } from "react";
import { CheckCircle2, ShoppingBag } from "lucide-react";
import { PageIntro, SiteFooter, SiteHeader } from "@/components/site-chrome";
import { assetPath } from "@/lib/paths";
import {
  createOrder,
  getGame,
  type GameItem,
  type GamePackageItem,
  type OrderItem,
} from "@/lib/api";

export default function GameOrderPage() {
  const params = useParams<{ slug: string }>();
  const [game, setGame] = useState<GameItem | null>(null);
  const [selectedPackageId, setSelectedPackageId] = useState<string>("");
  const [playerId, setPlayerId] = useState("");
  const [serverId, setServerId] = useState("");
  const [customerName, setCustomerName] = useState("");
  const [customerEmail, setCustomerEmail] = useState("");
  const [customerPhone, setCustomerPhone] = useState("");
  const [customerNote, setCustomerNote] = useState("");
  const [order, setOrder] = useState<OrderItem | null>(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    const token = window.localStorage.getItem("gfs_token");
    const user = window.localStorage.getItem("gfs_user");

    queueMicrotask(() => setIsLoggedIn(Boolean(token)));

    if (user) {
      try {
        const parsed = JSON.parse(user) as { name?: string; email?: string; phone?: string };
        queueMicrotask(() => {
          setCustomerName(parsed.name ?? "");
          setCustomerEmail(parsed.email ?? "");
          setCustomerPhone(parsed.phone ?? "");
        });
      } catch {
        window.localStorage.removeItem("gfs_user");
      }
    }
  }, []);

  useEffect(() => {
    let active = true;

    getGame(params.slug).then((item) => {
      if (!active) {
        return;
      }

      setGame(item);
      setSelectedPackageId(String(item?.packages?.[0]?.id ?? ""));
    });

    return () => {
      active = false;
    };
  }, [params.slug]);

  const selectedPackage = useMemo<GamePackageItem | undefined>(
    () => game?.packages?.find((item) => String(item.id) === selectedPackageId),
    [game?.packages, selectedPackageId],
  );

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    if (!selectedPackage) {
      setError("กรุณาเลือกแพ็กเกจก่อนสั่งซื้อ");
      return;
    }

    setLoading(true);
    setError("");

    try {
      const token = window.localStorage.getItem("gfs_token");
      const createdOrder = await createOrder(
        {
          game_package_id: selectedPackage.id,
          customer_name: customerName,
          customer_email: customerEmail,
          customer_phone: customerPhone,
          player_identifier: playerId,
          server_identifier: serverId,
          customer_note: customerNote,
        },
        token,
      );

      setOrder(createdOrder);
      setPlayerId("");
      setServerId("");
      setCustomerNote("");
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "ไม่สามารถสร้างออเดอร์ได้");
    } finally {
      setLoading(false);
    }
  }

  if (!game) {
    return (
      <main className="min-h-screen bg-[#0e0d17] text-white">
        <SiteHeader activeHref="/games" />
        <div className="mint-page-surface min-h-screen pt-32">
          <div className="mx-auto max-w-7xl px-5 text-white/70">กำลังโหลดข้อมูลเกม...</div>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/games" />
      <div className="mint-page-surface">
        <PageIntro
          eyebrow="Game topup"
          title={game.name}
          description={game.description ?? "เลือกแพ็กเกจ กรอกข้อมูลเกม แล้วส่งออเดอร์ให้ทีมงานตรวจสอบ"}
        />

        <section className="mx-auto grid max-w-7xl gap-6 px-5 pb-16 lg:grid-cols-[0.88fr_1.12fr]">
          <div className="rounded-[28px] border border-[#586c64]/70 bg-[#161d26]/80 p-4">
            <div className="relative aspect-square overflow-hidden rounded-[24px]">
              <Image
                alt={game.name}
                className="object-cover"
                fill
                sizes="(min-width: 1024px) 480px, 92vw"
                src={assetPath(game.image)}
              />
            </div>
            <div className="mt-5">
              <p className="text-sm text-emerald-300">{game.publisher ?? "Good Friend Shop"}</p>
              <h2 className="mt-1 text-2xl font-semibold">{game.name}</h2>
              <p className="mt-3 leading-7 text-white/65">{game.description}</p>
            </div>
          </div>

          <form
            className="rounded-[28px] border border-[#586c64]/70 bg-[#111821]/90 p-5 md:p-7"
            onSubmit={handleSubmit}
          >
            <div className="flex items-center justify-between gap-4">
              <div>
                <p className="text-xs font-medium uppercase tracking-[0.16em] text-emerald-300">
                  Create order
                </p>
                <h2 className="mt-2 text-2xl font-semibold">เลือกแพ็กเกจ</h2>
              </div>
              {isLoggedIn ? (
                <Link className="text-sm font-medium text-emerald-300" href="/profile">
                  โปรไฟล์ของฉัน
                </Link>
              ) : null}
            </div>

            <div className="mt-5 grid gap-3">
              {game.packages?.map((item) => (
                <label
                  className={`grid cursor-pointer grid-cols-[auto_1fr_auto] items-center gap-3 rounded-2xl border p-4 transition ${
                    selectedPackageId === String(item.id)
                      ? "border-emerald-400 bg-emerald-500/12"
                      : "border-white/10 bg-white/[0.04] hover:border-white/25"
                  }`}
                  key={item.id}
                >
                  <input
                    checked={selectedPackageId === String(item.id)}
                    className="h-5 w-5 accent-emerald-400"
                    name="package"
                    onChange={() => setSelectedPackageId(String(item.id))}
                    type="radio"
                  />
                  <span>
                    <span className="block font-semibold">{item.name}</span>
                    {item.description ? (
                      <span className="mt-1 block text-sm leading-6 text-white/58">{item.description}</span>
                    ) : null}
                  </span>
                  <strong className="text-right text-lg text-[#ffc012]">
                    {item.currency} {Number(item.price).toFixed(2)}
                  </strong>
                </label>
              ))}
            </div>

            <div className="mt-6 grid gap-4 md:grid-cols-2">
              <label className="grid gap-2 text-sm font-medium text-white/85">
                UID / Player ID
                <input
                  className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
                  onChange={(event) => setPlayerId(event.target.value)}
                  required
                  value={playerId}
                />
              </label>
              <label className="grid gap-2 text-sm font-medium text-white/85">
                Server / Zone
                <input
                  className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
                  onChange={(event) => setServerId(event.target.value)}
                  value={serverId}
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
            </div>

            <label className="mt-4 grid gap-2 text-sm font-medium text-white/85">
              อีเมล
              <input
                className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
                onChange={(event) => setCustomerEmail(event.target.value)}
                type="email"
                value={customerEmail}
              />
            </label>
            <label className="mt-4 grid gap-2 text-sm font-medium text-white/85">
              หมายเหตุ
              <textarea
                className="min-h-24 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 outline-none focus:border-emerald-400"
                onChange={(event) => setCustomerNote(event.target.value)}
                value={customerNote}
              />
            </label>

            {error ? <p className="mt-4 rounded-2xl bg-red-500/12 p-3 text-sm text-red-200">{error}</p> : null}
            {order ? (
              <div className="mt-4 rounded-2xl border border-emerald-400/30 bg-emerald-500/12 p-4">
                <div className="flex items-start gap-3">
                  <CheckCircle2 className="mt-1 text-emerald-300" size={22} />
                  <div>
                    <p className="font-semibold text-emerald-200">สร้างออเดอร์สำเร็จ</p>
                    <p className="mt-1 text-sm text-white/70">
                      เลขออเดอร์ {order.order_number} สถานะ {order.status_label}
                    </p>
                  </div>
                </div>
              </div>
            ) : null}

            <button
              className="mt-6 flex h-12 w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-7 text-base font-semibold text-white transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60"
              disabled={loading}
              type="submit"
            >
              <ShoppingBag size={18} />
              {loading ? "กำลังส่งออเดอร์..." : "สั่งซื้อแพ็กเกจนี้"}
            </button>
          </form>
        </section>

        <SiteFooter />
      </div>
    </main>
  );
}
