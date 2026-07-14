import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { NewsCard, SiteFooter, SiteHeader } from "@/components/site-chrome";
import { news } from "@/lib/site-data";
import { getNews, getNewsArticle } from "@/lib/api";
import { assetPath } from "@/lib/paths";

type NewsDetailPageProps = {
  params: Promise<{
    slug: string;
  }>;
};

export function generateStaticParams() {
  return news.map((item) => ({
    slug: item.slug,
  }));
}

export async function generateMetadata({
  params,
}: NewsDetailPageProps): Promise<Metadata> {
  const { slug } = await params;
  const article = await getNewsArticle(slug);

  return {
    title: article ? `${article.title} | Good Friend Shop` : "News | Good Friend Shop",
    description: article?.excerpt,
  };
}

function getArticleBody(title: string, excerpt: string) {
  return [
    excerpt,
    `${title} เป็นหนึ่งในหัวข้อที่ทีม Good Friend Shop เลือกมาอัปเดตให้ลูกค้าติดตาม เพราะเกี่ยวข้องกับกระแสเกม กิจกรรม หรือบริการที่ผู้เล่นให้ความสนใจในช่วงนี้`,
    "สำหรับลูกค้าที่ติดตามข่าวสารจากร้าน สามารถใช้หน้านี้เช็กข้อมูลเบื้องต้นก่อนเลือกเติมเกม ซื้อแพ็กแอพพรีเมี่ยม หรือรอโปรโมชันรอบใหม่จากทีมงาน",
    "รายละเอียดเพิ่มเติมของแต่ละกิจกรรมจะสามารถเชื่อมกับระบบหลังบ้านได้ในขั้นต่อไป เช่น รายละเอียดโปรโมชัน เงื่อนไข วันหมดเขต และปุ่มสั่งซื้อหรือสมัครกิจกรรม",
  ];
}

export default async function NewsDetailPage({ params }: NewsDetailPageProps) {
  const { slug } = await params;
  const article = await getNewsArticle(slug);

  if (!article) {
    notFound();
  }

  const related = (await getNews()).filter((item) => item.slug !== article.slug);
  const paragraphs = getArticleBody(article.title, article.excerpt);

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/news" />
      <div className="mint-page-surface pt-[73px]">
        <section className="mx-auto grid max-w-[1230px] gap-10 px-5 py-10 lg:grid-cols-[1fr_440px]">
          <article>
            <Link
              className="mb-6 inline-flex items-center gap-2 text-sm font-medium text-white/70 transition hover:text-emerald-300"
              href="/news"
            >
              <ArrowLeft size={16} />
              กลับหน้ากิจกรรม
            </Link>

            <div className="relative aspect-[16/9] overflow-hidden rounded-[28px]">
              <Image
                alt={article.title}
                className="object-cover"
                fill
                priority
                sizes="(min-width: 1024px) 780px, 100vw"
                src={assetPath(article.image)}
              />
            </div>

            <h1 className="mt-8 max-w-4xl text-3xl font-semibold leading-tight text-white md:text-5xl">
              {article.title}
            </h1>
            <p className="mt-2 text-xs text-white/60">
              By {article.category} · {article.date}
            </p>

            {article.content ? (
              <div
                className="news-content mt-8 text-lg leading-8 text-white/82"
                dangerouslySetInnerHTML={{ __html: article.content }}
              />
            ) : (
              <div className="mt-8 space-y-6 text-lg leading-8 text-white/82">
                {paragraphs.map((paragraph) => (
                  <p key={paragraph}>{paragraph}</p>
                ))}
              </div>
            )}

            <div className="mt-12 rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80 p-6">
              <p className="text-sm font-medium text-emerald-300">หมายเหตุ</p>
              <p className="mt-3 leading-7 text-white/70">
                ข้อมูลนี้เป็นหน้าตัวอย่างสำหรับวาง layout ก่อนเชื่อมระบบจริง
                เมื่อต่อ backend แล้วสามารถดึงเนื้อหาข่าวเต็ม รูปภาพ SEO และสถานะกิจกรรมจากฐานข้อมูลได้
              </p>
            </div>
          </article>

          <aside className="space-y-5 lg:pt-[520px]">
            {related.map((item) => (
              <NewsCard key={item.slug} {...item} />
            ))}
            <div className="flex items-center justify-center gap-3 pt-2 text-xs text-white/70">
              <span>Share this:</span>
              {[
                { text: "f", label: "Facebook", color: "bg-blue-600" },
                { text: "LINE", label: "LINE", color: "bg-green-500" },
                { text: "X", label: "Twitter", color: "bg-sky-500" },
              ].map(({ color, label, text }) => (
                <button
                  aria-label={label}
                  className={`grid h-11 min-w-11 place-items-center rounded-full px-3 text-xs font-semibold ${color} text-white`}
                  key={label}
                  type="button"
                >
                  {text}
                </button>
              ))}
            </div>
          </aside>
        </section>

        <SiteFooter />
      </div>
    </main>
  );
}
