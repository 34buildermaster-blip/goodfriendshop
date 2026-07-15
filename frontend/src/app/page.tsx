"use client";

import Image from "next/image";
import Link from "next/link";
import { useEffect, useState } from "react";
import {
  ChevronLeft,
  ChevronRight,
  ClipboardList,
  Mail,
  Menu,
  MessageCircle,
  Search,
  ShoppingBag,
  X,
  Zap,
} from "lucide-react";
import { AccountButton } from "@/components/account-button";
import { PremiumOrderModal } from "@/components/premium-order-modal";
import { getGames, getNews, getPremiumProducts, getSiteContent } from "@/lib/api";
import { assetPath } from "@/lib/paths";

const navItems = [
  { label: "หน้าหลัก", href: "/" },
  { label: "แอพพรีเมี่ยมทั้งหมด", href: "/premium" },
  { label: "สินค้าอื่นๆ", href: "/products" },
  { label: "เติมเกมส์ออนไลน์", href: "/games" },
  { label: "กิจกรรม", href: "/news" },
  { label: "ติดต่อเรา", href: "/contact" },
];

const games = [
  { name: "Mobile Legends", image: "/figma/game-mobile-legends.webp", featured: true },
  { name: "PUBG Mobile (Thai)", image: "/figma/game-pubg.webp" },
  { name: "RoV Mobile", image: "/figma/game-rov.webp" },
  { name: "Delta Force (Garena)", image: "/figma/game-delta-garena.webp" },
  { name: "Free Fire", image: "/figma/game-free-fire.webp" },
  { name: "Delta Force (Steam)", image: "/figma/game-delta-steam.webp" },
  { name: "League of Legends", image: "/figma/game-lol.webp" },
  { name: "Identity V", image: "/figma/game-identity-v.webp" },
  { name: "FC Mobile (FIFA Mobile)", image: "/figma/game-fc-mobile.webp" },
  { name: "MU Archangel", image: "/figma/game-mu.webp" },
];

type PremiumProduct = {
  id: string;
  title: string;
  image?: string;
  price: string;
  duration: string;
  warranty: string;
  platform: string;
  description: string;
  details: string[];
};

const premiumProducts: PremiumProduct[] = [
  {
    id: "nf-1-day",
    title: "แอคนอกมีเคลม NF 1 วัน (มือถือ)",
    price: "฿20.00",
    duration: "ใช้งาน 1 วัน",
    warranty: "มีเคลมระหว่างระยะเวลาใช้งาน",
    platform: "รองรับมือถือ",
    description:
      "เหมาะสำหรับลูกค้าที่ต้องการทดลองใช้งาน Netflix แบบสั้น ๆ จ่ายตรง ไม่ต้องเติม coin และรับข้อมูลหลังสั่งซื้อ",
    details: [
      "บัญชีพร้อมใช้งานหลังชำระเงิน",
      "เหมาะกับการดูผ่านมือถือ",
      "หากเข้าใช้งานไม่ได้ในระยะเวลา มีบริการเคลมตามเงื่อนไขร้าน",
    ],
  },
  {
    id: "nf-3-day",
    title: "แอคนอกมีเคลม NF 3 วัน (มือถือ)",
    price: "฿59.00",
    duration: "ใช้งาน 3 วัน",
    warranty: "มีเคลมระหว่างระยะเวลาใช้งาน",
    platform: "รองรับมือถือ",
    description:
      "แพ็กเกจยอดนิยมสำหรับใช้งานต่อเนื่องหลายวัน ราคาคุ้มกว่าแบบรายวัน พร้อมข้อมูลเข้าใช้งานหลังสั่งซื้อ",
    details: [
      "บัญชีพร้อมใช้งานหลังชำระเงิน",
      "ระยะเวลาใช้งาน 3 วันเต็มตามรอบร้าน",
      "มีทีมช่วยตรวจสอบหากพบปัญหาระหว่างใช้งาน",
    ],
  },
  {
    id: "nf-7-day",
    title: "แอคนอกมีเคลม NF 7 วัน (มือถือ)",
    price: "฿99.00",
    duration: "ใช้งาน 7 วัน",
    warranty: "มีเคลมระหว่างระยะเวลาใช้งาน",
    platform: "รองรับมือถือ",
    description:
      "เหมาะสำหรับลูกค้าที่ต้องการใช้งานทั้งสัปดาห์ ราคาประหยัดกว่า ซื้อครั้งเดียวใช้งานได้ยาวขึ้น",
    details: [
      "บัญชีพร้อมใช้งานหลังชำระเงิน",
      "ใช้งานได้ 7 วันตามเงื่อนไขแพ็กเกจ",
      "เหมาะกับลูกค้าที่ต้องการความคุ้มค่าระยะยาว",
    ],
  },
];

const news = [
  {
    title: "ByteDance บุกตลาด MOBA ซื้อกิจการ Moonton เจ้าของเกม Mobile Legends",
    image: "/figma/news-main.webp",
    featured: true,
  },
  {
    title:
      "เรื่องเล่าเช้านี้รายการข่าวชื่อดังหยิบนำข่าว RoV ทีม Dtac Talon คว้าแชมป์โลก AWC 2021",
    image: "/figma/news-champions.webp",
  },
  {
    title: "[เปิดรับสมัคร] การแข่งขัน Garena® Delta Force Road to ACL 2025",
    image: "/figma/news-delta.webp",
  },
];

const heroSlides = [
  {
    eyebrow: "SAFE TOPUP",
    title: "เติมเกมส์ปลอดภัย",
    highlight: "มั่นใจได้ ไม่ต้องกลัวโดนแบน",
    quote: "ร้านเติมเกมที่เชื่อถือได้! ปลอดภัย 100% เล่นต่อได้ไม่มีสะดุด",
    image: "/figma/hero.webp",
    href: "/games",
    cta: "เริ่มเติมเกม",
  },
  {
    eyebrow: "HOT DEAL",
    title: "แอพพรีเมี่ยมราคาดี",
    highlight: "ดูหนัง ฟังเพลง ใช้คุ้มกว่าเดิม",
    quote: "แพ็กยอดนิยมพร้อมรายละเอียดชัดเจน เลือกง่าย สั่งซื้อไว",
    image: "/figma/premium-netflix.webp",
    href: "/premium",
    cta: "ดูแพ็กทั้งหมด",
  },
  {
    eyebrow: "EVENT UPDATE",
    title: "ข่าวเกมและกิจกรรม",
    highlight: "อัปเดตไว ไม่พลาดทุกกระแส",
    quote: "รวมข่าว เกมฮิต ทัวร์นาเมนต์ และโปรโมชันจาก Good Friend Shop",
    image: "/figma/news-main.webp",
    href: "/news",
    cta: "อ่านข่าวล่าสุด",
  },
];

function SectionTitle({
  actionHref,
  title,
  subtitle,
}: {
  actionHref?: string;
  title: string;
  subtitle: string;
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
          ดูทั้งหมด
        </Link>
      ) : null}
    </div>
  );
}

function Header({
  logoPath,
  logoText = "Good Friend Shop",
}: {
  logoPath?: string;
  logoText?: string;
}) {
  return (
    <header className="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-[#0e0d17]/80 backdrop-blur-xl">
      <div className="mx-auto flex h-[82px] max-w-[1440px] items-center gap-5 px-5 lg:px-11">
        <Link className="relative flex h-16 w-[230px] shrink-0 items-center text-lg font-bold tracking-wide text-white" href="/">
          {logoPath ? (
            <Image
              alt={logoText}
              className="object-contain object-left"
              fill
              priority
              sizes="230px"
              src={assetPath(logoPath)}
            />
          ) : (
            logoText
          )}
        </Link>

        <nav className="hidden flex-1 items-center justify-center gap-8 text-sm font-medium text-white lg:flex">
          {navItems.map((item, index) => (
            <Link
              className={`relative py-6 transition hover:text-emerald-300 ${
                index === 0 ? "text-emerald-400" : ""
              }`}
              href={item.href}
              key={item.href}
            >
              {item.label}
              {index === 0 ? (
                <span className="absolute inset-x-1 -bottom-px h-1 rounded-full bg-emerald-400" />
              ) : null}
            </Link>
          ))}
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

function GameCard({
  image,
  name,
  featured,
  slug,
}: {
  image: string;
  name: string;
  featured?: boolean;
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

function PremiumCard({
  image,
  onDetails,
  onOrder,
  price,
  title,
}: {
  image?: string;
  onDetails: () => void;
  onOrder: () => void;
  price: string;
  title: string;
}) {
  return (
    <article className="rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80 p-4">
      <div className="relative aspect-square overflow-hidden rounded-[28px]">
        <Image
          alt={title}
          className="object-cover"
          fill
          sizes="(min-width: 1024px) 420px, 90vw"
          src={assetPath(image ?? "/figma/premium-netflix.webp")}
        />
      </div>
      <div className="px-3 pb-2 pt-6">
        <div className="mb-3 flex items-center justify-between gap-3">
          <h3 className="text-lg font-semibold text-white md:text-xl">{title}</h3>
          <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-400">
            ขายดี
          </span>
        </div>
        <p className="text-sm text-emerald-400">ราคาสินค้า</p>
        <p className="mt-1 text-2xl font-bold text-[#ffc012]">{price}</p>
        <div className="mt-5 grid grid-cols-[1fr_auto] rounded-full bg-[#040f1c] p-1.5">
          <button
            className="flex h-10 items-center justify-center gap-2 rounded-full text-sm font-medium text-white transition hover:bg-white/10"
            onClick={onDetails}
            type="button"
          >
            <ClipboardList size={16} />
            รายละเอียด
          </button>
          <button
            className="flex h-10 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400"
            onClick={onOrder}
            type="button"
          >
            <ShoppingBag size={16} />
            สั่งซื้อ
          </button>
        </div>
      </div>
    </article>
  );
}

function ProductDetailModal({
  onClose,
  onOrder,
  product,
}: {
  onClose: () => void;
  onOrder: () => void;
  product: PremiumProduct;
}) {
  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        onClose();
      }
    };

    document.addEventListener("keydown", onKeyDown);
    return () => document.removeEventListener("keydown", onKeyDown);
  }, [onClose]);

  return (
    <div
      aria-labelledby="product-detail-title"
      aria-modal="true"
      className="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 px-4 py-6 backdrop-blur-md"
      role="dialog"
    >
      <button
        aria-label="ปิดรายละเอียดสินค้า"
        className="absolute inset-0 cursor-default"
        onClick={onClose}
        type="button"
      />
      <section className="relative grid max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-[32px] border border-emerald-400/30 bg-[#101923] shadow-[0_0_60px_rgba(0,207,127,0.2)] md:grid-cols-[0.9fr_1.1fr]">
        <button
          aria-label="ปิด"
          className="absolute right-4 top-4 z-10 grid h-10 w-10 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
          onClick={onClose}
          type="button"
        >
          <X size={20} />
        </button>

        <div className="relative min-h-[260px] bg-[#08111c] md:min-h-[520px]">
          <Image
            alt={product.title}
            className="object-cover"
            fill
            sizes="(min-width: 768px) 360px, 100vw"
            src={assetPath(product.image ?? "/figma/premium-netflix.webp")}
          />
          <div className="absolute inset-0 bg-gradient-to-t from-[#101923] via-transparent to-transparent md:bg-gradient-to-r" />
        </div>

        <div className="overflow-y-auto p-6 md:p-8">
          <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300">
            แอพพรีเมี่ยมขายดี
          </span>
          <h2
            className="mt-4 pr-10 text-2xl font-semibold leading-tight text-white md:text-3xl"
            id="product-detail-title"
          >
            {product.title}
          </h2>
          <p className="mt-4 leading-7 text-white/75">{product.description}</p>

          <div className="mt-6 grid gap-3 sm:grid-cols-3">
            {[
              ["ระยะเวลา", product.duration],
              ["การเคลม", product.warranty],
              ["แพลตฟอร์ม", product.platform],
            ].map(([label, value]) => (
              <div
                className="rounded-2xl border border-white/10 bg-white/[0.04] p-4"
                key={label}
              >
                <p className="text-xs text-emerald-300">{label}</p>
                <p className="mt-1 text-sm font-medium text-white">{value}</p>
              </div>
            ))}
          </div>

          <div className="mt-6 rounded-3xl border border-white/10 bg-[#07111c] p-5">
            <h3 className="font-semibold text-white">รายละเอียดสินค้า</h3>
            <ul className="mt-4 space-y-3 text-sm leading-6 text-white/75">
              {product.details.map((detail) => (
                <li className="flex gap-3" key={detail}>
                  <span className="mt-2 h-2 w-2 shrink-0 rounded-full bg-emerald-400" />
                  <span>{detail}</span>
                </li>
              ))}
            </ul>
          </div>

          <div className="mt-6 flex flex-col gap-4 rounded-3xl bg-emerald-500/10 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-sm text-emerald-300">ราคาสินค้า</p>
              <p className="text-3xl font-bold text-[#ffc012]">
                {product.price}
              </p>
            </div>
            <button
              className="flex h-12 items-center justify-center gap-2 rounded-full bg-emerald-500 px-7 text-base font-semibold text-white transition hover:bg-emerald-400"
              onClick={onOrder}
              type="button"
            >
              <ShoppingBag size={18} />
              สั่งซื้อ
            </button>
          </div>
        </div>
      </section>
    </div>
  );
}

function NewsCard({
  image,
  title,
  featured,
}: {
  image: string;
  title: string;
  featured?: boolean;
}) {
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
        <div className="mt-5 flex items-center justify-between">
          <button className="rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white">
            อ่านเพิ่มเติม
          </button>
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
        <h3 className="text-sm leading-relaxed text-white md:text-base">
          {title}
        </h3>
        <button className="mt-4 w-fit rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white">
          อ่านเพิ่มเติม
        </button>
      </div>
    </article>
  );
}

export default function Home() {
  const [homeGames, setHomeGames] = useState(games);
  const [homePremiumProducts, setHomePremiumProducts] = useState(premiumProducts);
  const [homeNews, setHomeNews] = useState(news);
  const [homeHeroSlides, setHomeHeroSlides] = useState(heroSlides);
  const [homeAnnouncements, setHomeAnnouncements] = useState([
    "ช่องทางติดต่อ Tel: xxx-xxx-xxxx  Line : xxxxxxxxxx  Facebook : xxxxxx - รับเติมเกมส์ราคาถูก",
  ]);
  const [siteSettings, setSiteSettings] = useState({
    site_name: "Good Friend Shop",
    logo_path: "/figma/logo-goodfriend.webp" as string | undefined,
    footer_tagline: "เติมเกมไวเหมือนเพื่อนรู้ใจ ราคาสบายกระเป๋าที่สุด!",
    footer_description:
      "GoodFriendShop คือเพื่อนแท้ของเกมเมอร์ พร้อมสนับสนุนให้คุณเล่นต่อได้ไม่มีสะดุด",
    contact_line: "xxxxxxx",
    contact_email: "xxxxxx@gmail.com",
  });
  const [selectedProduct, setSelectedProduct] = useState<PremiumProduct | null>(
    null,
  );
  const [orderingProduct, setOrderingProduct] = useState<PremiumProduct | null>(
    null,
  );
  const [currentHeroSlide, setCurrentHeroSlide] = useState(0);
  const activeHeroSlide = homeHeroSlides[currentHeroSlide] ?? homeHeroSlides[0] ?? heroSlides[0];

  useEffect(() => {
    let active = true;

    Promise.all([getGames(), getPremiumProducts(), getNews(), getSiteContent()]).then(
      ([gameItems, premiumItems, newsItems, siteContent]) => {
        if (!active) {
          return;
        }

        setHomeGames(gameItems);
        setHomePremiumProducts(premiumItems);
        setHomeNews(newsItems);
        if (siteContent) {
          setSiteSettings((current) => ({ ...current, ...siteContent.settings }));
          if (siteContent.hero_slides.length) {
            setHomeHeroSlides(siteContent.hero_slides);
            setCurrentHeroSlide(0);
          }
          if (siteContent.announcements.length) {
            setHomeAnnouncements(siteContent.announcements.map((item) => item.message));
          }
        }
      },
    );

    return () => {
      active = false;
    };
  }, []);

  useEffect(() => {
    const intervalId = window.setInterval(() => {
      setCurrentHeroSlide((current) => (current + 1) % homeHeroSlides.length);
    }, 5600);

    return () => window.clearInterval(intervalId);
  }, [homeHeroSlides.length]);

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <Header logoPath={siteSettings.logo_path} logoText={siteSettings.site_name} />

      <section className="relative min-h-[860px] overflow-hidden pt-[82px]">
        {homeHeroSlides.map((slide, index) => (
          <Image
            alt={slide.title}
            className={`object-cover transition duration-700 ${
              index === currentHeroSlide ? "opacity-100 scale-100" : "opacity-0 scale-105"
            }`}
            fill
            key={slide.title}
            priority={index === 0}
            sizes="100vw"
            src={assetPath(slide.image)}
          />
        ))}
        <div className="absolute inset-0 bg-gradient-to-b from-[#0e0d17]/10 via-[#0e0d17]/35 to-[#0e0d17]" />
        <div className="absolute inset-0 bg-[linear-gradient(90deg,rgba(14,13,23,0.92)_0%,rgba(14,13,23,0.24)_48%,rgba(14,13,23,0.82)_100%)]" />
        <div className="absolute inset-x-0 bottom-0 h-[58%] bg-gradient-to-t from-[#0e0d17] via-[#0e0d17]/80 to-transparent" />

        <div className="relative z-10 mx-auto flex min-h-[787px] max-w-7xl flex-col justify-end px-5 pb-20">
          <div className="grid items-end gap-8 lg:grid-cols-[1fr_360px]">
            <div className="text-center lg:text-left">
              <div className="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/15 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-300">
                <Zap size={15} />
                {activeHeroSlide.eyebrow}
              </div>
              <h1 className="text-5xl font-bold leading-none md:text-7xl lg:text-[104px]">
                {activeHeroSlide.title}
              </h1>
              <p className="mt-4 text-2xl font-semibold text-emerald-400 md:text-5xl">
                {activeHeroSlide.highlight}
              </p>
              <p className="mt-4 max-w-4xl text-lg font-semibold text-[#ffc012] md:text-3xl">
                &quot;{activeHeroSlide.quote}&quot;
              </p>
              <div className="mt-7 flex flex-col items-center gap-3 sm:flex-row lg:justify-start">
                <Link
                  className="flex h-12 items-center justify-center rounded-full bg-emerald-500 px-7 text-base font-semibold text-white shadow-[0_0_28px_rgba(16,185,129,0.35)] transition hover:bg-emerald-400"
                  href={activeHeroSlide.href}
                >
                  {activeHeroSlide.cta}
                </Link>
                <div className="flex items-center gap-2">
                  <button
                    aria-label="สไลด์ก่อนหน้า"
                    className="grid h-12 w-12 place-items-center rounded-full border border-white/15 bg-white/10 text-white transition hover:border-emerald-400 hover:text-emerald-300"
                    onClick={() =>
                      setCurrentHeroSlide(
                        (currentHeroSlide - 1 + homeHeroSlides.length) % homeHeroSlides.length,
                      )
                    }
                    type="button"
                  >
                    <ChevronLeft size={21} />
                  </button>
                  <button
                    aria-label="สไลด์ถัดไป"
                    className="grid h-12 w-12 place-items-center rounded-full border border-white/15 bg-white/10 text-white transition hover:border-emerald-400 hover:text-emerald-300"
                    onClick={() =>
                      setCurrentHeroSlide((currentHeroSlide + 1) % homeHeroSlides.length)
                    }
                    type="button"
                  >
                    <ChevronRight size={21} />
                  </button>
                </div>
              </div>
            </div>

            <div className="hidden lg:block">
              <div className="mb-4 flex items-center justify-between text-xs font-medium uppercase tracking-[0.14em] text-white/55">
                <span>Featured Slides</span>
                <span>
                  {String(currentHeroSlide + 1).padStart(2, "0")} /{" "}
                  {String(homeHeroSlides.length).padStart(2, "0")}
                </span>
              </div>
              <div className="grid gap-3">
                {homeHeroSlides.map((slide, index) => (
                  <button
                    className={`grid grid-cols-[72px_1fr] items-center gap-4 rounded-[24px] border p-2 text-left transition ${
                      index === currentHeroSlide
                        ? "border-emerald-400 bg-emerald-500/18"
                        : "border-white/10 bg-white/[0.06] hover:border-white/30"
                    }`}
                    key={slide.title}
                    onClick={() => setCurrentHeroSlide(index)}
                    type="button"
                  >
                    <span className="relative aspect-square overflow-hidden rounded-[18px]">
                      <Image
                        alt={slide.title}
                        className="object-cover"
                        fill
                        sizes="72px"
                        src={assetPath(slide.image)}
                      />
                    </span>
                    <span>
                      <span className="block text-xs text-emerald-300">
                        {slide.eyebrow}
                      </span>
                      <span className="mt-1 block text-base font-semibold text-white">
                        {slide.title}
                      </span>
                    </span>
                  </button>
                ))}
              </div>
            </div>
          </div>

          <div className="mt-8 flex justify-center gap-2 lg:hidden">
            {homeHeroSlides.map((slide, index) => (
              <button
                aria-label={`เปิดสไลด์ ${index + 1}`}
                className={`h-2.5 rounded-full transition ${
                  index === currentHeroSlide ? "w-9 bg-emerald-400" : "w-2.5 bg-white/35"
                }`}
                key={slide.title}
                onClick={() => setCurrentHeroSlide(index)}
                type="button"
              />
            ))}
          </div>

          <div className="mt-8 flex w-full max-w-6xl flex-col items-stretch gap-3 rounded-2xl bg-[#161d26]/90 p-2 text-left shadow-2xl shadow-black/30 md:flex-row md:items-center">
            <span className="hidden w-fit rounded-2xl bg-emerald-500 px-5 py-3 text-lg font-semibold leading-none text-white md:block md:text-xl">
              ประกาศ :
            </span>
            <div className="announcement-ticker relative h-10 w-full min-w-0 max-w-full flex-none overflow-hidden rounded-xl bg-black/10 text-sm leading-none text-white/70 md:h-auto md:flex-1 md:bg-transparent md:px-2 md:text-lg">
              <div className="announcement-ticker-track flex h-full w-max items-center whitespace-nowrap md:gap-10">
                {[0, 1].map((item) => (
                  <span className="announcement-ticker-item flex h-full shrink-0 items-center px-6 md:px-0" key={item}>
                    {homeAnnouncements.join("   •   ")}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      <div className="mint-page-surface">
      <section className="relative mx-auto max-w-7xl px-5 py-14">
        <SectionTitle
          actionHref="/games"
          title="เติมเกมส์ออนไลน์"
          subtitle="Game Topup Online"
        />
        <div className="relative grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
          {homeGames.map((game) => (
            <GameCard key={game.name} {...game} />
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-[1398px] px-5 py-14">
        <SectionTitle
          actionHref="/premium"
          title="แอพพรีเมี่ยมขายดี"
          subtitle="แอพพรีเมียมแนะนำ"
        />
        <div className="grid gap-5 lg:grid-cols-3">
          {homePremiumProducts.map((product) => (
            <PremiumCard
              key={product.id}
              {...product}
              onDetails={() => setSelectedProduct(product)}
              onOrder={() => setOrderingProduct(product)}
            />
          ))}
        </div>
      </section>

      <section className="relative mx-auto max-w-7xl px-5 py-14">
        <SectionTitle
          actionHref="/news"
          title="ข่าวสารล่าสุด"
          subtitle="New & Event"
        />
        <div className="relative grid gap-5 lg:grid-cols-[1fr_1.28fr]">
          <NewsCard {...homeNews[0]} />
          <div className="grid gap-5">
            {homeNews.slice(1).map((item) => (
              <NewsCard key={item.title} {...item} />
            ))}
          </div>
        </div>
      </section>

      <footer className="mt-12 border-t border-white/5 bg-[rgba(18,16,26,0.35)]">
        <div className="mx-auto grid max-w-6xl gap-10 px-5 py-16 lg:grid-cols-[1.2fr_0.8fr_1fr]">
          <div>
            <div className="relative h-20 w-64">
              <Image
                alt={siteSettings.site_name}
                className="object-contain object-left"
                fill
                sizes="256px"
                src={assetPath(siteSettings.logo_path ?? "/figma/logo-goodfriend.webp")}
              />
            </div>
            <p className="mt-1 font-medium text-emerald-400">
              {siteSettings.footer_tagline}
            </p>
            <p className="mt-5 max-w-md text-sm leading-7 text-white/80">
              {siteSettings.footer_description}
            </p>
          </div>
          <div>
            <h3 className="font-semibold text-white">ติดต่อเรา</h3>
            <div className="mt-6 space-y-4 text-sm text-white/85">
              <p className="flex items-center gap-3">
                <MessageCircle className="text-white" size={20} />
                {siteSettings.contact_line}
              </p>
              <p className="flex items-center gap-3">
                <Mail className="text-white" size={20} />
                {siteSettings.contact_email}
              </p>
            </div>
            <div className="mt-6 flex gap-3">
              {["f", "LINE", "X"].map((item) => (
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
      </div>

      {selectedProduct ? (
        <ProductDetailModal
          onClose={() => setSelectedProduct(null)}
          onOrder={() => {
            setOrderingProduct(selectedProduct);
            setSelectedProduct(null);
          }}
          product={selectedProduct}
        />
      ) : null}

      {orderingProduct ? (
        <PremiumOrderModal
          onClose={() => setOrderingProduct(null)}
          product={orderingProduct}
        />
      ) : null}
    </main>
  );
}
