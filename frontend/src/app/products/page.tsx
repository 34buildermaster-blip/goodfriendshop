import Image from "next/image";
import { ArrowRight } from "lucide-react";
import { PageIntro, SectionTitle, SiteFooter, SiteHeader } from "@/components/site-chrome";
import { otherProducts } from "@/lib/site-data";

export default function ProductsPage() {
  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/products" />
      <div className="mint-page-surface">
        <PageIntro
          description="พื้นที่รวมสินค้าเสริม บัตรเติมเงิน โค้ดพิเศษ และบริการช่วยเหลือที่อยู่นอกหมวดเกมกับแอพพรีเมี่ยม"
          eyebrow="Other Products"
          title="สินค้าอื่นๆ"
        />

        <section className="mx-auto max-w-7xl px-5 pb-16">
          <SectionTitle subtitle="สินค้าและบริการเพิ่มเติม" title="หมวดสินค้า" />
          <div className="grid gap-5 lg:grid-cols-3">
            {otherProducts.map(({ description, icon: Icon, image, title }) => (
              <article
                className="overflow-hidden rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80"
                key={title}
              >
                <div className="relative aspect-[4/3]">
                  <Image
                    alt={title}
                    className="object-cover"
                    fill
                    sizes="(min-width: 1024px) 420px, 90vw"
                    src={image}
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#161d26] via-transparent to-transparent" />
                </div>
                <div className="p-6">
                  <div className="mb-5 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-500 text-white">
                    <Icon size={22} />
                  </div>
                  <h2 className="text-2xl font-semibold text-white">{title}</h2>
                  <p className="mt-3 min-h-16 leading-7 text-white/70">{description}</p>
                  <button
                    className="mt-6 flex h-11 items-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white transition hover:bg-emerald-400"
                    type="button"
                  >
                    ดูสินค้า
                    <ArrowRight size={16} />
                  </button>
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
