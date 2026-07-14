import {
  games as fallbackGames,
  news as fallbackNews,
  premiumProducts as fallbackPremiumProducts,
  type PremiumProduct,
} from "@/lib/site-data";

const apiBaseUrl =
  process.env.NEXT_PUBLIC_API_URL?.replace(/\/$/, "") ?? "http://127.0.0.1:8001/api";
const backendOrigin = (() => {
  try {
    return new URL(apiBaseUrl).origin;
  } catch {
    return "http://127.0.0.1:8001";
  }
})();

export type GamePackageItem = {
  id: number | string;
  name: string;
  sku?: string | null;
  description?: string | null;
  price: number;
  currency: string;
  required_fields?: string[];
};

export type GameItem = {
  id?: number | string;
  slug?: string;
  name: string;
  publisher?: string | null;
  description?: string | null;
  image: string;
  featured?: boolean;
  packages?: GamePackageItem[];
};

export type PremiumProductItem = PremiumProduct & {
  image?: string;
  provider?: string | null;
  slug?: string;
};

export type NewsItem = {
  slug: string;
  title: string;
  image: string;
  date: string;
  category: string;
  type?: string;
  featured?: boolean;
  excerpt: string;
  content?: string | null;
};

type ApiPayload<T> = {
  data?: T;
};

function normalizeMediaUrl(value: unknown, fallback: string) {
  if (typeof value !== "string" || value.length === 0) {
    return fallback;
  }

  if (value.startsWith("http://") || value.startsWith("https://")) {
    return value;
  }

  if (value.startsWith("/storage/")) {
    return `${backendOrigin}${value}`;
  }

  return value;
}

async function requestData<T>(path: string): Promise<T | null> {
  try {
    const response = await fetch(`${apiBaseUrl}${path}`, {
      cache: "no-store",
      headers: {
        Accept: "application/json",
      },
    });

    if (!response.ok) {
      return null;
    }

    const payload = (await response.json()) as ApiPayload<T> | T;
    return "data" in Object(payload) ? ((payload as ApiPayload<T>).data ?? null) : (payload as T);
  } catch {
    return null;
  }
}

export async function getGames(): Promise<GameItem[]> {
  const data = await requestData<GameItem[]>("/games");
  const source = data?.length ? data : fallbackGames;

  return source.map((game, index) => ({
    ...game,
    image: normalizeMediaUrl(game.image, fallbackGames[index]?.image ?? "/figma/game-mobile-legends.webp"),
    featured: game.featured ?? index === 0,
  }));
}

export async function getPremiumProducts(): Promise<PremiumProductItem[]> {
  const data = await requestData<PremiumProductItem[]>("/premium-apps");
  const source = data?.length ? data : fallbackPremiumProducts;

  return source.map((product, index) => ({
    ...product,
    image: normalizeMediaUrl(
      product.image,
      fallbackPremiumProducts[index]?.image ?? "/figma/premium-netflix.webp",
    ),
  }));
}

export async function getNews(): Promise<NewsItem[]> {
  const data = await requestData<NewsItem[]>("/content-posts");
  const source = data?.length ? data : fallbackNews;

  return source.map((item, index) => ({
    ...item,
    image: normalizeMediaUrl(item.image, fallbackNews[index]?.image ?? "/figma/news-main.webp"),
    featured: item.featured ?? index === 0,
  }));
}

export async function getNewsArticle(slug: string): Promise<NewsItem | null> {
  const fallbackArticle = fallbackNews.find((item) => item.slug === slug) ?? null;
  const data = await requestData<NewsItem>(`/content-posts/${slug}`);

  if (!data) {
    return fallbackArticle;
  }

  return {
    ...data,
    image: normalizeMediaUrl(data.image, fallbackArticle?.image ?? "/figma/news-main.webp"),
  };
}
