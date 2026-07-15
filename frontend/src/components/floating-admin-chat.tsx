"use client";

import Link from "next/link";
import { useEffect, useMemo, useState } from "react";
import { Headphones, MessageCircle, X } from "lucide-react";
import { getSiteContent, type SiteSettings } from "@/lib/api";

function cleanValue(value?: string | null) {
  const cleaned = value?.trim() ?? "";

  if (!cleaned || /^x+$/i.test(cleaned)) {
    return "";
  }

  return cleaned;
}

function lineHref(settings: SiteSettings | null) {
  const line = cleanValue(settings?.contact_line);

  if (!line) {
    return "/contact";
  }

  if (line.startsWith("http://") || line.startsWith("https://")) {
    return line;
  }

  if (line.startsWith("@")) {
    return `https://line.me/R/ti/p/${encodeURIComponent(line)}`;
  }

  return `https://line.me/ti/p/~${encodeURIComponent(line)}`;
}

function isExternalUrl(value: string) {
  return value.startsWith("http://") || value.startsWith("https://");
}

export function FloatingAdminChat() {
  const [settings, setSettings] = useState<SiteSettings | null>(null);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    getSiteContent()
      .then((content) => setSettings(content?.settings ?? null))
      .catch(() => setSettings(null));
  }, []);

  const href = useMemo(() => lineHref(settings), [settings]);
  const external = isExternalUrl(href);
  const lineLabel = cleanValue(settings?.contact_line);

  return (
    <div className="fixed bottom-5 right-4 z-[80] flex flex-col items-end gap-3 sm:bottom-7 sm:right-7">
      {open ? (
        <div className="w-[min(calc(100vw-2rem),330px)] overflow-hidden rounded-[26px] border border-emerald-300/20 bg-[#111821]/95 text-white shadow-2xl shadow-black/35 backdrop-blur-xl">
          <div className="flex items-start justify-between gap-3 border-b border-white/10 bg-emerald-400/10 p-4">
            <div>
              <p className="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-300">
                Live support
              </p>
              <h2 className="mt-1 text-lg font-bold">คุยกับแอดมิน 24 ชม.</h2>
            </div>
            <button
              aria-label="ปิดกล่องพูดคุย"
              className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-white/10 text-white/75 transition hover:bg-white/15 hover:text-white"
              onClick={() => setOpen(false)}
              type="button"
            >
              <X size={18} />
            </button>
          </div>

          <div className="p-4">
            <p className="text-sm leading-6 text-white/68">
              สอบถามสินค้า แจ้งปัญหาออเดอร์ หรือให้ช่วยเลือกแพ็กเกจได้เลย ทีมงานพร้อมดูแลตลอดวัน
            </p>
            <Link
              className="mt-4 flex h-12 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-bold text-white transition hover:bg-emerald-400"
              href={href}
              rel={external ? "noreferrer" : undefined}
              target={external ? "_blank" : undefined}
            >
              <MessageCircle size={18} />
              {lineLabel ? `ทัก LINE: ${lineLabel}` : "เปิดช่องทางติดต่อ"}
            </Link>
            <Link
              className="mt-3 flex h-11 items-center justify-center gap-2 rounded-full border border-white/10 px-5 text-sm font-semibold text-white/75 transition hover:border-emerald-300/30 hover:text-emerald-200"
              href="/contact"
            >
              ดูช่องทางติดต่อทั้งหมด
            </Link>
          </div>
        </div>
      ) : null}

      <button
        aria-expanded={open}
        aria-label="คุยกับแอดมิน 24 ชั่วโมง"
        className="group flex h-14 items-center gap-3 rounded-full border border-emerald-300/30 bg-emerald-500 px-4 pr-5 text-white shadow-[0_18px_45px_rgba(0,207,127,0.28)] transition hover:-translate-y-0.5 hover:bg-emerald-400"
        onClick={() => setOpen((current) => !current)}
        type="button"
      >
        <span className="grid h-10 w-10 place-items-center rounded-full bg-white text-emerald-600">
          <Headphones size={21} />
        </span>
        <span className="hidden text-left sm:block">
          <span className="block text-sm font-bold leading-none">คุยกับแอดมิน</span>
          <span className="mt-1 block text-xs font-semibold text-white/80">พร้อมช่วย 24 ชม.</span>
        </span>
      </button>
    </div>
  );
}
