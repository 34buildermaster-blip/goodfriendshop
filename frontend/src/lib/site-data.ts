import type { LucideIcon } from "lucide-react";
import {
  BadgeCheck,
  Gamepad2,
  Gift,
  Headphones,
  MessageCircle,
  ShieldCheck,
  Sparkles,
  WalletCards,
} from "lucide-react";

export const navItems = [
  { label: "หน้าหลัก", href: "/" },
  { label: "แอพพรีเมี่ยมทั้งหมด", href: "/premium" },
  { label: "สินค้าอื่นๆ", href: "/products" },
  { label: "เติมเกมส์ออนไลน์", href: "/games" },
  { label: "กิจกรรม", href: "/news" },
  { label: "ติดต่อเรา", href: "/contact" },
];

export const games = [
  { name: "Mobile Legends", image: "/figma/game-mobile-legends.webp", featured: true },
  { name: "PUBG Mobile (Thai)", image: "/figma/game-pubg.webp" },
  { name: "RoV Mobile", image: "/figma/game-rov.webp" },
  { name: "Delta Force (Garena)", image: "/figma/game-delta-garena.webp" },
  { name: "Free Fire", image: "/figma/game-free-fire.webp" },
  { name: "Delta Force (Steam)", image: "/figma/game-delta-steam.webp" },
  { name: "League of Legends", image: "/figma/game-lol.webp" },
  { name: "Identity V", image: "/figma/game-identity-v.webp" },
  { name: "FC Mobile (FIFA Mobile)", image: "/figma/game-fc-mobile.webp" },
  { name: "MU Archangel", image: "/figma/game-mu.webp" },
];

export type PremiumProduct = {
  id: string;
  title: string;
  price: string;
  duration: string;
  warranty: string;
  platform: string;
  description: string;
  details: string[];
};

export const premiumProducts: PremiumProduct[] = [
  {
    id: "nf-1-day",
    title: "แอคนอกมีเคลม NF 1 วัน (มือถือ)",
    price: "฿20.00",
    duration: "ใช้งาน 1 วัน",
    warranty: "มีเคลมระหว่างระยะเวลาใช้งาน",
    platform: "รองรับมือถือ",
    description:
      "เหมาะสำหรับลูกค้าที่ต้องการทดลองใช้งาน Netflix แบบสั้น ๆ จ่ายตรง ไม่ต้องเติม coin และรับข้อมูลหลังสั่งซื้อ",
    details: [
      "บัญชีพร้อมใช้งานหลังชำระเงิน",
      "เหมาะกับการดูผ่านมือถือ",
      "หากเข้าใช้งานไม่ได้ในระยะเวลา มีบริการเคลมตามเงื่อนไขร้าน",
    ],
  },
  {
    id: "nf-3-day",
    title: "แอคนอกมีเคลม NF 3 วัน (มือถือ)",
    price: "฿59.00",
    duration: "ใช้งาน 3 วัน",
    warranty: "มีเคลมระหว่างระยะเวลาใช้งาน",
    platform: "รองรับมือถือ",
    description:
      "แพ็กเกจยอดนิยมสำหรับใช้งานต่อเนื่องหลายวัน ราคาคุ้มกว่าแบบรายวัน พร้อมข้อมูลเข้าใช้งานหลังสั่งซื้อ",
    details: [
      "บัญชีพร้อมใช้งานหลังชำระเงิน",
      "ระยะเวลาใช้งาน 3 วันเต็มตามรอบร้าน",
      "มีทีมช่วยตรวจสอบหากพบปัญหาระหว่างใช้งาน",
    ],
  },
  {
    id: "nf-7-day",
    title: "แอคนอกมีเคลม NF 7 วัน (มือถือ)",
    price: "฿99.00",
    duration: "ใช้งาน 7 วัน",
    warranty: "มีเคลมระหว่างระยะเวลาใช้งาน",
    platform: "รองรับมือถือ",
    description:
      "เหมาะสำหรับลูกค้าที่ต้องการใช้งานทั้งสัปดาห์ ราคาประหยัดกว่า ซื้อครั้งเดียวใช้งานได้ยาวขึ้น",
    details: [
      "บัญชีพร้อมใช้งานหลังชำระเงิน",
      "ใช้งานได้ 7 วันตามเงื่อนไขแพ็กเกจ",
      "เหมาะกับลูกค้าที่ต้องการความคุ้มค่าระยะยาว",
    ],
  },
  {
    id: "nf-30-day",
    title: "แอคนอกมีเคลม NF 30 วัน (มือถือ)",
    price: "฿239.00",
    duration: "ใช้งาน 30 วัน",
    warranty: "มีเคลมตลอดแพ็กเกจ",
    platform: "มือถือ / แท็บเล็ต",
    description:
      "แพ็กเกจรายเดือนสำหรับลูกค้าที่ต้องการใช้งานยาว ๆ จบในครั้งเดียว พร้อมดูแลตลอดรอบใช้งาน",
    details: [
      "รับข้อมูลบัญชีหลังชำระเงิน",
      "เหมาะสำหรับผู้ใช้งานประจำ",
      "ทีมงานช่วยตรวจสอบกรณีเข้าใช้งานไม่ได้ตามเงื่อนไข",
    ],
  },
  {
    id: "spotify-30-day",
    title: "Spotify Premium 30 วัน",
    price: "฿89.00",
    duration: "ใช้งาน 30 วัน",
    warranty: "รับประกันตลอดอายุแพ็กเกจ",
    platform: "iOS / Android / Web",
    description:
      "ฟังเพลงแบบพรีเมี่ยม ไม่มีโฆษณาคั่น เหมาะกับลูกค้าที่ต้องการแพ็กเกจทดลองราคาประหยัด",
    details: [
      "รองรับการใช้งานหลายอุปกรณ์",
      "จัดส่งข้อมูลหลังชำระเงิน",
      "มีทีมช่วยดูแลหากพบปัญหาระหว่างรอบใช้งาน",
    ],
  },
  {
    id: "youtube-30-day",
    title: "YouTube Premium 30 วัน",
    price: "฿129.00",
    duration: "ใช้งาน 30 วัน",
    warranty: "รับประกันตามเงื่อนไขร้าน",
    platform: "มือถือ / เว็บ / Smart TV",
    description:
      "ดู YouTube แบบไม่มีโฆษณา พร้อมฟังเพลงต่อเนื่อง เหมาะสำหรับใช้งานส่วนตัวรายเดือน",
    details: [
      "เปิดใช้งานตามรอบบริการของร้าน",
      "รองรับการดูผ่านมือถือและเว็บ",
      "แจ้งวิธีใช้งานหลังชำระเงินสำเร็จ",
    ],
  },
];

export const otherProducts = [
  {
    title: "บัตรเติมเงินเกม",
    description: "รวมโค้ดเติมเงินและคูปองเกมยอดนิยม จัดส่งไวหลังชำระเงิน",
    image: "/figma/game-free-fire.webp",
    icon: WalletCards,
  },
  {
    title: "ไอเทมพิเศษ",
    description: "สินค้าแนะนำประจำรอบ โปรโมชัน และแพ็กเสริมสำหรับสายเกม",
    image: "/figma/game-delta-garena.webp",
    icon: Gift,
  },
  {
    title: "บริการช่วยเหลือ",
    description: "ทีมงานช่วยตรวจสอบรายการ เติมผิดเกม หรือมีปัญหาหลังสั่งซื้อ",
    image: "/figma/game-mobile-legends.webp",
    icon: Headphones,
  },
];

export const news = [
  {
    slug: "bytedance-moonton",
    title: "ByteDance บุกตลาด MOBA ซื้อกิจการ Moonton เจ้าของเกม Mobile Legends",
    image: "/figma/news-main.webp",
    date: "March 23, 2021",
    category: "TopTen",
    featured: true,
    excerpt:
      "ByteDance ได้เข้าซื้อ Moonton สตูดิโอเกมมือถือรายใหญ่ที่สร้างความทะเยอทะยานในอุตสาหกรรมเกม",
  },
  {
    slug: "rov-talon-awc",
    title:
      "เรื่องเล่าเช้านี้รายการข่าวชื่อดังหยิบนำข่าว RoV ทีม Dtac Talon คว้าแชมป์โลก AWC 2021",
    image: "/figma/news-champions.webp",
    date: "July 21, 2021",
    category: "Event",
    excerpt: "ข่าวทีมอีสปอร์ตไทยสร้างชื่อบนเวทีระดับโลก และกลายเป็นกระแสในวงการเกม",
  },
  {
    slug: "delta-force-acl",
    title: "[เปิดรับสมัคร] การแข่งขัน Garena® Delta Force Road to ACL 2025",
    image: "/figma/news-delta.webp",
    date: "May 12, 2025",
    category: "Tournament",
    excerpt: "เปิดรับสมัครนักแข่ง Delta Force เพื่อค้นหาทีมเข้าสู่เส้นทางการแข่งขันใหญ่",
  },
];

export const contactCards: Array<{
  title: string;
  value: string;
  description: string;
  icon: LucideIcon;
}> = [
  {
    title: "แชทกับทีมงาน",
    value: "LINE: xxxxxxx",
    description: "เหมาะสำหรับสอบถามรายการสั่งซื้อและแจ้งปัญหาหลังใช้งาน",
    icon: MessageCircle,
  },
  {
    title: "เช็กสถานะรายการ",
    value: "ตอบกลับในเวลาทำการ",
    description: "ส่งเลขออเดอร์หรือชื่อเกมให้ทีมงานตรวจสอบได้ทันที",
    icon: BadgeCheck,
  },
  {
    title: "บริการปลอดภัย",
    value: "ดูแลทุกขั้นตอน",
    description: "เติมเกมและแพ็กพรีเมี่ยมตามเงื่อนไขร้าน มีทีมช่วยเหลือ",
    icon: ShieldCheck,
  },
];

export const contactSteps = [
  { title: "เลือกสินค้า", icon: Sparkles },
  { title: "แจ้งข้อมูลให้ครบ", icon: Gamepad2 },
  { title: "ชำระเงินและรอดำเนินการ", icon: WalletCards },
  { title: "รับสินค้า / แจ้งเคลม", icon: BadgeCheck },
];
