"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { FormEvent, useState } from "react";
import { SiteHeader } from "@/components/site-chrome";
import { backendBaseUrl, loginCustomer } from "@/lib/api";

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setError("");

    try {
      const result = await loginCustomer(email, password);
      window.localStorage.setItem("gfs_token", result.token);
      window.localStorage.setItem("gfs_user", JSON.stringify(result.user));
      router.push("/profile");
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "เข้าสู่ระบบไม่สำเร็จ");
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/login" />
      <section className="mint-page-surface min-h-screen px-5 pt-32">
        <form
          className="mx-auto max-w-md rounded-[28px] border border-[#586c64]/70 bg-[#111821]/90 p-6"
          onSubmit={handleSubmit}
        >
          <p className="text-xs font-medium uppercase tracking-[0.16em] text-emerald-300">
            Member login
          </p>
          <h1 className="mt-3 text-3xl font-semibold">เข้าสู่ระบบ</h1>
          <label className="mt-6 grid gap-2 text-sm font-medium text-white/85">
            อีเมล
            <input className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400" onChange={(event) => setEmail(event.target.value)} required type="email" value={email} />
          </label>
          <label className="mt-4 grid gap-2 text-sm font-medium text-white/85">
            รหัสผ่าน
            <input className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400" onChange={(event) => setPassword(event.target.value)} required type="password" value={password} />
          </label>
          {error ? <p className="mt-4 rounded-2xl bg-red-500/12 p-3 text-sm text-red-200">{error}</p> : null}
          <button className="mt-6 h-12 w-full rounded-full bg-emerald-500 font-semibold text-white transition hover:bg-emerald-400 disabled:opacity-60" disabled={loading} type="submit">
            {loading ? "กำลังเข้าสู่ระบบ..." : "เข้าสู่ระบบ"}
          </button>
          <div className="my-5 flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.16em] text-white/36">
            <span className="h-px flex-1 bg-white/10" />
            หรือ
            <span className="h-px flex-1 bg-white/10" />
          </div>
          <a
            className="flex h-12 w-full items-center justify-center rounded-full border border-white/12 bg-white text-sm font-semibold text-[#111827] transition hover:bg-white/90"
            href={`${backendBaseUrl}/auth/google/redirect`}
          >
            เข้าสู่ระบบด้วย Google
          </a>
          <p className="mt-5 text-center text-sm text-white/65">
            ยังไม่มีบัญชี? <Link className="font-semibold text-emerald-300" href="/register">สมัครสมาชิก</Link>
          </p>
        </form>
      </section>
    </main>
  );
}
