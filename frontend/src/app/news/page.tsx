import Image from "next/image";
import Link from "next/link";
import { CalendarDays, Megaphone, Trophy } from "lucide-react";
import { PageIntro, SiteFooter, SiteHeader } from "@/components/site-chrome";
import { news } from "@/lib/site-data";
import { assetPath } from "@/lib/paths";

const categories = ["ทั้งหมด", "ข่าวเกม", "กิจกรรม", "การแข่งขัน", "โปรโมชัน"];

const activityHighlights = [
  {
    title: "กิจกรรมเติมเกมประจำสัปดาห์",
    description: "ติดตามโปรและแพ็กแนะนำสำหรับเกมยอดนิยม อัปเดตเป็นรอบ",
    icon: Megaphone,
  },
  {
    title: "ทัวร์นาเมนต์และอีสปอร์ต",
    description: "รวมข่าวการแข่งขัน ทีมดัง และรายการสมัครแข่งที่น่าสนใจ",
    icon: Trophy,
  },
  {
    title: "อัปเดตรายการใหม่",
    description: "แจ้งสินค้าเข้าใหม่ แอพพรีเมี่ยม และบริการเสริมจากร้าน",
    icon: CalendarDays,
  },
];

export default function NewsPage() {
  const featured = news[0];
  const newsList = [...news, ...news.slice(1), news[0]];

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/news" />
      <div className="mint-page-surface">
        <PageIntro
          description="รวมข่าวสาร กิจกรรม โปรโมชัน และอัปเดตจาก Good Friend Shop ให้ลูกค้าติดตามได้ในหน้าเดียว"
          eyebrow="News & Event"
          title="กิจกรรมและข่าวสารทั้งหมด"
        />

        <section className="mx-auto max-w-7xl px-5 pb-14">
          <div className="grid gap-5 lg:grid-cols-[1.25fr_0.75fr]">
            <article className="overflow-hidden rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80">
              <div className="relative aspect-[16/9]">
                <Image
                  alt={featured.title}
                  className="object-cover"
                  fill
                  priority
                  sizes="(min-width: 1024px) 760px, 100vw"
                  src={assetPath(featured.image)}
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#161d26] via-[#161d26]/10 to-transparent" />
                <span className="absolute left-5 top-5 rounded-full bg-emerald-500 px-4 py-2 text-xs font-semibold text-white">
                  ข่าวเด่น
                </span>
              </div>
              <div className="flex flex-col p-5 md:p-7">
                <p className="text-xs text-emerald-300">
                  {featured.category} · {featured.date}
                </p>
                <h2 className="mt-3 max-w-3xl text-2xl font-semibold leading-tight text-white md:text-4xl">
                  {featured.title}
                </h2>
                <p className="mt-4 max-w-3xl leading-7 text-white/70">
                  {featured.excerpt}
                </p>
                <Link
                  className="mt-6 flex h-12 w-fit items-center justify-center rounded-full bg-emerald-500 px-6 text-sm font-semibold text-white transition hover:bg-emerald-400"
                  href={`/news/${featured.slug}`}
                >
                  อ่านเพิ่มเติม
                </Link>
              </div>
            </article>

            <div className="grid gap-5">
              {activityHighlights.map(({ description, icon: Icon, title }) => (
                <article
                  className="rounded-[28px] border border-[#586c64]/70 bg-[#161d26]/80 p-6"
                  key={title}
                >
                  <div className="mb-5 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-500 text-white">
                    <Icon size={22} />
                  </div>
                  <h3 className="text-xl font-semibold text-white">{title}</h3>
                  <p className="mt-3 leading-7 text-white/70">{description}</p>
                </article>
              ))}
            </div>
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 pb-16">
          <div className="mb-8 flex flex-wrap gap-3">
            {categories.map((category, index) => (
              <button
                className={`rounded-full px-5 py-2.5 text-sm font-medium transition ${
                  index === 0
                    ? "bg-emerald-500 text-white"
                    : "border border-white/10 bg-white/[0.04] text-white/75 hover:border-emerald-400 hover:text-white"
                }`}
                key={category}
                type="button"
              >
                {category}
              </button>
            ))}
          </div>

          <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            {newsList.map((item, index) => (
              <article
                className="overflow-hidden rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80"
                key={`${item.slug}-${index}`}
              >
                <div className="relative aspect-[16/10]">
                  <Image
                    alt={item.title}
                    className="object-cover"
                    fill
                    sizes="(min-width: 1280px) 390px, (min-width: 768px) 48vw, 100vw"
                    src={assetPath(item.image)}
                  />
                </div>
                <div className="flex min-h-[254px] flex-col p-5">
                  <div className="mb-3 flex items-center justify-between gap-3">
                    <p className="text-xs text-emerald-300">{item.category}</p>
                    <p className="text-xs text-white/45">{item.date}</p>
                  </div>
                  <h2 className="min-h-16 text-lg font-semibold leading-snug text-white">
                    {item.title}
                  </h2>
                  <p className="mt-3 line-clamp-2 text-sm leading-6 text-white/65">
                    {item.excerpt}
                  </p>
                  <Link
                    className="mt-auto flex h-10 w-fit items-center justify-center rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400"
                    href={`/news/${item.slug}`}
                  >
                    อ่านเพิ่มเติม
                  </Link>
                </div>
              </article>
            ))}
          </div>
        </section>

        <SiteFooter />
      </div>
    </main>
  );
}
