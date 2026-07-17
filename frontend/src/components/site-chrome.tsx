"use client";

import Image from "next/image";
import Link from "next/link";
import {
  Clock,
  Mail,
  Menu,
  MessageCircle,
  Search,
  ShoppingBag,
} from "lucide-react";
import { AccountButton } from "@/components/account-button";
import { cleanSiteSettingValue, useSiteSettings } from "@/components/site-settings-provider";
import { navItems } from "@/lib/site-data";
import { assetPath } from "@/lib/paths";

export function SiteHeader({
  activeHref = "/",
  logoPath = "/figma/logo-goodfriend.webp",
  logoText = "Good Friend Shop",
}: {
  activeHref?: string;
  logoPath?: string;
  logoText?: string;
}) {
  const settings = useSiteSettings();
  const resolvedLogoPath =
    logoPath === "/figma/logo-goodfriend.webp" ? (settings.logo_path ?? logoPath) : logoPath;
  const resolvedLogoText =
    logoText === "Good Friend Shop" ? (settings.site_name ?? logoText) : logoText;

  return (
    <header className="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-[#0e0d17]/80 backdrop-blur-xl">
      <div className="mx-auto flex h-[82px] max-w-[1440px] items-center gap-5 px-5 lg:px-11">
        <Link className="relative flex h-16 w-[230px] shrink-0 items-center text-lg font-bold tracking-wide text-white" href="/">
          <Image
            alt={resolvedLogoText}
            className="object-contain object-left"
            fill
            priority
            sizes="230px"
            src={assetPath(resolvedLogoPath)}
          />
        </Link>

        <nav className="hidden flex-1 items-center justify-center gap-8 text-sm font-medium text-white lg:flex">
          {navItems.map((item) => {
            const active = activeHref === item.href;
            return (
              <Link
                className={`relative py-6 transition hover:text-emerald-300 ${
                  active ? "text-emerald-400" : ""
                }`}
                href={item.href}
                key={item.href}
              >
                {item.label}
                {active ? (
                  <span className="absolute inset-x-1 -bottom-px h-1 rounded-full bg-emerald-400" />
                ) : null}
              </Link>
            );
          })}
        </nav>

        <div className="ml-auto hidden h-11 w-[246px] items-center gap-3 rounded-[14px] bg-emerald-500/25 px-4 text-emerald-300 xl:flex">
          <Search size={16} />
          <span className="text-xs">Search ...</span>
        </div>

        <AccountButton />

        <button className="ml-auto grid h-10 w-10 place-items-center rounded-xl bg-white/10 text-white lg:hidden">
          <Menu size={22} />
        </button>
      </div>
    </header>
  );
}

export function SectionTitle({
  actionHref,
  actionLabel = "ดูทั้งหมด",
  subtitle,
  title,
}: {
  actionHref?: string;
  actionLabel?: string;
  subtitle: string;
  title: string;
}) {
  return (
    <div className="mb-6 flex items-end justify-between gap-4">
      <div>
        <h2 className="text-2xl font-semibold text-white md:text-3xl">{title}</h2>
        <p className="mt-1 text-xs font-medium uppercase tracking-[0.08em] text-emerald-400">
          {subtitle}
        </p>
      </div>
      {actionHref ? (
        <Link
          className="shrink-0 text-sm font-medium text-white/85 transition hover:text-emerald-300"
          href={actionHref}
        >
          {actionLabel}
        </Link>
      ) : null}
    </div>
  );
}

export function PageIntro({
  eyebrow,
  title,
  description,
}: {
  eyebrow: string;
  title: string;
  description: string;
}) {
  return (
    <section className="mx-auto max-w-7xl px-5 pb-10 pt-32">
      <p className="text-xs font-medium uppercase tracking-[0.18em] text-emerald-400">
        {eyebrow}
      </p>
      <h1 className="mt-4 max-w-4xl text-4xl font-bold leading-tight text-white md:text-6xl">
        {title}
      </h1>
      <p className="mt-5 max-w-3xl text-base leading-8 text-white/72 md:text-lg">
        {description}
      </p>
    </section>
  );
}

export function GameCard({
  featured,
  image,
  name,
  slug,
}: {
  featured?: boolean;
  image: string;
  name: string;
  slug?: string;
}) {
  const href = slug ? `/games/${slug}` : "/games";

  return (
    <article
      className={`rounded-[28px] border bg-[#161d26]/80 p-2.5 text-center transition hover:-translate-y-1 hover:border-emerald-400 hover:shadow-[0_0_24px_rgba(0,207,127,0.22)] ${
        featured
          ? "border-emerald-400 shadow-[0_0_24px_rgba(0,207,127,0.35)]"
          : "border-[#586c64]/70"
      }`}
    >
      <div className="relative aspect-square overflow-hidden rounded-[22px]">
        <Image
          alt={name}
          className="object-cover"
          fill
          sizes="(min-width: 1024px) 202px, 42vw"
          src={assetPath(image)}
        />
      </div>
      <h3 className="mt-4 min-h-5 text-sm font-medium text-white">{name}</h3>
      <Link
        className={`mt-3 inline-flex h-10 items-center justify-center rounded-2xl px-5 text-base font-semibold transition ${
          featured
            ? "border border-emerald-400 text-emerald-400 hover:bg-emerald-400 hover:text-white"
            : "bg-emerald-500 text-white hover:bg-emerald-400"
        }`}
        href={href}
      >
        เติมเกม
      </Link>
    </article>
  );
}

export function NewsCard({
  slug,
  image,
  title,
  excerpt,
  featured,
}: {
  excerpt?: string;
  featured?: boolean;
  image: string;
  slug?: string;
  title: string;
}) {
  const href = slug ? `/news/${slug}` : "/news";

  if (featured) {
    return (
      <article className="rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80 p-4">
        <div className="relative aspect-[16/9] overflow-hidden rounded-[26px]">
          <Image
            alt={title}
            className="object-cover"
            fill
            sizes="(min-width: 1024px) 520px, 90vw"
            src={assetPath(image)}
          />
        </div>
        <h3 className="mt-5 max-w-xl text-lg leading-snug text-white md:text-xl">
          {title}
        </h3>
        {excerpt ? <p className="mt-3 text-sm leading-6 text-white/65">{excerpt}</p> : null}
        <div className="mt-5 flex items-center justify-between">
          <Link
            className="flex h-10 items-center justify-center rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white"
            href={href}
          >
            อ่านเพิ่มเติม
          </Link>
          <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs text-emerald-400">
            มาแรง
          </span>
        </div>
      </article>
    );
  }

  return (
    <article className="grid grid-cols-[112px_1fr] gap-4 rounded-[28px] border border-[#586c64]/70 bg-[#161d26]/80 p-4 sm:grid-cols-[168px_1fr]">
      <div className="relative aspect-square overflow-hidden rounded-[22px]">
        <Image
          alt={title}
          className="object-cover"
          fill
          sizes="168px"
          src={assetPath(image)}
        />
      </div>
      <div className="flex min-w-0 flex-col justify-center">
        <h3 className="text-sm leading-relaxed text-white md:text-base">{title}</h3>
        {excerpt ? <p className="mt-2 line-clamp-2 text-xs leading-5 text-white/60">{excerpt}</p> : null}
        <Link
          className="mt-4 flex h-10 w-fit items-center justify-center rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white"
          href={href}
        >
          อ่านเพิ่มเติม
        </Link>
      </div>
    </article>
  );
}

export function SiteFooter() {
  const settings = useSiteSettings();
  const logoPath = settings.logo_path ?? "/figma/logo-goodfriend.webp";
  const siteName = settings.site_name ?? "Good Friend Shop";
  const lineLabel = cleanSiteSettingValue(settings.contact_line) || "xxxxxxx";
  const emailLabel = cleanSiteSettingValue(settings.contact_email) || "xxxxxxx@gmail.com";
  const phoneLabel = cleanSiteSettingValue(settings.contact_phone);
  const supportHours = cleanSiteSettingValue(settings.support_hours);
  const socialItems = [
    cleanSiteSettingValue(settings.facebook_label) ? "f" : null,
    lineLabel ? "LINE" : null,
    siteName ? "GF" : null,
  ].filter((item): item is string => Boolean(item));

  return (
    <footer className="mt-12 border-t border-white/5 bg-[rgba(18,16,26,0.35)]">
      <div className="mx-auto grid max-w-6xl gap-10 px-5 py-16 lg:grid-cols-[1.2fr_0.8fr_1fr]">
        <div>
          <div className="relative h-20 w-64">
            <Image
              alt={siteName}
              className="object-contain object-left"
              fill
              sizes="256px"
              src={assetPath(logoPath)}
            />
          </div>
          <p className="mt-1 font-medium text-emerald-400">
            {settings.footer_tagline}
          </p>
          <p className="mt-5 max-w-md text-sm leading-7 text-white/80">
            {settings.footer_description}
          </p>
        </div>
        <div>
          <h3 className="font-semibold text-white">ติดต่อเรา</h3>
          <div className="mt-6 space-y-4 text-sm text-white/85">
            <p className="flex items-center gap-3">
              <MessageCircle className="text-white" size={20} />
              {lineLabel}
            </p>
            <p className="flex items-center gap-3">
              <Mail className="text-white" size={20} />
              {emailLabel}
            </p>
            {phoneLabel ? (
              <p className="flex items-center gap-3">
                <span className="grid h-5 w-5 place-items-center text-xs font-bold text-white">
                  TEL
                </span>
                {phoneLabel}
              </p>
            ) : null}
            {supportHours ? (
              <p className="flex items-center gap-3">
                <Clock className="text-white" size={20} />
                {supportHours}
              </p>
            ) : null}
          </div>
          <div className="mt-6 flex gap-3">
            {socialItems.map((item) => (
              <span
                className="grid h-11 min-w-11 place-items-center rounded-full bg-emerald-500 px-3 text-xs font-medium text-white"
                key={item}
              >
                {item}
              </span>
            ))}
          </div>
        </div>
        <div className="relative min-h-[220px]">
          <Image
            alt="Good Friend Shop mobile rewards"
            className="object-contain"
            fill
            sizes="340px"
            src={assetPath("/figma/footer-phone.webp")}
          />
        </div>
      </div>
      <div className="bg-[#07080a] px-5 py-5">
        <div className="mx-auto flex max-w-6xl flex-col items-center justify-center gap-3 text-center text-xs text-white/60">
          <span>รองรับการชำระเงินหลากหลายช่องทาง</span>
          <Image
            alt="Payment methods"
            height={36}
            src={assetPath("/figma/payments.webp")}
            width={572}
          />
        </div>
      </div>
    </footer>
  );
}

export function OrderButton() {
  return (
    <button
      className="flex h-10 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400"
      type="button"
    >
      <ShoppingBag size={16} />
      สั่งซื้อ
    </button>
  );
}
