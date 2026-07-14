import Image from "next/image";
import { Mail, MapPin, MessageCircle, Send } from "lucide-react";
import { PageIntro, SiteFooter, SiteHeader } from "@/components/site-chrome";
import { contactCards, contactSteps } from "@/lib/site-data";
import { assetPath } from "@/lib/paths";

export default function ContactPage() {
  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/contact" />
      <div className="mint-page-surface">
        <PageIntro
          description="ติดต่อทีมงาน Good Friend Shop สำหรับสอบถามสินค้า แจ้งปัญหาออเดอร์ หรือขอให้ช่วยตรวจสอบรายการเติมเกมและแอพพรีเมี่ยม"
          eyebrow="Contact Us"
          title="ติดต่อเรา"
        />

        <section className="mx-auto grid max-w-7xl gap-6 px-5 pb-16 lg:grid-cols-[0.9fr_1.1fr]">
          <div className="space-y-5">
            {contactCards.map(({ description, icon: Icon, title, value }) => (
              <article
                className="rounded-[28px] border border-[#586c64]/70 bg-[#161d26]/80 p-6"
                key={title}
              >
                <div className="mb-5 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-500 text-white">
                  <Icon size={22} />
                </div>
                <h2 className="text-2xl font-semibold text-white">{title}</h2>
                <p className="mt-2 text-lg font-medium text-emerald-400">{value}</p>
                <p className="mt-3 leading-7 text-white/70">{description}</p>
              </article>
            ))}
          </div>

          <div className="rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80 p-5 md:p-8">
            <div className="grid gap-6 lg:grid-cols-[1fr_260px]">
              <div>
                <h2 className="text-3xl font-semibold text-white">ส่งข้อความถึงเรา</h2>
                <p className="mt-3 leading-7 text-white/70">
                  กรอกข้อมูลเบื้องต้นไว้ก่อน เมื่อเชื่อม backend แล้วฟอร์มนี้จะต่อเข้าระบบออเดอร์/แชทได้ทันที
                </p>
                <form className="mt-7 grid gap-4">
                  {["ชื่อผู้ติดต่อ", "อีเมลหรือ LINE", "หัวข้อที่ต้องการติดต่อ"].map((label) => (
                    <label className="block" key={label}>
                      <span className="text-sm text-white/70">{label}</span>
                      <input
                        className="mt-2 h-12 w-full rounded-2xl border border-white/10 bg-[#07111c] px-4 text-white outline-none transition placeholder:text-white/35 focus:border-emerald-400"
                        placeholder={label}
                      />
                    </label>
                  ))}
                  <label className="block">
                    <span className="text-sm text-white/70">รายละเอียด</span>
                    <textarea
                      className="mt-2 min-h-36 w-full resize-none rounded-2xl border border-white/10 bg-[#07111c] px-4 py-3 text-white outline-none transition placeholder:text-white/35 focus:border-emerald-400"
                      placeholder="เช่น เกมที่ต้องการเติม เลขออเดอร์ หรือปัญหาที่พบ"
                    />
                  </label>
                  <button
                    className="flex h-12 w-fit items-center gap-2 rounded-full bg-emerald-500 px-7 text-base font-semibold text-white transition hover:bg-emerald-400"
                    type="button"
                  >
                    <Send size={18} />
                    ส่งข้อความ
                  </button>
                </form>
              </div>

              <div className="relative min-h-[360px] overflow-hidden rounded-[28px] bg-[#07111c]">
                <Image
                  alt="Good Friend Shop contact support"
                  className="object-contain p-5"
                  fill
                  sizes="260px"
                  src={assetPath("/figma/footer-phone.webp")}
                />
              </div>
            </div>

            <div className="mt-8 grid gap-3 sm:grid-cols-3">
              <p className="flex items-center gap-3 rounded-2xl bg-white/[0.04] p-4 text-sm text-white/75">
                <MessageCircle className="text-emerald-400" size={18} />
                LINE: xxxxxxx
              </p>
              <p className="flex items-center gap-3 rounded-2xl bg-white/[0.04] p-4 text-sm text-white/75">
                <Mail className="text-emerald-400" size={18} />
                xxxxxxx@gmail.com
              </p>
              <p className="flex items-center gap-3 rounded-2xl bg-white/[0.04] p-4 text-sm text-white/75">
                <MapPin className="text-emerald-400" size={18} />
                Online Service
              </p>
            </div>
          </div>
        </section>

        <section className="mx-auto max-w-7xl px-5 pb-16">
          <div className="grid gap-4 rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80 p-5 md:grid-cols-4">
            {contactSteps.map(({ icon: Icon, title }, index) => (
              <div className="flex items-center gap-4 rounded-3xl bg-white/[0.04] p-4" key={title}>
                <div className="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-emerald-500 text-white">
                  <Icon size={20} />
                </div>
                <div>
                  <p className="text-xs text-emerald-300">ขั้นตอน {index + 1}</p>
                  <p className="font-semibold text-white">{title}</p>
                </div>
              </div>
            ))}
          </div>
        </section>

        <SiteFooter />
      </div>
    </main>
  );
}
