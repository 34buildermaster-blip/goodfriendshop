"use client";

import {
  createContext,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { getSiteContent, type SiteSettings } from "@/lib/api";

const defaultSiteSettings: SiteSettings = {
  site_name: "Good Friend Shop",
  logo_path: "/figma/logo-goodfriend.webp",
  footer_tagline: "เติมเกมไวเหมือนเพื่อนรู้ใจ ราคาสบายกระเป๋าที่สุด!",
  footer_description:
    "GoodFriendShop คือเพื่อนแท้ของเกมเมอร์ พร้อมสนับสนุนให้คุณเล่นต่อได้ไม่มีสะดุด",
  contact_line: "xxxxxxx",
  contact_email: "xxxxxxx@gmail.com",
  contact_phone: "xxx-xxx-xxxx",
  facebook_label: "xxxxxx",
};

const SiteSettingsContext = createContext<SiteSettings>(defaultSiteSettings);

export function SiteSettingsProvider({ children }: { children: ReactNode }) {
  const [settings, setSettings] = useState<SiteSettings>(defaultSiteSettings);

  useEffect(() => {
    let active = true;

    getSiteContent()
      .then((content) => {
        if (active && content?.settings) {
          setSettings((current) => ({ ...current, ...content.settings }));
        }
      })
      .catch(() => null);

    return () => {
      active = false;
    };
  }, []);

  const value = useMemo(() => settings, [settings]);

  return (
    <SiteSettingsContext.Provider value={value}>
      {children}
    </SiteSettingsContext.Provider>
  );
}

export function useSiteSettings() {
  return useContext(SiteSettingsContext);
}

export function cleanSiteSettingValue(value?: string | null) {
  const cleaned = value?.trim() ?? "";

  if (!cleaned || /^x+$/i.test(cleaned)) {
    return "";
  }

  return cleaned;
}
