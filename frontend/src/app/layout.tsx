import type { Metadata } from "next";
import localFont from "next/font/local";
import "./globals.css";

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
  title: "Good Friend Shop",
  description: "เว็บเติมเกมและร้านแอพพรีเมี่ยม Good Friend Shop",
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
