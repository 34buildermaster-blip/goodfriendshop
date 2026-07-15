"use client";

import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useMemo, useState } from "react";
import {
  AlertCircle,
  BadgeCheck,
  Bell,
  Camera,
  ChevronRight,
  CheckCircle2,
  Clock,
  Coins,
  CreditCard,
  Gamepad2,
  Gift,
  History,
  Home,
  IdCard,
  LogOut,
  Mail,
  MessageCircle,
  Phone,
  Save,
  ShieldCheck,
  UserRound,
  WalletCards,
  X,
} from "lucide-react";
import { SiteFooter, SiteHeader } from "@/components/site-chrome";
import {
  getCurrentCustomer,
  getMyOrder,
  getMyOrders,
  logoutCustomer,
  uploadCurrentCustomerAvatar,
  updateCurrentCustomer,
  updateCurrentCustomerPassword,
  type CustomerUser,
  type OrderItem,
} from "@/lib/api";

type ProfileTab = "overview" | "contact" | "orders" | "payment";

const profileTabIds: ProfileTab[] = ["overview", "contact", "orders", "payment"];

const tabs: Array<{ id: ProfileTab; label: string; icon: typeof UserRound }> = [
  { id: "overview", label: "ข้อมูลผู้ใช้", icon: UserRound },
  { id: "contact", label: "ช่องทางติดต่อ", icon: MessageCircle },
  { id: "orders", label: "ประวัติออเดอร์", icon: History },
  { id: "payment", label: "ช่องทางชำระเงิน", icon: CreditCard },
];

const memberMenu = [
  { label: "หน้าหลัก", href: "/", icon: Home },
  { label: "เติมเกม", href: "/games", icon: Gamepad2 },
  { label: "แอพพรีเมียม", href: "/premium", icon: Gift },
];

function initials(name: string) {
  return name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join("")
    .toUpperCase();
}

function statusTone(status: string) {
  if (status === "completed") {
    return "border-emerald-400/30 bg-emerald-500/12 text-emerald-200";
  }

  if (status === "cancelled") {
    return "border-red-400/25 bg-red-500/12 text-red-200";
  }

  if (status === "processing" || status === "paid") {
    return "border-sky-400/25 bg-sky-500/12 text-sky-200";
  }

  return "border-amber-300/25 bg-amber-400/12 text-amber-100";
}

export default function ProfilePage() {
  const router = useRouter();
  const [user, setUser] = useState<CustomerUser | null>(null);
  const [orders, setOrders] = useState<OrderItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<ProfileTab>("overview");
  const [editingProfile, setEditingProfile] = useState(false);
  const [savingProfile, setSavingProfile] = useState(false);
  const [savingPassword, setSavingPassword] = useState(false);
  const [avatarUploading, setAvatarUploading] = useState(false);
  const [profileMessage, setProfileMessage] = useState("");
  const [profileError, setProfileError] = useState("");
  const [selectedOrder, setSelectedOrder] = useState<OrderItem | null>(null);
  const [orderLoading, setOrderLoading] = useState(false);
  const [profileForm, setProfileForm] = useState({
    name: "",
    email: "",
    phone: "",
    line_id: "",
  });
  const [passwordForm, setPasswordForm] = useState({
    current_password: "",
    password: "",
    password_confirmation: "",
  });

  useEffect(() => {
    const token = window.localStorage.getItem("gfs_token");
    const requestedTab = new URLSearchParams(window.location.search).get("tab");

    if (profileTabIds.includes(requestedTab as ProfileTab)) {
      queueMicrotask(() => setActiveTab(requestedTab as ProfileTab));
    }

    if (!token) {
      queueMicrotask(() => setLoading(false));
      return;
    }

    Promise.all([getCurrentCustomer(token), getMyOrders(token)])
      .then(([currentUser, currentOrders]) => {
        setUser(currentUser);
        setOrders(currentOrders);
        setProfileForm({
          name: currentUser.name,
          email: currentUser.email,
          phone: currentUser.phone ?? "",
          line_id: currentUser.line_id ?? "",
        });
        setSelectedOrder(currentOrders[0] ?? null);
        window.localStorage.setItem("gfs_user", JSON.stringify(currentUser));
      })
      .catch(() => {
        window.localStorage.removeItem("gfs_token");
        window.localStorage.removeItem("gfs_user");
      })
      .finally(() => setLoading(false));
  }, []);

  const orderSummary = useMemo(
    () => ({
      total: orders.length,
      pending: orders.filter((order) => order.status === "pending").length,
      completed: orders.filter((order) => order.status === "completed").length,
    }),
    [orders],
  );

  const memberNotifications = useMemo(() => {
    if (!user) {
      return [];
    }

    const notices: Array<{ tone: "warning" | "info" | "success"; title: string; body: string }> = [];
    const pendingOrders = orders.filter((order) => order.status === "pending");
    const activeOrders = orders.filter((order) => ["paid", "processing"].includes(order.status));

    if (!user.phone || !user.line_id) {
      notices.push({
        tone: "warning",
        title: "ข้อมูลติดต่อยังไม่ครบ",
        body: "แนะนำให้เพิ่มเบอร์โทรและ LINE ID เพื่อให้ทีมงานติดต่อกลับได้เร็วขึ้น",
      });
    }

    if (pendingOrders.length > 0) {
      notices.push({
        tone: "info",
        title: `มี ${pendingOrders.length} ออเดอร์รอตรวจสอบ`,
        body: "ทีมงานจะอัปเดตสถานะหลังตรวจข้อมูลและการชำระเงิน",
      });
    }

    if (activeOrders.length > 0) {
      notices.push({
        tone: "success",
        title: `มี ${activeOrders.length} ออเดอร์กำลังดำเนินการ`,
        body: "สามารถดูรายละเอียดและขั้นตอนล่าสุดได้ในแท็บประวัติออเดอร์",
      });
    }

    return notices;
  }, [orders, user]);

  async function handleLogout() {
    const token = window.localStorage.getItem("gfs_token");
    if (token) {
      await logoutCustomer(token).catch(() => null);
    }
    window.localStorage.removeItem("gfs_token");
    window.localStorage.removeItem("gfs_user");
    router.push("/login");
  }

  function resetProfileForm() {
    if (!user) {
      return;
    }

    setProfileForm({
      name: user.name,
      email: user.email,
      phone: user.phone ?? "",
      line_id: user.line_id ?? "",
    });
    setProfileError("");
    setProfileMessage("");
    setEditingProfile(false);
  }

  function persistCurrentUser(updatedUser: CustomerUser) {
    setUser(updatedUser);
    window.localStorage.setItem("gfs_user", JSON.stringify(updatedUser));
    window.dispatchEvent(new Event("gfs:user-updated"));
  }

  async function handleSaveProfile() {
    const token = window.localStorage.getItem("gfs_token");

    if (!token) {
      router.push("/login");
      return;
    }

    setSavingProfile(true);
    setProfileError("");
    setProfileMessage("");

    try {
      const updatedUser = await updateCurrentCustomer(token, {
        name: profileForm.name,
        email: profileForm.email,
        phone: profileForm.phone || null,
        line_id: profileForm.line_id || null,
      });

      persistCurrentUser(updatedUser);
      setProfileForm({
        name: updatedUser.name,
        email: updatedUser.email,
        phone: updatedUser.phone ?? "",
        line_id: updatedUser.line_id ?? "",
      });
      setEditingProfile(false);
      setProfileMessage("บันทึกข้อมูลสมาชิกเรียบร้อยแล้ว");
    } catch (caught) {
      setProfileError(caught instanceof Error ? caught.message : "บันทึกข้อมูลไม่สำเร็จ");
    } finally {
      setSavingProfile(false);
    }
  }

  async function handleAvatarUpload(file: File | null) {
    const token = window.localStorage.getItem("gfs_token");

    if (!file) {
      return;
    }

    if (!token) {
      router.push("/login");
      return;
    }

    const allowedTypes = ["image/webp", "image/png", "image/jpeg"];

    if (!allowedTypes.includes(file.type)) {
      setProfileError("รองรับเฉพาะไฟล์ PNG, JPG หรือ WebP");
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      setProfileError("ไฟล์รูปโปรไฟล์ต้องไม่เกิน 2MB");
      return;
    }

    setAvatarUploading(true);
    setProfileError("");
    setProfileMessage("");

    try {
      const updatedUser = await uploadCurrentCustomerAvatar(token, file);
      persistCurrentUser(updatedUser);
      setProfileMessage("อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว");
    } catch (caught) {
      setProfileError(caught instanceof Error ? caught.message : "อัปโหลดรูปโปรไฟล์ไม่สำเร็จ");
    } finally {
      setAvatarUploading(false);
    }
  }

  async function handleChangePassword() {
    const token = window.localStorage.getItem("gfs_token");

    if (!token) {
      router.push("/login");
      return;
    }

    setSavingPassword(true);
    setProfileError("");
    setProfileMessage("");

    try {
      await updateCurrentCustomerPassword(token, passwordForm);
      setPasswordForm({
        current_password: "",
        password: "",
        password_confirmation: "",
      });
      setProfileMessage("เปลี่ยนรหัสผ่านเรียบร้อยแล้ว");
    } catch (caught) {
      setProfileError(caught instanceof Error ? caught.message : "เปลี่ยนรหัสผ่านไม่สำเร็จ");
    } finally {
      setSavingPassword(false);
    }
  }

  async function handleSelectOrder(order: OrderItem) {
    const token = window.localStorage.getItem("gfs_token");

    setSelectedOrder(order);

    if (!token) {
      return;
    }

    setOrderLoading(true);
    try {
      setSelectedOrder(await getMyOrder(token, order.order_number));
    } catch {
      setSelectedOrder(order);
    } finally {
      setOrderLoading(false);
    }
  }

  function handleProfileTabChange(tab: ProfileTab) {
    setActiveTab(tab);
    window.history.replaceState(null, "", tab === "overview" ? "/profile" : `/profile?tab=${tab}`);
  }

  return (
    <main className="min-h-screen overflow-hidden bg-[#0e0d17] text-white">
      <SiteHeader activeHref="/profile" />
      <div className="mint-page-surface min-h-screen pt-28">
        <section className="mx-auto grid max-w-[1480px] gap-6 px-5 pb-16 lg:grid-cols-[292px_minmax(0,1fr)]">
          <aside className="rounded-[28px] border border-white/10 bg-[#121821]/92 p-4 shadow-2xl shadow-black/20 lg:sticky lg:top-28 lg:h-[calc(100vh-8rem)]">
            <div className="flex items-center gap-3 rounded-3xl bg-white/[0.04] p-4">
              <ProfileAvatar className="h-14 w-14 rounded-2xl text-lg" user={user} />
              <div className="min-w-0">
                <p className="truncate text-sm font-semibold text-white">
                  {user?.name ?? "Good Friend Member"}
                </p>
                <p className="mt-1 truncate text-xs text-emerald-300">
                  {user?.email ?? "เข้าสู่ระบบเพื่อดูข้อมูลสมาชิก"}
                </p>
              </div>
            </div>

            <div className="mt-4 grid grid-cols-2 gap-3">
              <div className="rounded-2xl border border-yellow-300/20 bg-yellow-400/10 p-3">
                <div className="flex items-center gap-2 text-yellow-200">
                  <Coins size={17} />
                  <span className="text-xs font-semibold">Coin</span>
                </div>
                <p className="mt-2 text-2xl font-bold text-white">0</p>
              </div>
              <div className="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-3">
                <div className="flex items-center gap-2 text-emerald-200">
                  <Gift size={17} />
                  <span className="text-xs font-semibold">Coupon</span>
                </div>
                <p className="mt-2 text-2xl font-bold text-white">0</p>
              </div>
            </div>

            <nav className="mt-5 grid gap-2">
              {tabs.map(({ id, icon: Icon, label }) => (
                <button
                  className={`flex h-12 items-center gap-3 rounded-2xl px-4 text-left text-sm font-semibold transition ${
                    activeTab === id
                      ? "bg-emerald-400 text-[#06140f] shadow-lg shadow-emerald-500/20"
                      : "text-white/70 hover:bg-white/[0.06] hover:text-white"
                  }`}
                  key={id}
                  onClick={() => handleProfileTabChange(id)}
                  type="button"
                >
                  <Icon size={18} />
                  {label}
                </button>
              ))}
            </nav>

            <nav className="mt-5 grid gap-2 border-t border-white/10 pt-4">
              {memberMenu.map(({ href, icon: Icon, label }) => (
                <Link
                  className="flex h-12 items-center gap-3 rounded-2xl px-4 text-sm font-semibold text-white/70 transition hover:bg-white/[0.06] hover:text-white"
                  href={href}
                  key={label}
                >
                  <Icon size={18} />
                  {label}
                </Link>
              ))}
            </nav>

            <div className="mt-5 border-t border-white/10 pt-4">
              {user ? (
                <button
                  className="flex h-12 w-full items-center gap-3 rounded-2xl px-4 text-left text-sm font-semibold text-white/70 transition hover:bg-red-500/10 hover:text-red-100"
                  onClick={handleLogout}
                  type="button"
                >
                  <LogOut size={18} />
                  ออกจากระบบ
                </button>
              ) : (
                <Link
                  className="flex h-12 items-center justify-center rounded-2xl bg-emerald-500 px-4 text-sm font-semibold text-white"
                  href="/login"
                >
                  เข้าสู่ระบบ
                </Link>
              )}
            </div>
          </aside>

          <div className="min-w-0">
            {loading ? (
              <div className="rounded-[30px] border border-white/10 bg-[#111821]/90 p-8 text-white/70">
                กำลังโหลดข้อมูลสมาชิก...
              </div>
            ) : null}

            {!loading && !user ? (
              <div className="rounded-[30px] border border-[#586c64]/70 bg-[#111821]/90 p-7">
                <p className="text-xs font-medium uppercase tracking-[0.18em] text-emerald-300">
                  Member center
                </p>
                <h1 className="mt-3 text-4xl font-bold">เข้าสู่ระบบสมาชิก</h1>
                <p className="mt-3 max-w-2xl leading-7 text-white/65">
                  เข้าสู่ระบบเพื่อดูโปรไฟล์ ประวัติออเดอร์ และใช้ข้อมูลเดิมตอนสั่งซื้อ
                </p>
                <div className="mt-6 flex flex-wrap gap-3">
                  <Link className="rounded-full bg-emerald-500 px-6 py-3 font-semibold" href="/login">
                    เข้าสู่ระบบ
                  </Link>
                  <Link className="rounded-full border border-white/10 px-6 py-3 font-semibold text-white/85" href="/register">
                    สมัครสมาชิก
                  </Link>
                </div>
              </div>
            ) : null}

            {user ? (
              <>
                {memberNotifications.length > 0 ? (
                  <section className="mb-6 grid gap-3">
                    {memberNotifications.map((notice) => (
                      <div
                        className={`rounded-3xl border p-4 ${
                          notice.tone === "warning"
                            ? "border-amber-300/20 bg-amber-400/10 text-amber-100"
                            : notice.tone === "success"
                              ? "border-emerald-300/20 bg-emerald-400/10 text-emerald-100"
                              : "border-sky-300/20 bg-sky-400/10 text-sky-100"
                        }`}
                        key={notice.title}
                      >
                        <div className="flex gap-3">
                          {notice.tone === "warning" ? <AlertCircle className="mt-1 shrink-0" size={20} /> : <Bell className="mt-1 shrink-0" size={20} />}
                          <div>
                            <p className="font-semibold">{notice.title}</p>
                            <p className="mt-1 text-sm leading-6 text-white/68">{notice.body}</p>
                          </div>
                        </div>
                      </div>
                    ))}
                  </section>
                ) : null}

                <section className="overflow-hidden rounded-[32px] border border-emerald-300/15 bg-[#111821]/92 text-white shadow-2xl shadow-black/20">
                  <div className="grid gap-6 p-5 lg:grid-cols-[320px_1fr] lg:p-7">
                    <div className="flex items-center gap-5">
                      <div className="relative shrink-0">
                        <ProfileAvatar className="h-28 w-28 text-3xl shadow-lg" user={user} />
                        <label className="absolute bottom-0 right-0 grid h-10 w-10 cursor-pointer place-items-center rounded-full border border-white/20 bg-emerald-500 text-white shadow-lg transition hover:bg-emerald-400">
                          <Camera size={18} />
                          <input
                            accept="image/png,image/jpeg,image/webp"
                            className="sr-only"
                            disabled={avatarUploading}
                            onChange={(event) => {
                              void handleAvatarUpload(event.target.files?.[0] ?? null);
                              event.target.value = "";
                            }}
                            type="file"
                          />
                        </label>
                      </div>
                      <div className="min-w-0">
                        <h1 className="truncate text-3xl font-bold text-white">{user.name}</h1>
                        <p className="mt-2 text-sm font-semibold text-emerald-300">UID</p>
                        <p className="mt-1 break-all text-sm text-white/58">GF-{String(user.id).padStart(6, "0")}</p>
                        <p className="mt-2 text-xs text-white/45">
                          {avatarUploading ? "กำลังอัปโหลดรูป..." : "รูปโปรไฟล์แนะนำ 512x512px / PNG, JPG, WebP ไม่เกิน 2MB"}
                        </p>
                      </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-3">
                      <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                        <div className="flex items-center gap-2 text-emerald-600">
                          <BadgeCheck size={18} />
                          <span className="text-xs font-bold uppercase tracking-[0.1em]">Status</span>
                        </div>
                        <p className="mt-2 text-lg font-bold text-white">เปิดใช้งาน</p>
                      </div>
                      <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                        <div className="flex items-center gap-2 text-sky-600">
                          <Mail size={18} />
                          <span className="text-xs font-bold uppercase tracking-[0.1em]">Email</span>
                        </div>
                        <p className="mt-2 truncate text-lg font-bold text-white">{user.email}</p>
                      </div>
                      <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                        <div className="flex items-center gap-2 text-violet-600">
                          <ShieldCheck size={18} />
                          <span className="text-xs font-bold uppercase tracking-[0.1em]">Role</span>
                        </div>
                        <p className="mt-2 text-lg font-bold text-white">Customer</p>
                      </div>
                    </div>
                  </div>
                </section>

                <section className="mt-6 rounded-[32px] border border-emerald-300/15 bg-[#111821]/92 p-5 text-white shadow-2xl shadow-black/15 lg:p-7">
                  {activeTab === "overview" ? (
                    <div>
                      <div className="flex items-center justify-between gap-4">
                        <div>
                          <h2 className="text-2xl font-bold">ข้อมูลส่วนตัว</h2>
                          <p className="mt-1 text-sm text-white/58">ข้อมูลหลักของบัญชีสมาชิก</p>
                        </div>
                        {editingProfile ? (
                          <button
                            className="flex h-10 items-center gap-2 rounded-full border border-white/10 bg-white/[0.05] px-4 text-sm font-semibold text-white/72 hover:text-white"
                            onClick={resetProfileForm}
                            type="button"
                          >
                            <X size={16} />
                            ยกเลิก
                          </button>
                        ) : (
                          <button
                            className="rounded-full border border-emerald-300/25 bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-200"
                            onClick={() => {
                              setProfileError("");
                              setProfileMessage("");
                              setEditingProfile(true);
                            }}
                            type="button"
                          >
                            แก้ไขข้อมูล
                          </button>
                        )}
                      </div>

                      {profileMessage ? (
                        <p className="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-3 text-sm text-emerald-100">
                          {profileMessage}
                        </p>
                      ) : null}

                      {profileError ? (
                        <p className="mt-4 rounded-2xl border border-red-300/20 bg-red-400/10 p-3 text-sm text-red-100">
                          {profileError}
                        </p>
                      ) : null}

                      {editingProfile ? (
                        <div className="mt-6 grid gap-4 md:grid-cols-2">
                          <ProfileInput
                            label="ชื่อ"
                            onChange={(value) => setProfileForm((current) => ({ ...current, name: value }))}
                            required
                            value={profileForm.name}
                          />
                          <ProfileInput
                            label="อีเมล"
                            onChange={(value) => setProfileForm((current) => ({ ...current, email: value }))}
                            required
                            type="email"
                            value={profileForm.email}
                          />
                          <ProfileInput
                            label="เบอร์โทร"
                            onChange={(value) => setProfileForm((current) => ({ ...current, phone: value }))}
                            value={profileForm.phone}
                          />
                          <ProfileInput
                            label="LINE ID"
                            onChange={(value) => setProfileForm((current) => ({ ...current, line_id: value }))}
                            value={profileForm.line_id}
                          />
                          <div className="md:col-span-2">
                            <button
                              className="flex h-12 items-center justify-center gap-2 rounded-full bg-emerald-500 px-7 text-sm font-semibold text-white transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60"
                              disabled={savingProfile}
                              onClick={handleSaveProfile}
                              type="button"
                            >
                              <Save size={17} />
                              {savingProfile ? "กำลังบันทึก..." : "บันทึกข้อมูล"}
                            </button>
                          </div>
                        </div>
                      ) : (
                        <div className="mt-6 grid gap-4 md:grid-cols-2">
                          <InfoField label="ชื่อ" value={user.name} />
                          <InfoField label="อีเมล" value={user.email} />
                          <InfoField label="เบอร์โทร" value={user.phone ?? "-"} />
                          <InfoField label="LINE ID" value={user.line_id ?? "-"} />
                        </div>
                      )}

                      <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <SummaryCard icon={History} label="ออเดอร์ทั้งหมด" value={String(orderSummary.total)} />
                        <SummaryCard icon={WalletCards} label="รอตรวจสอบ" value={String(orderSummary.pending)} />
                        <SummaryCard icon={BadgeCheck} label="สำเร็จแล้ว" value={String(orderSummary.completed)} />
                      </div>

                      <section className="mt-6 rounded-3xl border border-white/10 bg-white/[0.04] p-5">
                        <div className="flex items-start gap-3">
                          <div className="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-300">
                            <ShieldCheck size={20} />
                          </div>
                          <div>
                            <h3 className="text-lg font-bold text-white">เปลี่ยนรหัสผ่าน</h3>
                            <p className="mt-1 text-sm leading-6 text-white/58">
                              ใช้สำหรับเพิ่มความปลอดภัยของบัญชีสมาชิก
                            </p>
                          </div>
                        </div>
                        <div className="mt-5 grid gap-4 md:grid-cols-3">
                          <ProfileInput
                            label="รหัสผ่านปัจจุบัน"
                            onChange={(value) => setPasswordForm((current) => ({ ...current, current_password: value }))}
                            type="password"
                            value={passwordForm.current_password}
                          />
                          <ProfileInput
                            label="รหัสผ่านใหม่"
                            onChange={(value) => setPasswordForm((current) => ({ ...current, password: value }))}
                            type="password"
                            value={passwordForm.password}
                          />
                          <ProfileInput
                            label="ยืนยันรหัสผ่านใหม่"
                            onChange={(value) => setPasswordForm((current) => ({ ...current, password_confirmation: value }))}
                            type="password"
                            value={passwordForm.password_confirmation}
                          />
                        </div>
                        <button
                          className="mt-5 flex h-11 items-center justify-center rounded-full bg-emerald-500 px-6 text-sm font-semibold text-white transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-60"
                          disabled={
                            savingPassword ||
                            !passwordForm.current_password ||
                            !passwordForm.password ||
                            !passwordForm.password_confirmation
                          }
                          onClick={handleChangePassword}
                          type="button"
                        >
                          {savingPassword ? "กำลังเปลี่ยน..." : "บันทึกรหัสผ่านใหม่"}
                        </button>
                      </section>
                    </div>
                  ) : null}

                  {activeTab === "contact" ? (
                    <div>
                      <div className="flex items-center justify-between gap-4">
                        <div>
                          <h2 className="text-2xl font-bold">ช่องทางติดต่อ</h2>
                          <p className="mt-1 text-sm text-white/58">
                            ใช้สำหรับติดต่อกลับและแจ้งสถานะรายการในอนาคต
                          </p>
                        </div>
                        <button
                          className="rounded-full border border-emerald-300/25 bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-200"
                          onClick={() => {
                            setProfileError("");
                            setProfileMessage("");
                            setEditingProfile(true);
                            setActiveTab("overview");
                            window.history.replaceState(null, "", "/profile");
                          }}
                          type="button"
                        >
                          แก้ไขช่องทางติดต่อ
                        </button>
                      </div>
                      <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <ContactField icon={Phone} label="เบอร์โทรศัพท์" value={user.phone ?? "-"} />
                        <ContactField icon={Mail} label="อีเมลรับข้อมูล" value={user.email} />
                        <ContactField icon={MessageCircle} label="บัญชี LINE" value={user.line_id ?? "ยังไม่ได้ผูก"} />
                      </div>
                      <div className="mt-5 rounded-2xl border border-amber-300/20 bg-amber-400/10 p-4 text-sm leading-6 text-amber-100">
                        หากต้องชำระผ่านระบบอัตโนมัติหรือ Wepay ภายหลัง ควรเติมเบอร์โทรและ LINE ให้ครบก่อนเปิดใช้งานจริง
                      </div>
                    </div>
                  ) : null}

                  {activeTab === "orders" ? (
                    <div>
                      <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                          <h2 className="text-2xl font-bold">ประวัติออเดอร์</h2>
                          <p className="mt-1 text-sm text-white/58">รายการสั่งซื้อทั้งหมดของบัญชีนี้</p>
                        </div>
                        <Link
                          className="flex h-11 items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 text-sm font-semibold text-white"
                          href="/games"
                        >
                          เติมเกมเพิ่ม
                          <ChevronRight size={17} />
                        </Link>
                      </div>

                      {orders.length === 0 ? (
                        <div className="mt-6 rounded-3xl border border-dashed border-white/15 bg-white/[0.04] p-8 text-center">
                          <p className="text-lg font-bold text-white">ยังไม่มีออเดอร์</p>
                          <p className="mt-2 text-sm text-white/58">เมื่อสั่งซื้อแล้ว รายการจะมาแสดงตรงนี้</p>
                        </div>
                      ) : (
                        <div className="mt-6 overflow-hidden rounded-3xl border border-white/10">
                          {orders.map((order) => (
                            <article
                              className="grid gap-4 border-b border-white/10 bg-white/[0.03] p-4 last:border-b-0 md:grid-cols-[1fr_auto_auto_auto]"
                              key={order.order_number}
                            >
                              <div>
                                <p className="font-bold text-white">{order.order_number}</p>
                                <p className="mt-1 text-sm text-white/58">
                                  {order.game_name} / {order.package_name}
                                </p>
                                <p className="mt-1 text-sm text-white/42">UID: {order.player_identifier}</p>
                              </div>
                              <div className="md:text-right">
                                <p className="font-bold text-[#ffc012]">
                                  {order.currency} {Number(order.price).toFixed(2)}
                                </p>
                                <p className="mt-1 text-xs text-white/42">
                                  {order.created_at ? new Date(order.created_at).toLocaleDateString("th-TH") : "-"}
                                </p>
                              </div>
                              <div>
                                <span className={`inline-flex rounded-full border px-3 py-1 text-xs font-bold ${statusTone(order.status)}`}>
                                  {order.status_label}
                                </span>
                              </div>
                              <button
                                className="h-9 rounded-full border border-emerald-300/20 px-4 text-xs font-bold text-emerald-200 transition hover:bg-emerald-400/10"
                                onClick={() => handleSelectOrder(order)}
                                type="button"
                              >
                                ดูรายละเอียด
                              </button>
                            </article>
                          ))}
                        </div>
                      )}

                      {selectedOrder ? (
                        <OrderDetailCard loading={orderLoading} order={selectedOrder} />
                      ) : null}
                    </div>
                  ) : null}

                  {activeTab === "payment" ? (
                    <div>
                      <h2 className="text-2xl font-bold">ช่องทางชำระเงิน</h2>
                      <p className="mt-1 text-sm text-white/58">
                        เตรียมพื้นที่ไว้สำหรับเชื่อม Wepay และข้อมูลชำระเงินในขั้นถัดไป
                      </p>
                      <div className="mt-6 grid gap-4 md:grid-cols-3">
                        <PaymentCard title="PromptPay" status="รอต่อระบบ" />
                        <PaymentCard title="Wepay" status="รอ API" />
                        <PaymentCard title="บัญชีธนาคาร" status="ยังไม่เปิดใช้" />
                      </div>
                    </div>
                  ) : null}
                </section>
              </>
            ) : null}
          </div>
        </section>
        <SiteFooter />
      </div>
    </main>
  );
}

function InfoField({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
      <p className="text-sm font-semibold text-emerald-300">{label}</p>
      <p className="mt-2 min-h-6 break-words text-base font-bold text-white">{value}</p>
    </div>
  );
}

function ProfileAvatar({
  className,
  user,
}: {
  className: string;
  user: CustomerUser | null;
}) {
  return (
    <div
      className={`grid shrink-0 place-items-center overflow-hidden rounded-full bg-gradient-to-br from-emerald-300 to-cyan-300 font-bold text-[#062016] ${className}`}
    >
      {user?.avatar_url ? (
        <Image
          alt={user.name}
          className="h-full w-full object-cover"
          height={112}
          src={user.avatar_url}
          unoptimized
          width={112}
        />
      ) : user ? (
        initials(user.name)
      ) : (
        "GF"
      )}
    </div>
  );
}

function ProfileInput({
  label,
  onChange,
  required,
  type = "text",
  value,
}: {
  label: string;
  onChange: (value: string) => void;
  required?: boolean;
  type?: string;
  value: string;
}) {
  return (
    <label className="grid gap-2 text-sm font-semibold text-emerald-300">
      {label}
      <input
        className="h-12 rounded-2xl border border-white/10 bg-white/[0.04] px-4 text-base font-semibold text-white outline-none transition placeholder:text-white/30 focus:border-emerald-400 focus:bg-white/[0.06]"
        onChange={(event) => onChange(event.target.value)}
        required={required}
        type={type}
        value={value}
      />
    </label>
  );
}

function OrderDetailCard({ loading, order }: { loading: boolean; order: OrderItem }) {
  const steps =
    order.status_steps?.length
      ? order.status_steps
      : [
          { key: "pending", label: "รับออเดอร์", state: order.status === "pending" ? "current" : "done" as const },
          { key: "paid", label: "ชำระเงิน", state: order.status === "paid" ? "current" : "upcoming" as const },
          { key: "processing", label: "กำลังดำเนินการ", state: order.status === "processing" ? "current" : "upcoming" as const },
          { key: "completed", label: "สำเร็จ", state: order.status === "completed" ? "current" : "upcoming" as const },
        ];

  return (
    <section className="mt-6 rounded-3xl border border-emerald-300/15 bg-white/[0.04] p-5">
      <div className="flex flex-col justify-between gap-4 md:flex-row md:items-start">
        <div>
          <p className="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-300">
            Order detail
          </p>
          <h3 className="mt-2 text-2xl font-bold text-white">{order.order_number}</h3>
          <p className="mt-1 text-sm text-white/58">
            {order.game_name} / {order.package_name}
          </p>
        </div>
        <div className="rounded-2xl border border-white/10 bg-black/10 px-4 py-3 md:text-right">
          <p className="font-bold text-[#ffc012]">
            {order.currency} {Number(order.price).toFixed(2)}
          </p>
          <p className="mt-1 text-xs text-white/42">
            อัปเดตล่าสุด {order.updated_at ? new Date(order.updated_at).toLocaleString("th-TH") : "-"}
          </p>
        </div>
      </div>

      <div className="mt-5 rounded-2xl border border-white/10 bg-black/10 p-4">
        <div className="flex gap-3">
          <Clock className="mt-1 shrink-0 text-emerald-300" size={20} />
          <div>
            <p className="font-semibold text-white">สถานะตอนนี้: {order.status_label}</p>
            <p className="mt-1 text-sm leading-6 text-white/62">
              {loading ? "กำลังโหลดรายละเอียดล่าสุด..." : (order.next_action ?? "รออัปเดตจากทีมงาน")}
            </p>
          </div>
        </div>
      </div>

      <div className="mt-5 grid gap-3 md:grid-cols-4">
        {steps.map((step) => (
          <div
            className={`rounded-2xl border p-4 ${
              step.state === "done"
                ? "border-emerald-300/25 bg-emerald-400/10"
                : step.state === "current"
                  ? "border-[#ffc012]/35 bg-[#ffc012]/10"
                  : "border-white/10 bg-white/[0.03]"
            }`}
            key={step.key}
          >
            <div className="flex items-center gap-2">
              {step.state === "done" ? (
                <CheckCircle2 className="text-emerald-300" size={18} />
              ) : step.state === "current" ? (
                <Clock className="text-[#ffc012]" size={18} />
              ) : (
                <span className="h-[18px] w-[18px] rounded-full border border-white/20" />
              )}
              <p className="text-sm font-bold text-white">{step.label}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="mt-5 grid gap-4 md:grid-cols-3">
        <InfoField label="UID / Player ID" value={order.player_identifier} />
        <InfoField label="Server / Zone" value={order.server_identifier ?? "-"} />
        <InfoField label="เบอร์ติดต่อ" value={order.customer_phone ?? "-"} />
      </div>

      {order.support_note ? (
        <div className="mt-5 rounded-2xl border border-sky-300/20 bg-sky-400/10 p-4">
          <p className="font-semibold text-sky-100">ข้อความจากทีมงาน</p>
          <p className="mt-1 text-sm leading-6 text-white/70">{order.support_note}</p>
        </div>
      ) : null}
    </section>
  );
}

function SummaryCard({
  icon: Icon,
  label,
  value,
}: {
  icon: typeof History;
  label: string;
  value: string;
}) {
  return (
    <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4 shadow-sm shadow-black/10">
      <div className="flex items-center gap-2 text-emerald-600">
        <Icon size={18} />
        <span className="text-sm font-semibold">{label}</span>
      </div>
      <p className="mt-3 text-3xl font-bold text-white">{value}</p>
    </div>
  );
}

function ContactField({
  icon: Icon,
  label,
  value,
}: {
  icon: typeof Phone;
  label: string;
  value: string;
}) {
  return (
    <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
      <div className="flex items-center gap-2 text-emerald-300">
        <Icon size={18} />
        <span className="text-sm font-semibold">{label}</span>
      </div>
      <p className="mt-3 min-h-6 break-words font-bold text-white">{value}</p>
    </div>
  );
}

function PaymentCard({ status, title }: { status: string; title: string }) {
  return (
    <div className="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
      <div className="flex items-center justify-between gap-3">
        <div className="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-600">
          <IdCard size={20} />
        </div>
        <span className="rounded-full border border-white/10 bg-white/[0.06] px-3 py-1 text-xs font-bold text-white/58">
          {status}
        </span>
      </div>
      <p className="mt-4 text-lg font-bold text-white">{title}</p>
      <p className="mt-1 text-sm text-white/58">จะเปิดใช้งานเมื่อเชื่อมระบบชำระเงินจริง</p>
    </div>
  );
}
