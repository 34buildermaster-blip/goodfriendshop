"use client";

import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { SiteHeader } from "@/components/site-chrome";
import type { AuthResponse } from "@/lib/api";

export default function SocialCallbackPage() {
  const router = useRouter();
  const [message, setMessage] = useState("กำลังเข้าสู่ระบบ...");

  useEffect(() => {
    const params = new URLSearchParams(window.location.hash.replace(/^#/, ""));
    const payload = params.get("payload");

    if (!payload) {
      queueMicrotask(() => setMessage("ไม่พบข้อมูลเข้าสู่ระบบจาก Google"));
      return;
    }

    try {
      const result = JSON.parse(window.atob(payload)) as AuthResponse;

      window.localStorage.setItem("gfs_token", result.token);
      window.localStorage.setItem("gfs_user", JSON.stringify(result.user));
      window.dispatchEvent(new Event("gfs:user-updated"));
      router.replace("/profile");
    } catch {
      queueMicrotask(() => setMessage("เข้าสู่ระบบด้วย Google ไม่สำเร็จ"));
    }
  }, [router]);

  return (
    <main className="min-h-screen bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/login" />
      <section className="mint-page-surface flex min-h-screen items-center justify-center px-5 pt-24">
        <div className="w-full max-w-md rounded-[28px] border border-[#586c64]/70 bg-[#111821]/90 p-6 text-center">
          <p className="text-xs font-medium uppercase tracking-[0.16em] text-emerald-300">
            Google login
          </p>
          <h1 className="mt-3 text-2xl font-semibold">{message}</h1>
        </div>
      </section>
    </main>
  );
}
