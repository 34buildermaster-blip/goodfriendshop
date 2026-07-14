import { GameCard, PageIntro, SectionTitle, SiteFooter, SiteHeader } from "@/components/site-chrome";
import { getGames } from "@/lib/api";

const categories = ["ยอดนิยม", "มือถือ", "PC", "Garena", "Steam"];

export default async function GamesPage() {
  const games = await getGames();

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/games" />
      <div className="mint-page-surface">
        <PageIntro
          description="รวมเกมออนไลน์ยอดนิยมสำหรับเติมเครดิต ไอเทม และแพ็กพิเศษ จัดวางแบบเดียวกับหน้า Home แต่ขยายพื้นที่ให้ค้นหาเกมได้ง่ายขึ้น"
          eyebrow="Game Topup Online"
          title="เติมเกมส์ออนไลน์"
        />

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

          <SectionTitle subtitle="เลือกเกมที่ต้องการเติม" title="เกมทั้งหมด" />
          <div className="grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            {games.map((game) => (
              <GameCard key={game.name} {...game} />
            ))}
          </div>
        </section>

        <SiteFooter />
      </div>
    </main>
  );
}
