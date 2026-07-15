"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useRef, useState } from "react";
import {
  ChevronDown,
  History,
  LogIn,
  LogOut,
  TicketPercent,
  UserPlus,
  UserRound,
  WalletCards,
} from "lucide-react";
import { logoutCustomer, type CustomerUser } from "@/lib/api";

export function AccountButton() {
  const router = useRouter();
  const dropdownRef = useRef<HTMLDivElement>(null);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [user, setUser] = useState<CustomerUser | null>(null);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const timeoutId = window.setTimeout(() => {
      const token = window.localStorage.getItem("gfs_token");
      const storedUser = window.localStorage.getItem("gfs_user");

      setIsLoggedIn(Boolean(token));
      if (storedUser) {
        try {
          setUser(JSON.parse(storedUser) as CustomerUser);
        } catch {
          window.localStorage.removeItem("gfs_user");
        }
      }
    }, 0);

    return () => window.clearTimeout(timeoutId);
  }, []);

  useEffect(() => {
    function handlePointerDown(event: PointerEvent) {
      if (!dropdownRef.current?.contains(event.target as Node)) {
        setOpen(false);
      }
    }

    function handleKeyDown(event: KeyboardEvent) {
      if (event.key === "Escape") {
        setOpen(false);
      }
    }

    document.addEventListener("pointerdown", handlePointerDown);
    document.addEventListener("keydown", handleKeyDown);

    return () => {
      document.removeEventListener("pointerdown", handlePointerDown);
      document.removeEventListener("keydown", handleKeyDown);
    };
  }, []);

  async function handleLogout() {
    const token = window.localStorage.getItem("gfs_token");

    if (token) {
      await logoutCustomer(token).catch(() => null);
    }

    window.localStorage.removeItem("gfs_token");
    window.localStorage.removeItem("gfs_user");
    setIsLoggedIn(false);
    setUser(null);
    setOpen(false);
    router.push("/login");
  }

  return (
    <div className="relative hidden lg:block" ref={dropdownRef}>
      <button
        aria-expanded={open}
        aria-haspopup="menu"
        className="flex h-11 items-center justify-center gap-2 rounded-full border border-white/10 bg-white/[0.06] px-4 text-sm font-semibold text-white transition hover:border-emerald-400 hover:bg-emerald-500/15 hover:text-emerald-200"
        onClick={() => setOpen((current) => !current)}
        type="button"
      >
        <span className="grid h-7 w-7 place-items-center rounded-full bg-emerald-400 text-xs font-bold text-[#06140f]">
          {user?.name ? user.name.charAt(0).toUpperCase() : <UserRound size={15} />}
        </span>
        <span className="max-w-28 truncate">{user?.name ?? "Account"}</span>
        <ChevronDown
          className={`transition ${open ? "rotate-180 text-emerald-300" : "text-white/70"}`}
          size={16}
        />
      </button>

      {open ? (
        <div
          className="absolute right-0 top-14 z-[70] w-72 overflow-hidden rounded-3xl border border-emerald-300/15 bg-[#111821]/95 p-3 text-white shadow-2xl shadow-black/35 backdrop-blur-xl"
          role="menu"
        >
          <div className="rounded-2xl bg-white/[0.04] p-4">
            <div className="flex items-center gap-3">
              <div className="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-emerald-400 text-base font-bold text-[#06140f]">
                {user?.name ? user.name.charAt(0).toUpperCase() : "GF"}
              </div>
              <div className="min-w-0">
                <p className="truncate text-sm font-semibold">
                  {user?.name ?? "Good Friend Member"}
                </p>
                <p className="mt-1 truncate text-xs text-emerald-300">
                  {user?.email ?? "เข้าสู่ระบบเพื่อจัดการบัญชี"}
                </p>
              </div>
            </div>
          </div>

          <div className="mt-2 grid gap-1">
            {isLoggedIn ? (
              <>
                <AccountMenuLink href="/profile" icon={UserRound} label="บัญชีของฉัน" />
                <AccountMenuLink href="/profile?tab=orders" icon={History} label="ประวัติออเดอร์" />
                <AccountMenuLink href="/profile?tab=payment" icon={WalletCards} label="ช่องทางชำระเงิน" />
                <AccountMenuLink href="/profile" icon={TicketPercent} label="คูปอง / Coin" />
                <div className="my-2 h-px bg-white/10" />
                <button
                  className="flex h-11 w-full items-center gap-3 rounded-2xl px-3 text-left text-sm font-semibold text-white/72 transition hover:bg-red-500/10 hover:text-red-100"
                  onClick={handleLogout}
                  role="menuitem"
                  type="button"
                >
                  <LogOut size={17} />
                  ออกจากระบบ
                </button>
              </>
            ) : (
              <>
                <AccountMenuLink href="/login" icon={LogIn} label="เข้าสู่ระบบ" />
                <AccountMenuLink href="/register" icon={UserPlus} label="สมัครสมาชิก" />
              </>
            )}
          </div>
        </div>
      ) : null}
    </div>
  );
}

function AccountMenuLink({
  href,
  icon: Icon,
  label,
}: {
  href: string;
  icon: typeof UserRound;
  label: string;
}) {
  return (
    <Link
      className="flex h-11 items-center gap-3 rounded-2xl px-3 text-sm font-semibold text-white/72 transition hover:bg-white/[0.06] hover:text-emerald-200"
      href={href}
      role="menuitem"
    >
      <Icon size={17} />
      {label}
    </Link>
  );
}
