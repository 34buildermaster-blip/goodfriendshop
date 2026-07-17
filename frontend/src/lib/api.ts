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

export const backendBaseUrl = backendOrigin;

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

export type CustomerUser = {
  id: number | string;
  name: string;
  email: string;
  phone?: string | null;
  line_id?: string | null;
  avatar_url?: string | null;
};

export type AuthResponse = {
  token: string;
  user: CustomerUser;
};

export type OrderItem = {
  id?: number | string;
  order_number: string;
  customer_name?: string | null;
  customer_email?: string | null;
  customer_phone?: string | null;
  player_identifier: string;
  server_identifier?: string | null;
  game_name: string;
  package_name: string;
  price: number;
  currency: string;
  payment_method?: string | null;
  payment_status?: string | null;
  payment_status_label?: string | null;
  payment_reference?: string | null;
  payment_note?: string | null;
  paid_at?: string | null;
  status: string;
  status_label: string;
  customer_note?: string | null;
  support_note?: string | null;
  next_action?: string | null;
  status_steps?: Array<{
    key: string;
    label: string;
    state: "done" | "current" | "upcoming";
  }>;
  created_at?: string | null;
  updated_at?: string | null;
};

export type CreateOrderPayload = {
  game_package_id?: number | string;
  premium_app_id?: number | string;
  customer_name?: string;
  customer_email?: string;
  customer_phone?: string;
  player_identifier: string;
  server_identifier?: string;
  customer_note?: string;
  extra_fields?: Record<string, unknown>;
};

export type PremiumProductItem = PremiumProduct & {
  image?: string;
  provider?: string | null;
  slug?: string;
  delivery_type?: string;
  delivery_label?: string;
  stock_status?: string;
  stock_label?: string;
  customer_required_fields?: string[];
  customer_field_labels?: Record<string, string>;
  warranty_days?: number | null;
  terms?: string | null;
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
  meta_title?: string | null;
  meta_description?: string | null;
  og_image?: string | null;
};

export type PaymentMethodItem = {
  id: string;
  name: string;
  status: string;
  description?: string | null;
};

export type SiteSettings = {
  site_name?: string;
  logo_path?: string;
  footer_tagline?: string;
  footer_description?: string;
  contact_line?: string;
  contact_email?: string;
  contact_phone?: string;
  facebook_label?: string;
};

export type HeroSlideItem = {
  id?: number | string;
  eyebrow: string;
  title: string;
  highlight: string;
  quote: string;
  image: string;
  href: string;
  cta: string;
};

export type AnnouncementItem = {
  id?: number | string;
  message: string;
};

export type SiteContent = {
  settings: SiteSettings;
  hero_slides: HeroSlideItem[];
  announcements: AnnouncementItem[];
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

function normalizeCustomerUser(user: CustomerUser): CustomerUser {
  return {
    ...user,
    avatar_url: normalizeMediaUrl(user.avatar_url, ""),
  };
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

async function sendData<T>(
  path: string,
  body?: unknown,
  token?: string | null,
  method = "POST",
): Promise<T> {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    method,
    cache: "no-store",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    body: body ? JSON.stringify(body) : undefined,
  });

  const payload = (await response.json().catch(() => ({}))) as
    | ApiPayload<T>
    | { message?: string; errors?: Record<string, string[]> };

  if (!response.ok) {
    const message =
      "message" in payload && typeof payload.message === "string"
        ? payload.message
        : "ไม่สามารถทำรายการได้ในตอนนี้";
    throw new Error(message);
  }

  return "data" in Object(payload) ? ((payload as ApiPayload<T>).data as T) : (payload as T);
}

async function sendFormData<T>(path: string, body: FormData, token?: string | null): Promise<T> {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    method: "POST",
    cache: "no-store",
    headers: {
      Accept: "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    body,
  });

  const payload = (await response.json().catch(() => ({}))) as
    | ApiPayload<T>
    | { message?: string; errors?: Record<string, string[]> };

  if (!response.ok) {
    const message =
      "message" in payload && typeof payload.message === "string"
        ? payload.message
        : "ไม่สามารถอัปโหลดไฟล์ได้ในตอนนี้";
    throw new Error(message);
  }

  return "data" in Object(payload) ? ((payload as ApiPayload<T>).data as T) : (payload as T);
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

export async function getGame(slug: string): Promise<GameItem | null> {
  const fallbackGame = fallbackGames.find((game) => (game as GameItem).slug === slug) ?? null;
  const data = await requestData<GameItem>(`/games/${slug}`);

  if (!data) {
    return fallbackGame;
  }

  return {
    ...data,
    image: normalizeMediaUrl(data.image, fallbackGame?.image ?? "/figma/game-mobile-legends.webp"),
  };
}

export async function createOrder(payload: CreateOrderPayload, token?: string | null) {
  return sendData<OrderItem>("/orders", payload, token);
}

export async function loginCustomer(email: string, password: string) {
  const response = await sendData<AuthResponse>("/auth/login", { email, password });

  return {
    ...response,
    user: normalizeCustomerUser(response.user),
  };
}

export async function registerCustomer(payload: {
  name: string;
  email: string;
  phone?: string;
  line_id?: string;
  password: string;
}) {
  const response = await sendData<AuthResponse>("/auth/register", payload);

  return {
    ...response,
    user: normalizeCustomerUser(response.user),
  };
}

export async function getCurrentCustomer(token: string) {
  return normalizeCustomerUser(await sendData<CustomerUser>("/auth/me", undefined, token, "GET"));
}

export async function updateCurrentCustomer(
  token: string,
  payload: Pick<CustomerUser, "name" | "email"> & {
    phone?: string | null;
    line_id?: string | null;
  },
) {
  return normalizeCustomerUser(await sendData<CustomerUser>("/auth/me", payload, token, "PATCH"));
}

export async function updateCurrentCustomerPassword(
  token: string,
  payload: {
    current_password: string;
    password: string;
    password_confirmation: string;
  },
) {
  return sendData<{ ok: boolean }>("/auth/me/password", payload, token, "PATCH");
}

export async function uploadCurrentCustomerAvatar(token: string, file: File) {
  const body = new FormData();
  body.append("avatar", file);

  return normalizeCustomerUser(await sendFormData<CustomerUser>("/auth/me/avatar", body, token));
}

export async function logoutCustomer(token: string) {
  return sendData<{ ok: boolean }>("/auth/logout", undefined, token);
}

export async function getMyOrders(token: string) {
  return sendData<OrderItem[]>("/my/orders", undefined, token, "GET");
}

export async function getMyOrder(token: string, orderNumber: string) {
  return sendData<OrderItem>(`/my/orders/${orderNumber}`, undefined, token, "GET");
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
    og_image: normalizeMediaUrl(data.og_image, data.image),
  };
}

export async function getPaymentMethods(): Promise<PaymentMethodItem[]> {
  return (await requestData<PaymentMethodItem[]>("/payment-methods")) ?? [];
}

export async function getSiteContent(): Promise<SiteContent | null> {
  const data = await requestData<SiteContent>("/site-content");

  if (!data) {
    return null;
  }

  return {
    ...data,
    settings: {
      ...data.settings,
      logo_path: normalizeMediaUrl(data.settings.logo_path, ""),
    },
    hero_slides: data.hero_slides.map((slide, index) => ({
      ...slide,
      image: normalizeMediaUrl(slide.image, index === 0 ? "/figma/hero.webp" : "/figma/news-main.webp"),
    })),
  };
}
