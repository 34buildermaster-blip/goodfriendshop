import type { Metadata } from "next";
import localFont from "next/font/local";
import "./globals.css";

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL ?? "https://goodfriendshop.com";
const siteTitle = "Good Friend Shop";
const siteDescription =
  "เว็บเติมเกมและร้านแอพพรีเมี่ยม Good Friend Shop เติมเกมไว ปลอดภัย และติดตามออเดอร์ได้ง่าย";
const siteLogo = "/figma/logo-goodfriend.webp";
const siteIcon = "/icon.png";

const lineSeedSansTh = localFont({
  src: [
    {
      path: "../../node_modules/@fontpkg/line-seed-sans-th/LINESeedSansTH_Rg.otf",
      weight: "400",
      style: "normal",
    },
    {
      path: "../../node_modules/@fontpkg/line-seed-sans-th/LINESeedSansTH_Bd.otf",
      weight: "700",
      style: "normal",
    },
  ],
  variable: "--font-line-seed-sans-th",
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl),
  applicationName: siteTitle,
  title: {
    default: siteTitle,
    template: `%s | ${siteTitle}`,
  },
  description: siteDescription,
  alternates: {
    canonical: "/",
  },
  icons: {
    icon: [
      { url: "/favicon.ico", sizes: "any" },
      { url: siteIcon, type: "image/png", sizes: "512x512" },
    ],
    shortcut: [{ url: "/favicon.ico" }],
  },
  openGraph: {
    title: siteTitle,
    description: siteDescription,
    url: "/",
    siteName: siteTitle,
    images: [
      {
        url: siteLogo,
        width: 800,
        height: 800,
        alt: siteTitle,
      },
    ],
    locale: "th_TH",
    type: "website",
  },
  twitter: {
    card: "summary",
    title: siteTitle,
    description: siteDescription,
    images: [siteLogo],
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="th" className={`${lineSeedSansTh.variable} h-full antialiased`}>
      <body className="min-h-full flex flex-col">{children}</body>
    </html>
  );
}
