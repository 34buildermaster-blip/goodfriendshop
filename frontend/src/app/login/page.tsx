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
            className="flex h-12 w-full items-center justify-center gap-3 rounded-full border border-[#dadce0] bg-white text-sm font-semibold text-[#3c4043] shadow-sm transition hover:bg-[#f8fafd] hover:shadow-md"
            href={`${backendBaseUrl}/auth/google/redirect`}
          >
            <svg aria-hidden="true" className="h-5 w-5" viewBox="0 0 24 24">
              <path
                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09Z"
                fill="#4285F4"
              />
              <path
                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23Z"
                fill="#34A853"
              />
              <path
                d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84Z"
                fill="#FBBC05"
              />
              <path
                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.31 9.14 5.38 12 5.38Z"
                fill="#EA4335"
              />
            </svg>
            เข้าสู่ระบบด้วย Google
          </a>
          <a
            className="mt-3 flex h-12 w-full items-center justify-center gap-3 rounded-full bg-[#06C755] text-sm font-semibold text-white shadow-sm transition hover:bg-[#05b84f] hover:shadow-md"
            href={`${backendBaseUrl}/auth/line/redirect`}
          >
            <svg aria-hidden="true" className="h-6 w-6" viewBox="0 0 32 32">
              <path
                d="M16 4C8.83 4 3 8.67 3 14.42c0 5.16 4.59 9.49 10.79 10.31.42.09.99.28 1.14.64.13.33.08.84.04 1.17l-.18 1.11c-.06.33-.26 1.29 1.13.7 1.39-.58 7.5-4.42 10.24-7.57A9.2 9.2 0 0 0 29 14.42C29 8.67 23.17 4 16 4Z"
                fill="white"
              />
              <path
                d="M10.08 17.86h2.98v1.03H9.04v-6.08h1.04v5.05Zm4.43 1.03h-1.04v-6.08h1.04v6.08Zm5.95 0h-1.05l-2.75-4.1v4.1h-1.04v-6.08h1.05l2.75 4.1v-4.1h1.04v6.08Zm4.52-5.05h-2.7v1.39h2.41v1.01h-2.41v1.62h2.7v1.03h-3.74v-6.08h3.74v1.03Z"
                fill="#06C755"
              />
            </svg>
            เข้าสู่ระบบด้วย LINE
          </a>
          <p className="mt-5 text-center text-sm text-white/65">
            ยังไม่มีบัญชี? <Link className="font-semibold text-emerald-300" href="/register">สมัครสมาชิก</Link>
          </p>
        </form>
      </section>
    </main>
  );
}
