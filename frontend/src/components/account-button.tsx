"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { UserRound } from "lucide-react";

export function AccountButton() {
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    const timeoutId = window.setTimeout(() => {
      setIsLoggedIn(Boolean(window.localStorage.getItem("gfs_token")));
    }, 0);

    return () => window.clearTimeout(timeoutId);
  }, []);

  return (
    <Link
      className="hidden h-11 items-center justify-center gap-2 rounded-full border border-white/10 bg-white/[0.06] px-5 text-sm font-semibold text-white transition hover:border-emerald-400 hover:bg-emerald-500/15 hover:text-emerald-200 lg:inline-flex"
      href={isLoggedIn ? "/profile" : "/login"}
    >
      <UserRound size={17} />
      Account
    </Link>
  );
}
