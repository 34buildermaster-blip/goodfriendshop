"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { FormEvent, useState } from "react";
import { SiteHeader } from "@/components/site-chrome";
import { registerCustomer } from "@/lib/api";

export default function RegisterPage() {
  const router = useRouter();
  const [form, setForm] = useState({ name: "", email: "", phone: "", line_id: "", password: "" });
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setError("");

    try {
      const result = await registerCustomer(form);
      window.localStorage.setItem("gfs_token", result.token);
      window.localStorage.setItem("gfs_user", JSON.stringify(result.user));
      router.push("/profile");
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "สมัครสมาชิกไม่สำเร็จ");
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/register" />
      <section className="mint-page-surface min-h-screen px-5 pt-32">
        <form
          className="mx-auto max-w-2xl rounded-[28px] border border-[#586c64]/70 bg-[#111821]/90 p-6"
          onSubmit={handleSubmit}
        >
          <p className="text-xs font-medium uppercase tracking-[0.16em] text-emerald-300">
            Member register
          </p>
          <h1 className="mt-3 text-3xl font-semibold">สมัครสมาชิก</h1>
          <div className="mt-6 grid gap-4 md:grid-cols-2">
            {[
              ["name", "ชื่อ", "text"],
              ["email", "อีเมล", "email"],
              ["phone", "เบอร์โทร", "tel"],
              ["line_id", "LINE ID", "text"],
              ["password", "รหัสผ่าน", "password"],
            ].map(([key, label, type]) => (
              <label className="grid gap-2 text-sm font-medium text-white/85" key={key}>
                {label}
                <input
                  className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 outline-none focus:border-emerald-400"
                  minLength={key === "password" ? 8 : undefined}
                  onChange={(event) => setForm((current) => ({ ...current, [key]: event.target.value }))}
                  required={["name", "email", "password"].includes(key)}
                  type={type}
                  value={form[key as keyof typeof form]}
                />
              </label>
            ))}
          </div>
          {error ? <p className="mt-4 rounded-2xl bg-red-500/12 p-3 text-sm text-red-200">{error}</p> : null}
          <button className="mt-6 h-12 w-full rounded-full bg-emerald-500 font-semibold text-white transition hover:bg-emerald-400 disabled:opacity-60" disabled={loading} type="submit">
            {loading ? "กำลังสมัครสมาชิก..." : "สมัครสมาชิก"}
          </button>
          <p className="mt-5 text-center text-sm text-white/65">
            มีบัญชีแล้ว? <Link className="font-semibold text-emerald-300" href="/login">เข้าสู่ระบบ</Link>
          </p>
        </form>
      </section>
    </main>
  );
}
