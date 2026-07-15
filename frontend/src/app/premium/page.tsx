"use client";

import Image from "next/image";
import { useEffect, useState } from "react";
import { ClipboardList, ShoppingBag, X } from "lucide-react";
import {
  PageIntro,
  SectionTitle,
  SiteFooter,
  SiteHeader,
} from "@/components/site-chrome";
import { PremiumOrderModal } from "@/components/premium-order-modal";
import { getPremiumProducts, type PremiumProductItem } from "@/lib/api";
import { premiumProducts as fallbackPremiumProducts } from "@/lib/site-data";
import { assetPath } from "@/lib/paths";

function PremiumProductCard({
  onDetails,
  onOrder,
  product,
}: {
  onDetails: () => void;
  onOrder: () => void;
  product: PremiumProductItem;
}) {
  return (
    <article className="rounded-[32px] border border-[#586c64]/70 bg-[#161d26]/80 p-4">
      <div className="relative aspect-square overflow-hidden rounded-[28px]">
        <Image
          alt={product.title}
          className="object-cover"
          fill
          sizes="(min-width: 1024px) 420px, 90vw"
          src={assetPath(product.image ?? "/figma/premium-netflix.webp")}
        />
      </div>
      <div className="px-3 pb-2 pt-6">
        <div className="mb-3 flex items-center justify-between gap-3">
          <h3 className="text-lg font-semibold text-white md:text-xl">
            {product.title}
          </h3>
          <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-400">
            ขายดี
          </span>
        </div>
        <p className="text-sm text-emerald-400">ราคาสินค้า</p>
        <p className="mt-1 text-2xl font-bold text-[#ffc012]">{product.price}</p>
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
  product: PremiumProductItem;
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
      aria-labelledby="premium-product-title"
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
            id="premium-product-title"
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
              <p className="text-3xl font-bold text-[#ffc012]">{product.price}</p>
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

export default function PremiumPage() {
  const [products, setProducts] = useState<PremiumProductItem[]>(
    fallbackPremiumProducts,
  );
  const [selectedProduct, setSelectedProduct] = useState<PremiumProductItem | null>(
    null,
  );
  const [orderingProduct, setOrderingProduct] = useState<PremiumProductItem | null>(
    null,
  );

  useEffect(() => {
    let active = true;

    getPremiumProducts().then((items) => {
      if (active) {
        setProducts(items);
      }
    });

    return () => {
      active = false;
    };
  }, []);

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/premium" />
      <div className="mint-page-surface">
        <PageIntro
          description="รวมแพ็กแอพพรีเมี่ยมยอดนิยม จัดเป็นหมวดให้เลือกง่าย พร้อมราคา ระยะเวลา และรายละเอียดก่อนสั่งซื้อ"
          eyebrow="Premium Apps"
          title="แอพพรีเมี่ยมทั้งหมด"
        />
        <section className="mx-auto max-w-[1398px] px-5 pb-16">
          <SectionTitle
            subtitle="แพ็กยอดนิยมและสินค้าแนะนำ"
            title="แอพขายดี"
          />
          <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            {products.map((product) => (
              <PremiumProductCard
                key={product.id}
                onDetails={() => setSelectedProduct(product)}
                onOrder={() => setOrderingProduct(product)}
                product={product}
              />
            ))}
          </div>
        </section>
        <SiteFooter />
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
