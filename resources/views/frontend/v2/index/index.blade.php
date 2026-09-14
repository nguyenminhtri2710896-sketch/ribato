@extends('frontend.v2.layouts.default')

@section('title', 'Razorepay - Best Payment Solution for Online Payments')

@section('style')
<style>
    /* Gradient backgrounds */
    .hero-glow-bg {
        background: radial-gradient(ellipse 60% 50% at 75% 30%, rgba(48, 94, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
    }

    /* Floating card animation */
    @keyframes floatCard {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .floating-card {
        animation: floatCard 5s ease-in-out infinite;
    }

    /* Pulse soft glow */
    @keyframes softPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.85; transform: scale(1.02); }
    }
    .pulse-glow {
        animation: softPulse 4s ease-in-out infinite;
    }

    /* FAQ accordion styling */
    .faq-item input:checked ~ .faq-content {
        max-height: 220px;
        opacity: 1;
        padding-top: 12px;
    }
    .faq-item input:checked ~ .faq-header .faq-icon {
        transform: rotate(180deg);
        color: #305eff;
    }
    .faq-content {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0, 1, 0, 1);
    }
</style>
@endsection

@section('content')
<div class="relative bg-white overflow-hidden">

    <!-- ==================== 1. HERO FOLD WITH PROFESSIONAL BUSINESS WOMAN ==================== -->
    <section class="relative pt-12 pb-16 lg:pt-20 lg:pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto hero-glow-bg">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Pitch (7 cols) -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-[#edf7f7] border border-[#b6ecd1] text-xs font-semibold text-rzp-green-dark">
                    <span class="w-2 h-2 rounded-full bg-rzp-green"></span>
                    <span data-i18n="hero_badge">Hạ tầng thanh toán uy tín hàng đầu</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-bold text-rzp-navy tracking-tight leading-[1.12]">
                    <span data-i18n="hero_title_1">Giải pháp Thanh toán Nâng cao</span> <br/>
                    <span class="text-rzp-blue" data-i18n="hero_title_2">dành cho doanh nghiệp dẫn đầu</span>
                </h1>

                <p class="text-base sm:text-lg text-rzp-navy-muted max-w-xl mx-auto lg:mx-0 font-normal leading-relaxed" data-i18n="hero_desc">
                    Chấp nhận thanh toán từ khách hàng toàn cầu, tự động hóa chi trả nhà cung cấp, quản lý dòng vốn liền mạch và bứt phá tăng trưởng với nền tảng tài chính thông minh.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <button onclick="openContactModal()" class="btn-rzp-primary px-7 py-3.5 text-sm shadow-rzp-btn w-full sm:w-auto">
                        <span data-i18n="hero_signup">Bắt đầu ngay</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

                <!-- Trust Points -->
                <div class="pt-6 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-rzp-navy-muted font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-rzp-green">check_circle</span>
                        <span data-i18n="hero_trust_modes">100+ Phương thức thanh toán</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-rzp-blue">flash_on</span>
                        <span data-i18n="hero_trust_settle">Quyết toán tức thì</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-amber-500">verified</span>
                        <span data-i18n="hero_trust_fees">Miễn phí thiết lập</span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual (5 cols) -->
            <div class="lg:col-span-5 relative flex items-center justify-center">
                <!-- Background Ambient Glow -->
                <div class="w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-gradient-to-tr from-blue-100 to-indigo-100 absolute -z-10 blur-2xl"></div>

                <div class="relative w-full max-w-sm sm:max-w-md">
                    <!-- Business Professional Image -->
                    <div class="rounded-2xl overflow-hidden shadow-2xl border-4 border-white bg-slate-900 relative">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=700&q=80" alt="Razorepay Business Solutions" class="w-full h-[380px] sm:h-[420px] object-cover object-top">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                    </div>

                    <!-- Layered Floating Payment Notification Card -->
                    <div class="absolute -bottom-6 -left-6 bg-white border border-rzp-border rounded-xl p-4 shadow-2xl floating-card flex items-center gap-3.5 min-w-[260px] z-20">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-rzp-green flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl font-bold">check</span>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400 font-medium" data-i18n="hero_card_received">Đã nhận thanh toán</div>
                            <div class="text-base font-extrabold text-rzp-navy">4.999.000 ₫</div>
                            <div class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                <span data-i18n="hero_card_sub">qua Chuyển khoản QR • Tức thì</span>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Badge (Top Right) -->
                    <div class="absolute -top-4 -right-4 bg-[#0c2340] text-white rounded-lg px-3.5 py-2 shadow-xl flex items-center gap-2 text-xs font-semibold z-20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span data-i18n="hero_uptime">Hoạt động 99.99%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust Partners Logo Row -->
        <div class="mt-16 pt-8 border-t border-rzp-border text-center">
            <p class="text-xs uppercase tracking-widest text-rzp-navy-muted font-semibold mb-6" data-i18n="trust_partners">
                Hỗ trợ thanh toán cho hàng triệu doanh nghiệp & kỳ lân công nghệ
            </p>
            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 lg:gap-16 opacity-75 grayscale hover:grayscale-0 transition-all duration-300">
                <span class="text-lg font-black tracking-tighter text-[#1877f2]">facebook</span>
                <span class="text-lg font-black tracking-tighter text-slate-800">SWIGGY</span>
                <span class="text-lg font-black tracking-tighter text-red-500">zomato</span>
                <span class="text-lg font-black tracking-tighter text-slate-800">book<span class="text-red-500">my</span>show</span>
                <span class="text-lg font-black tracking-tighter text-pink-500">NYKAA</span>
                <span class="text-lg font-black tracking-tighter text-blue-500">Flipkart</span>
                <span class="text-lg font-black tracking-tighter text-slate-800">ZERODHA</span>
                <span class="text-lg font-black tracking-tighter text-red-600">airtel</span>
                <span class="text-lg font-black tracking-tighter text-yellow-500">OLA</span>
            </div>
        </div>
    </section>

    <!-- ==================== 2. STRATV26 EVENT / PRODUCT HERO BANNER ==================== -->
    <section class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-gradient-to-r from-[#020a1c] via-[#0b1b3a] to-[#020a1c] rounded-2xl p-8 sm:p-12 text-white shadow-2xl relative overflow-hidden border border-blue-900/40 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-4 max-w-xl z-10 text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-cyan-300 text-xs font-semibold border border-blue-500/30">
                    <span data-i18n="startup26_badge">HỘI NGHỊ THƯỢNG ĐỈNH FINTECH</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                    <span data-i18n="startup26_title_1">STARTUP 26</span> <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-300 to-emerald-400" data-i18n="startup26_title_2">Tương lai của Thương mại Tự động hóa</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-300" data-i18n="startup26_desc">
                    Cùng hơn 10.000 nhà sáng lập, lập trình viên và chuyên gia khám phá thanh toán AI, định tuyến thông minh và kiến trúc tài chính thế hệ mới.
                </p>
                <div class="pt-2">
                    <button onclick="openContactModal()" class="btn-rzp-primary px-6 py-3 text-xs" data-i18n="startup26_cta">
                        Đăng ký tham dự
                    </button>
                </div>
            </div>

            <!-- Right Graphic -->
            <div class="relative flex items-center justify-center gap-4 z-10">
                <div class="w-40 sm:w-48 rounded-xl bg-gradient-to-b from-blue-600 to-indigo-900 p-3.5 shadow-2xl border border-white/20 transform -rotate-6">
                    <div class="flex items-center justify-between text-[10px] font-mono text-cyan-200">
                        <span>Razorepay Turbo</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    </div>
                    <div class="text-sm font-bold text-white mt-1.5">245.000.000 ₫</div>
                    <div class="mt-2.5 p-2 bg-white/10 rounded text-[9px] text-blue-100 flex items-center justify-between">
                        <span>Success Rate</span>
                        <span class="font-bold text-emerald-300">99.8%</span>
                    </div>
                </div>
                <div class="w-40 sm:w-48 rounded-xl bg-gradient-to-b from-indigo-800 to-slate-900 p-3.5 shadow-2xl border border-white/20 transform rotate-6">
                    <div class="flex items-center justify-between text-[10px] font-mono text-emerald-300">
                        <span>Agent Studio</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/30 text-emerald-300">LIVE</span>
                    </div>
                    <div class="text-sm font-bold text-white mt-1.5">Autonomous Flow</div>
                    <div class="mt-2.5 p-2 bg-white/10 rounded text-[9px] text-slate-200 flex items-center justify-between">
                        <span>Latency</span>
                        <span class="font-bold text-cyan-300">18ms</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 3. "THE ALL IN ONE FINANCE PLATFORM" (MAIN 6 CATEGORIES) ==================== -->
    <section class="py-16 lg:py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" id="products">
        <!-- Main Section Title -->
        <div class="max-w-4xl mb-6">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-rzp-navy tracking-tight leading-tight">
                <span data-i18n="main_sec_title_1">Nền tảng tài chính toàn diện</span> <span class="text-rzp-green" data-i18n="main_sec_title_2">bạn luôn tìm kiếm</span>
            </h2>
        </div>

        <!-- Sticky Navigation Pill Bar with Right Action Button -->
        <div class="sticky top-[72px] z-40 bg-white/95 backdrop-blur-md py-4 border-b border-rzp-border flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                <a href="#build-ai-native" class="category-tab active" data-i18n="tab_ai">Xây dựng AI Native</a>
                <a href="#accept-payments" class="category-tab" data-i18n="tab_accept">Nhận thanh toán</a>
                <a href="#make-payouts" class="category-tab" data-i18n="tab_payouts">Chi trả tiền</a>
                <a href="#business-banking" class="category-tab" data-i18n="tab_banking">Ngân hàng số</a>
                <a href="#automate-payroll" class="category-tab" data-i18n="tab_payroll">Bảng lương</a>
                <a href="#credit-and-loans" class="category-tab" data-i18n="tab_credit">Tín dụng & Vốn vay</a>
            </div>
            <button onclick="openContactModal()" class="btn-rzp-primary hidden md:inline-flex px-4 py-2 text-xs flex-shrink-0">
                <span data-i18n="tab_cta">Bắt đầu ngay</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <!-- ==================== Category 1: Build AI Native ==================== -->
        <div class="mt-12 pt-4" id="build-ai-native">
            <div class="flex items-center gap-2 mb-6">
                <h3 class="text-2xl font-bold text-rzp-navy" data-i18n="tab_ai">Xây dựng AI Native</h3>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-cyan-100 text-cyan-800" data-i18n="announcement_badge">MỚI</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Agentic Payments -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-br from-[#0c1927] to-[#162a42] p-3 relative overflow-hidden flex items-center justify-center border-b border-slate-800">
                        <img src="/frontend/v2/images/products/ai-agentic.png" alt="Agentic AI Payments" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 text-[9px] font-mono border border-cyan-500/30">AI Native</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_ai_agentic_title">Thanh toán Agentic AI</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_ai_agentic_desc">Biến mọi đoạn chat thành luồng thanh toán liền mạch với AI-native.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Agent Studio -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-br from-[#0c1927] to-[#162a42] p-3 relative overflow-hidden flex items-center justify-center border-b border-slate-800">
                        <img src="/frontend/v2/images/products/ai-studio.png" alt="Agent Studio" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 text-[9px] font-mono border border-purple-500/30">Workflow</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_ai_studio_title">Agent Studio</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_ai_studio_desc">Ủy quyền quy trình vận hành tài chính cho các trợ lý AI tự động hoàn thành nhiệm vụ.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Payments for AI Builders -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-br from-[#0c1927] to-[#162a42] p-3 relative overflow-hidden flex items-center justify-center border-b border-slate-800">
                        <img src="/frontend/v2/images/products/ai-builders.png" alt="Payments for AI Builders" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 text-[9px] font-mono border border-blue-500/30">1-Click SDK</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_ai_builders_title">Thanh toán cho AI Builders</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_ai_builders_desc">Node thanh toán 1-click tích hợp cho luồng xử lý n8n, Replit và Vercel.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: AI Routing Engine -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-br from-[#0c1927] to-[#162a42] p-3 relative overflow-hidden flex items-center justify-center border-b border-slate-800">
                        <img src="/frontend/v2/images/products/ai-routing.png" alt="AI Routing Engine" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[9px] font-mono border border-emerald-500/30">Smart Rails</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_ai_routing_title">Định tuyến thông minh AI</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_ai_routing_desc">Tối ưu hóa tuyến thanh toán tự động đạt tỷ lệ giao dịch thành công 99.9%.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== Category 2: Accept Payments ==================== -->
        <div class="mt-16 pt-4" id="accept-payments">
            <h3 class="text-2xl font-bold text-rzp-navy mb-6" data-i18n="tab_accept">Nhận thanh toán</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: PAYMENT GATEWAY -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#ebf3ff] to-[#f8faff] p-3 relative overflow-hidden flex items-center justify-center border-b border-blue-100">
                        <img src="/frontend/v2/images/products/payment-gateway.webp" alt="Payment Gateway" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-blue-100 text-rzp-blue text-[9px] font-bold">100+ Modes</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_pg_title">Cổng thanh toán</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_pg_desc">Chấp nhận 100+ phương thức thanh toán với tỷ lệ chuyển đổi cao nhất thị trường.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: PAYMENT LINKS -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eafaf1] to-[#f4fcf7] p-3 relative overflow-hidden flex items-center justify-center border-b border-emerald-100">
                        <img src="/frontend/v2/images/products/payment-links.png" alt="Payment Links" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">Instant SMS/Zalo</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_pl_title">Link thanh toán</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_pl_desc">Chia sẻ link qua SMS, Zalo, WhatsApp, Email và nhận tiền tức thì.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: PAYMENT PAGES -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#f0f4ff] to-[#f9fbff] p-3 relative overflow-hidden flex items-center justify-center border-b border-indigo-100">
                        <img src="/frontend/v2/images/products/payment-pages.webp" alt="Payment Pages" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[9px] font-bold">No-Code Page</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_pp_title">Trang thanh toán</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_pp_desc">Tạo trang thanh toán mang thương hiệu riêng mà không cần lập trình.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: SUBSCRIPTIONS -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#fbf2ff] to-[#fdf9ff] p-3 relative overflow-hidden flex items-center justify-center border-b border-purple-100">
                        <img src="/frontend/v2/images/products/subscriptions.webp" alt="Subscriptions" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[9px] font-bold">AutoPay</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_sub_title">Thanh toán định kỳ</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_sub_desc">Tự động hóa thu phí định kỳ qua thẻ, ví điện tử và trích nợ tự động.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== Category 3: Make Payouts ==================== -->
        <div class="mt-16 pt-4" id="make-payouts">
            <h3 class="text-2xl font-bold text-rzp-navy mb-6" data-i18n="tab_payouts">Chi trả tiền</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: API & Bulk Payouts -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#e8f6ff] to-[#f4faff] p-3 relative overflow-hidden flex items-center justify-center border-b border-cyan-100">
                        <img src="/frontend/v2/images/products/payouts.png" alt="Bulk Payouts" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-cyan-100 text-cyan-800 text-[9px] font-bold">24/7 API</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_bulk_title">Chi trả tự động qua API & Hàng loạt</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_bulk_desc">Chi trả tức thì 24/7 qua chuyển khoản liên ngân hàng nhanh và ví điện tử cả ngày lễ.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Source to Pay / Vendor Payments -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eef4ff] to-[#f9fbff] p-3 relative overflow-hidden flex items-center justify-center border-b border-blue-100">
                        <img src="/frontend/v2/images/products/vendor-payments.png" alt="Vendor Payments" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-bold">OCR Auto</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_s2p_title">Quản trị thanh toán NCC</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_s2p_desc">Tự động hóa đối soát hóa đơn nhà cung cấp và phê duyệt chi nhiều cấp.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Payout Links -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#fff7e6] to-[#fffdf7] p-3 relative overflow-hidden flex items-center justify-center border-b border-amber-100">
                        <img src="/frontend/v2/images/products/payout-links.png" alt="Payout Links" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold">Instant Disbursal</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_plinks_title">Link chi trả nhận tiền</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_plinks_desc">Hoàn tiền, trả thưởng & chiết khấu trực tiếp tới khách hàng chỉ bằng 1 đường link.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Tax & Bank Rails -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#f3f0ff] to-[#faf8ff] p-3 relative overflow-hidden flex items-center justify-center border-b border-indigo-100">
                        <img src="/frontend/v2/images/products/tax-payments.png" alt="Multi-Bank Routing" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[9px] font-bold">Dynamic Rails</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_multibank_title">Định tuyến đa ngân hàng</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_multibank_desc">Tự động chuyển tiếp thông minh giữa các ngân hàng đối tác đảm bảo không gián đoạn.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== Category 4: Start Business Banking ==================== -->
        <div class="mt-16 pt-4" id="business-banking">
            <h3 class="text-2xl font-bold text-rzp-navy mb-6" data-i18n="tab_banking">Ngân hàng số</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Current Account -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#e8f1ff] to-[#f7faff] p-3 relative overflow-hidden flex items-center justify-center border-b border-blue-100">
                        <img src="/frontend/v2/images/products/current-account.png" alt="Current Account" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-bold">Neo-Bank</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_ca_title">Tài khoản thanh toán Doanh nghiệp</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_ca_desc">Trung tâm ngân hàng số tự động hóa thiết kế cho doanh nghiệp hiện đại & startup.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Corporate Cards -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-br from-[#0c1424] via-[#1a263c] to-[#080d17] p-3 relative overflow-hidden flex items-center justify-center border-b border-slate-800">
                        <img src="/frontend/v2/images/products/corporate-cards.png" alt="Corporate Cards" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-amber-400/20 text-amber-300 text-[9px] font-mono border border-amber-400/30">BLACK</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_cards_title">Thẻ tín dụng Doanh nghiệp</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_cards_desc">Thẻ không cần thế chấp với hạn mức cao và hoàn tiền khi chi tiêu SaaS/quảng cáo.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Forex & Global Banking -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eafaf1] to-[#f4fcf7] p-3 relative overflow-hidden flex items-center justify-center border-b border-emerald-100">
                        <img src="/frontend/v2/images/products/forex-banking.png" alt="Forex Banking" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">Global Forex</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_tax_title">Nộp thuế & Ngoại tệ điện tử</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_tax_desc">Nộp thuế thu nhập, thanh toán ngoại tệ quốc tế tự động chỉ với 1 cú nhấp.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Escrow & Accounting Integrations -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eef2ff] to-[#f8faff] p-3 relative overflow-hidden flex items-center justify-center border-b border-indigo-100">
                        <img src="/frontend/v2/images/products/escrow-account.png" alt="Digital Escrow & Accounting" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[9px] font-bold">Escrow & ERP</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_acc_title">Tích hợp Kế toán & Ký quỹ</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_acc_desc">Tài khoản ký quỹ an toàn và đồng bộ hóa 2 chiều thời gian thực với phần mềm kế toán.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== Category 5: Automate Payroll ==================== -->
        <div class="mt-16 pt-4" id="automate-payroll">
            <h3 class="text-2xl font-bold text-rzp-navy mb-6" data-i18n="tab_payroll">Bảng lương</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Card 1: Automated Payroll -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eafaf1] to-[#f4fcf7] p-3 relative overflow-hidden flex items-center justify-center border-b border-emerald-100">
                        <img src="/frontend/v2/images/products/payroll-dashboard.png" alt="Payroll Suite" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">3 Clicks Payroll</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_pay_title">Tính & Trả lương tự động</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_pay_desc">Chuyển lương cho toàn bộ nhân sự chỉ với 3 thao tác kèm bảng tính thuế tự động.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Compliance (PF/PT/TDS) -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#e8f3ff] to-[#f6faff] p-3 relative overflow-hidden flex items-center justify-center border-b border-blue-100">
                        <img src="/frontend/v2/images/products/payroll-compliance.png" alt="Payroll Compliance" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-bold">100% Shield</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_comp_title">Tuân thủ Bảo hiểm & Thuế</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_comp_desc">Tự động khấu trừ bảo hiểm xã hội, thuế TNCN và xuất tờ khai chuẩn quy định.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Self-Service Portal -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#fbf2ff] to-[#fdf9ff] p-3 relative overflow-hidden flex items-center justify-center border-b border-purple-100">
                        <img src="/frontend/v2/images/products/payroll-cards.png" alt="Employee Self-Service" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[9px] font-bold">iOS & Android App</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_emp_title">Cổng tự phục vụ cho nhân viên</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_emp_desc">Ứng dụng di động cho nhân viên tra cứu phiếu lương, ngày phép và chứng từ thuế.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ==================== Category 6: Get Credit & Loans ==================== -->
        <div class="mt-16 pt-4" id="credit-and-loans">
            <h3 class="text-2xl font-bold text-rzp-navy mb-6" data-i18n="tab_credit">Tín dụng & Vốn vay</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Instant Working Capital -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#fff8eb] to-[#fffdf7] p-3 relative overflow-hidden flex items-center justify-center border-b border-amber-100">
                        <img src="/frontend/v2/images/products/working-capital.png" alt="Working Capital" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-amber-200 text-amber-900 text-[9px] font-bold">2 Hours</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_wc_title">Vốn lưu động tức thì</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_wc_desc">Hạn mức tín dụng không thế chấp phê duyệt nhanh trong 2 giờ hỗ trợ quay vòng vốn.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Corporate Credit Line -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eef2ff] to-[#f8faff] p-3 relative overflow-hidden flex items-center justify-center border-b border-indigo-100">
                        <img src="/frontend/v2/images/products/line-of-credit.png" alt="Credit Line" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-indigo-200 text-indigo-900 text-[9px] font-bold">Flexible Line</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_line_title">Hạn mức tín dụng Doanh nghiệp</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_line_desc">Hạn mức tín dụng tuần hoàn linh hoạt, hoàn trả tự động theo doanh thu hàng ngày.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Instant Settlements -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#eafaf1] to-[#f4fcf7] p-3 relative overflow-hidden flex items-center justify-center border-b border-emerald-100">
                        <img src="/frontend/v2/images/products/instant-settlement.png" alt="Instant Settlements" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">24/7 Settle</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="hero_trust_settle">Quyết toán tức thì</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_ai_agentic_desc">Nhận tiền về tài khoản ngân hàng chỉ trong 10 giây bất kể ngày nghỉ lễ.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Corporate Card Capital -->
                <div class="rzp-product-card flex flex-col hover:shadow-rzp-card-hover transition-all group">
                    <div class="h-52 bg-gradient-to-b from-[#f3f0ff] to-[#faf8ff] p-3 relative overflow-hidden flex items-center justify-center border-b border-purple-100">
                        <img src="/frontend/v2/images/products/credit-cards.png" alt="Credit Card Capital" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-sm">
                        <div class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[9px] font-bold">Smart Cards</div>
                    </div>
                    <div class="p-5 flex flex-col justify-between flex-grow">
                        <div>
                            <h4 class="text-base font-bold text-rzp-navy mb-1" data-i18n="card_cards_title">Thẻ tín dụng Vốn doanh nghiệp</h4>
                            <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="card_cards_desc">Quản lý chi tiêu và giải ngân vốn trực tiếp qua thẻ tín dụng doanh nghiệp thông minh.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==================== 4. "POWERING EVERY BUSINESS NEED FROM DAY 1 TO IPO" ==================== -->
    <section class="py-16 bg-[#f8fafc] border-y border-rzp-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <h2 class="text-3xl sm:text-4xl font-bold text-rzp-navy tracking-tight">
                    Powering every business need from <span class="text-rzp-blue">Day 1 to IPO</span>
                </h2>
                <p class="text-xs sm:text-sm text-rzp-navy-muted mt-2">
                    Tailored solutions designed for your specific business stage and growth velocity.
                </p>
            </div>

            <!-- Merchant Showcase Split Banner -->
            <div class="bg-white rounded-2xl border border-rzp-border shadow-rzp-card overflow-hidden grid grid-cols-1 lg:grid-cols-12 items-center">
                <div class="lg:col-span-6 p-8 sm:p-12 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-blue-50 text-rzp-blue text-xs font-semibold">
                        <span data-i18n="case_tag">Bán lẻ & Thương mại điện tử</span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-rzp-navy leading-snug" data-i18n="case_quote">
                        "Razorepay đã giúp chúng tôi nâng tỷ lệ thanh toán thành công lên 94.2%."
                    </h3>
                    <p class="text-xs sm:text-sm text-rzp-navy-muted leading-relaxed" data-i18n="case_desc">
                        Với mã QR động và công nghệ định tuyến giao dịch thông minh, hàng ngàn thương hiệu bán lẻ mang đến trải nghiệm thanh toán không độ trễ cho hàng triệu khách hàng.
                    </p>
                    <div class="pt-2 flex items-center gap-4">
                        <button onclick="openContactModal()" class="btn-rzp-primary px-5 py-2.5 text-xs" data-i18n="case_btn">
                            Xem câu chuyện thành công
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-6 h-full min-h-[320px] bg-gradient-to-br from-[#0c1927] to-[#162a42] p-8 flex flex-col justify-center text-white relative">
                    <div class="space-y-4 max-w-md mx-auto">
                        <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                            <div>
                                <div class="text-xs text-slate-400">Checkout Success Rate</div>
                                <div class="text-3xl font-extrabold text-emerald-400">94.2%</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-400">Conversion Boost</div>
                                <div class="text-2xl font-bold text-cyan-300">+18.5%</div>
                            </div>
                        </div>

                        <div class="bg-slate-900/90 border border-slate-700 rounded-xl p-4 shadow-xl space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-white">Dynamic QR & Smart Checkout</span>
                                <span class="text-emerald-400 font-mono">0.4s Instant Pay</span>
                            </div>
                            <div class="flex items-center gap-3 pt-1 text-[11px] text-slate-300">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> 0 Lag Processing</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400"></span> Instant Webhook</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 5. COMMUNITY & SOCIAL PROOF CARDS ==================== -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Stat Highlight Card -->
            <div class="bg-white p-8 rounded-2xl border border-rzp-border shadow-rzp-card flex flex-col justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-rzp-blue mb-3" data-i18n="stat_scale_tag">Quy mô Doanh nghiệp</div>
                    <h3 class="text-2xl font-bold text-rzp-navy leading-snug mb-3" data-i18n="stat_scale_title">
                        Xử lý hơn 10.000+ Tỷ VNĐ giao dịch mỗi tháng với độ sẵn sàng hệ thống 99.99%.
                    </h3>
                    <p class="text-xs text-rzp-navy-muted leading-relaxed" data-i18n="stat_scale_desc">
                        Kiến trúc ngân hàng phân tán đa vùng xử lý các đợt flash sale trên 1.000 giao dịch/giây mượt mà.
                    </p>
                </div>
                <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="font-bold text-rzp-navy">50M+ Accounts Powered</span>
                    <button onclick="openContactModal()" class="text-rzp-blue font-bold hover:underline" data-i18n="stat_scale_link">Khám phá hạ tầng →</button>
                </div>
            </div>

            <!-- Founder Quote Card -->
            <div class="bg-white p-8 rounded-2xl border border-rzp-border shadow-rzp-card flex flex-col justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-rzp-green mb-3" data-i18n="testi_tag">Đánh giá xác thực</div>
                    <h3 class="text-xl font-medium text-rzp-navy italic leading-snug mb-3" data-i18n="testi_quote">
                        "Các API và hệ thống chi trả tự động của Razorepay giúp đội ngũ kỹ thuật của chúng tôi tập trung phát triển sản phẩm cốt lõi mà không phải tự xây dựng hạ tầng ngân hàng."
                    </h3>
                </div>
                <div class="mt-8 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                        SK
                    </div>
                    <div>
                        <div class="text-xs font-bold text-rzp-navy">Shashank Kumar</div>
                        <div class="text-[11px] text-rzp-navy-muted">CTO & Co-Founder, Unicorn Ventures</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 6. DEEP DARK NAVY DEVELOPER SECTION ==================== -->
    <section class="py-20 bg-[#0c1927] text-white" id="developer-section">
        <!-- Top Green Accent Bar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
            <div class="py-2 px-4 rounded bg-[#009e5c]/20 border border-[#009e5c]/40 text-[#48d08c] text-[11px] font-mono font-bold tracking-widest uppercase flex items-center justify-between">
                <span data-i18n="dev_tag">ĐỘ TIN CẬY • QUY MÔ • BẢO MẬT • TIÊN TIẾN CHO LẬP TRÌNH VIÊN</span>
                <span class="hidden sm:inline">99.99% API SLA</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Details -->
                <div class="lg:col-span-5 space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                        <span data-i18n="dev_title_1">Razorepay được kiến tạo</span> <br/>
                        <span class="text-[#38bdf8]" data-i18n="dev_title_2">dành cho lập trình viên, bởi lập trình viên</span>
                    </h2>

                    <p class="text-slate-300 text-sm leading-relaxed" data-i18n="dev_desc">
                        Khám phá API REST chuẩn mực, webhook tự động gửi lại và các bộ SDK tối ưu giúp bạn chuyển từ thử nghiệm sang vận hành thực tế trong vài phút.
                    </p>

                    <div class="space-y-3 text-xs text-slate-300">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-emerald-400">check_circle</span>
                            <span data-i18n="dev_bullet_1">API RESTful JSON chuẩn mực hỗ trợ Idempotency</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-emerald-400">check_circle</span>
                            <span data-i18n="dev_bullet_2">Bộ SDK hoàn chỉnh cho PHP, Node.js, Python, Java, Go, React, Flutter</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-base text-emerald-400">check_circle</span>
                            <span data-i18n="dev_bullet_3">Bộ sưu tập Postman và trình giả lập Webhook trực quan</span>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-4">
                        <button onclick="openContactModal()" class="btn-rzp-primary px-6 py-3 text-xs">
                            <span data-i18n="dev_btn_keys">Lấy API Keys</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        <button onclick="openContactModal()" class="px-5 py-3 rounded border border-white/20 text-xs font-semibold text-white hover:bg-white/10 transition" data-i18n="dev_btn_docs">
                            Xem tài liệu API
                        </button>
                    </div>
                </div>

                <!-- Right Code Editor Box -->
                <div class="lg:col-span-7">
                    <div class="bg-[#050b14] border border-white/15 rounded-xl shadow-2xl overflow-hidden font-mono text-xs">
                        <div class="px-4 py-3 bg-[#111f31] border-b border-white/10 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-green-500/80"></span>
                                <span class="text-slate-400 ml-2 text-[11px]">POST https://api.razorepay.com/v1/orders</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button onclick="switchDevLang('curl')" id="dev-btn-curl" class="dev-lang-btn active px-2.5 py-1 rounded text-white bg-rzp-blue text-[11px]">cURL</button>
                                <button onclick="switchDevLang('php')" id="dev-btn-php" class="dev-lang-btn px-2.5 py-1 rounded text-slate-400 hover:text-white text-[11px]">PHP</button>
                                <button onclick="switchDevLang('node')" id="dev-btn-node" class="dev-lang-btn px-2.5 py-1 rounded text-slate-400 hover:text-white text-[11px]">Node.js</button>
                                <button onclick="switchDevLang('python')" id="dev-btn-python" class="dev-lang-btn px-2.5 py-1 rounded text-slate-400 hover:text-white text-[11px]">Python</button>
                            </div>
                        </div>

                        <div class="p-5 text-slate-300 overflow-x-auto leading-relaxed">
                            <pre id="dev-code-curl" class="dev-snippet"><code><span class="text-cyan-400">curl</span> -u rzp_live_KEY:SECRET \
  https://api.razorepay.com/v1/orders \
  -H <span class="text-amber-300">"Content-Type: application/json"</span> \
  -d <span class="text-emerald-400">'{
    "amount": 499900,
    "currency": "INR",
    "receipt": "order_rcptid_11",
    "notes": {
      "customer_email": "rahul@company.com"
    }
  }'</span></code></pre>

                            <pre id="dev-code-php" class="dev-snippet hidden"><code><span class="text-purple-400">&lt;?php</span>
<span class="text-cyan-400">$api</span> = <span class="text-amber-300">new</span> Razorepay\Api\Api(<span class="text-emerald-400">'rzp_live_KEY'</span>, <span class="text-emerald-400">'SECRET'</span>);

<span class="text-cyan-400">$order</span> = <span class="text-cyan-400">$api</span>-&gt;order-&gt;create([
    <span class="text-emerald-400">'receipt'</span>         =&gt; <span class="text-emerald-400">'order_rcptid_11'</span>,
    <span class="text-emerald-400">'amount'</span>          =&gt; <span class="text-amber-400">499900</span>, <span class="text-slate-500">// in paise/cents</span>
    <span class="text-emerald-400">'currency'</span>        =&gt; <span class="text-emerald-400">'INR'</span>,
    <span class="text-emerald-400">'payment_capture'</span> =&gt; <span class="text-amber-400">1</span>
]);

<span class="text-cyan-400">echo</span> <span class="text-cyan-400">$order</span>-&gt;id; <span class="text-slate-500">// order_Nx8Yg8Jkq70eLa</span></code></pre>

                            <pre id="dev-code-node" class="dev-snippet hidden"><code><span class="text-purple-400">const</span> Razorepay = <span class="text-cyan-400">require</span>(<span class="text-emerald-400">'razorepay'</span>);

<span class="text-purple-400">const</span> instance = <span class="text-purple-400">new</span> Razorepay({
  key_id: <span class="text-emerald-400">'rzp_live_KEY'</span>,
  key_secret: <span class="text-emerald-400">'SECRET'</span>,
});

<span class="text-purple-400">const</span> response = <span class="text-purple-400">await</span> instance.orders.create({
  amount: <span class="text-amber-400">499900</span>,
  currency: <span class="text-emerald-400">'INR'</span>,
  receipt: <span class="text-emerald-400">'order_rcptid_11'</span>,
});</code></pre>

                            <pre id="dev-code-python" class="dev-snippet hidden"><code><span class="text-purple-400">import</span> razorepay

client = razorepay.Client(auth=(<span class="text-emerald-400">"rzp_live_KEY"</span>, <span class="text-emerald-400">"SECRET"</span>))

order = client.order.create({
    <span class="text-emerald-400">"amount"</span>: <span class="text-amber-400">499900</span>,
    <span class="text-emerald-400">"currency"</span>: <span class="text-emerald-400">"INR"</span>,
    <span class="text-emerald-400">"receipt"</span>: <span class="text-emerald-400">"order_rcptid_11"</span>,
    <span class="text-emerald-400">"payment_capture"</span>: <span class="text-amber-400">1</span>
})</code></pre>
                        </div>

                        <div class="px-5 py-2.5 bg-[#09121d] border-t border-white/5 flex items-center justify-between text-[11px] text-slate-400">
                            <span class="text-emerald-400 font-semibold">HTTP 200 OK (84ms)</span>
                            <span>order_Nx8Yg8Jkq70eLa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 7. NO-CODE PRODUCTS SECTION ==================== -->
    <section class="py-20 bg-[#f1f5fa] border-y border-rzp-border" id="no-code-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <div class="text-sm font-mono text-rzp-navy-muted mb-1" data-i18n="nocode_sub">&lt;không cần viết code?&gt;</div>
                <h2 class="text-3xl sm:text-4xl font-bold text-rzp-navy tracking-tight">
                    <span class="text-rzp-green" data-i18n="nocode_title_1">Bạn không phải lập trình viên?</span> <br/>
                    <span data-i18n="nocode_title_2">Bộ công cụ No-Code của chúng tôi sẽ hỗ trợ bạn</span>
                </h2>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- 1. Payment Links -->
                <div class="bg-white p-7 rounded-xl border border-rzp-border shadow-rzp-card hover:shadow-rzp-card-hover transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-medium text-rzp-navy" data-i18n="nocode_1_title">Link thanh toán</h4>
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-rzp-blue flex items-center justify-center">
                                <span class="material-symbols-outlined text-xl">link</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-rzp-navy leading-snug" data-i18n="nocode_1_desc">
                            Nhận tiền ngay tức thì: Chia sẻ link thanh toán qua Email, tin nhắn hoặc mạng xã hội.
                        </h3>
                    </div>
                </div>

                <!-- 2. Payment Pages -->
                <div class="bg-white p-7 rounded-xl border border-rzp-border shadow-rzp-card hover:shadow-rzp-card-hover transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-medium text-rzp-navy" data-i18n="nocode_2_title">Trang thanh toán</h4>
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-xl">web</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-rzp-navy leading-snug" data-i18n="nocode_2_desc">
                            Nhận thanh toán online không cần website với trang bán hàng mang thương hiệu riêng.
                        </h3>
                    </div>
                </div>

                <!-- 3. Payment Buttons -->
                <div class="bg-white p-7 rounded-xl border border-rzp-border shadow-rzp-card hover:shadow-rzp-card-hover transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-medium text-rzp-navy" data-i18n="nocode_3_title">Nút thanh toán</h4>
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-rzp-green flex items-center justify-center">
                                <span class="material-symbols-outlined text-xl">smart_button</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-rzp-navy leading-snug" data-i18n="nocode_3_desc">
                            Dễ dàng nhúng nút Thanh Toán Ngay vào bất kỳ trang web nào mà không cần biết code.
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 8. "POWERING GROWTH WITH REAL STORIES" (UNICORN FOUNDERS GALLERY) ==================== -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-3xl sm:text-4xl font-bold text-rzp-navy tracking-tight">
                <span data-i18n="founders_title_1">Đồng hành tăng trưởng</span> <span class="text-rzp-blue" data-i18n="founders_title_2">cùng những câu chuyện thực tế</span>
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-xs text-rzp-navy-muted font-semibold" data-i18n="founders_count">10.000.000+ doanh nghiệp tin cậy</span>
            </div>
        </div>

        <!-- Founders Black & White Gallery Carousel Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
            <!-- Founder 1: Swiggy -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80" alt="Swiggy" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Sriharsha Majety</div>
                    <div class="text-[9px] text-slate-300">Swiggy</div>
                </div>
            </div>

            <!-- Founder 2: Zerodha -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80" alt="Zerodha" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Nithin Kamath</div>
                    <div class="text-[9px] text-slate-300">Zerodha</div>
                </div>
            </div>

            <!-- Founder 3: Nykaa -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80" alt="Nykaa" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Falguni Nayar</div>
                    <div class="text-[9px] text-slate-300">Nykaa</div>
                </div>
            </div>

            <!-- Founder 4: Urban Company -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80" alt="Urban Company" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Abhiraj Bhal</div>
                    <div class="text-[9px] text-slate-300">Urban Company</div>
                </div>
            </div>

            <!-- Founder 5: Meesho -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=400&q=80" alt="Meesho" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Vidit Aatrey</div>
                    <div class="text-[9px] text-slate-300">Meesho</div>
                </div>
            </div>

            <!-- Founder 6: Zomato -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=400&q=80" alt="Zomato" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Deepinder Goyal</div>
                    <div class="text-[9px] text-slate-300">Zomato</div>
                </div>
            </div>

            <!-- Founder 7: Cure.fit -->
            <div class="group relative rounded-xl overflow-hidden bg-slate-900 aspect-[3/4] shadow-md cursor-pointer">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80" alt="Cure.fit" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-3 text-white">
                    <div class="text-[11px] font-bold">Mukesh Bansal</div>
                    <div class="text-[9px] text-slate-300">Cure.fit</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 9. FREQUENTLY ASKED QUESTIONS (FAQ ACCORDION) ==================== -->
    <section class="py-20 bg-[#f8fafc] border-t border-rzp-border" id="faqs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Left Title -->
                <div class="lg:col-span-4 space-y-4">
                    <h2 class="text-3xl font-bold text-rzp-navy tracking-tight" data-i18n="faq_title">
                        Câu hỏi thường gặp
                    </h2>
                    <p class="text-xs sm:text-sm text-rzp-navy-muted leading-relaxed" data-i18n="faq_desc">
                        Giải đáp các thắc mắc phổ biến về kích hoạt tài khoản, biểu phí giao dịch, phương thức thanh toán và bảo mật.
                    </p>
                    <div class="pt-2">
                        <button onclick="openContactModal()" class="btn-rzp-secondary text-xs" data-i18n="faq_support">
                            Bạn còn câu hỏi khác? Liên hệ hỗ trợ →
                        </button>
                    </div>
                </div>

                <!-- Right Accordion List -->
                <div class="lg:col-span-8 space-y-4">
                    <!-- FAQ 1 -->
                    <div class="faq-item bg-white border border-rzp-border rounded-xl p-5 shadow-sm">
                        <label class="faq-header flex items-center justify-between cursor-pointer">
                            <input type="checkbox" class="hidden">
                            <span class="text-sm font-bold text-rzp-navy" data-i18n="faq_q1">Razorepay là gì và hoạt động như thế nào?</span>
                            <span class="faq-icon material-symbols-outlined text-rzp-navy-muted transition-transform">expand_more</span>
                        </label>
                        <div class="faq-content text-xs text-rzp-navy-muted leading-relaxed" data-i18n="faq_a1">
                            Razorepay là giải pháp thanh toán toàn diện cho phép doanh nghiệp chấp nhận, xử lý và chi trả thanh toán. Chỉ với một lần tích hợp duy nhất, bạn có thể kết nối Thẻ quốc tế (Visa/Mastercard), mã QR ngân hàng, ví điện tử và chuyển khoản trực tuyến.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq-item bg-white border border-rzp-border rounded-xl p-5 shadow-sm">
                        <label class="faq-header flex items-center justify-between cursor-pointer">
                            <input type="checkbox" class="hidden">
                            <span class="text-sm font-bold text-rzp-navy" data-i18n="faq_q2">Biểu phí giao dịch và chi phí sử dụng là bao nhiêu?</span>
                            <span class="faq-icon material-symbols-outlined text-rzp-navy-muted transition-transform">expand_more</span>
                        </label>
                        <div class="faq-content text-xs text-rzp-navy-muted leading-relaxed" data-i18n="faq_a2">
                            Razorepay áp dụng mức phí cạnh tranh và minh bạch trên mỗi giao dịch thành công. Không phí thiết lập ban đầu, không phí duy trì hàng năm, và có chính sách chiết khấu riêng cho doanh nghiệp có sản lượng lớn.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq-item bg-white border border-rzp-border rounded-xl p-5 shadow-sm">
                        <label class="faq-header flex items-center justify-between cursor-pointer">
                            <input type="checkbox" class="hidden">
                            <span class="text-sm font-bold text-rzp-navy" data-i18n="faq_q3">Các phương thức thanh toán nào được hỗ trợ?</span>
                            <span class="faq-icon material-symbols-outlined text-rzp-navy-muted transition-transform">expand_more</span>
                        </label>
                        <div class="faq-content text-xs text-rzp-navy-muted leading-relaxed" data-i18n="faq_a3">
                            Chúng tôi hỗ trợ 100+ phương thức thanh toán bao gồm thẻ quốc tế (Visa, MasterCard, JCB, Amex), thẻ nội địa, quét mã QR chuyển khoản và các ví điện tử phổ biến.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="faq-item bg-white border border-rzp-border rounded-xl p-5 shadow-sm">
                        <label class="faq-header flex items-center justify-between cursor-pointer">
                            <input type="checkbox" class="hidden">
                            <span class="text-sm font-bold text-rzp-navy" data-i18n="faq_q4">Thời gian đăng ký và kích hoạt tài khoản mất bao lâu?</span>
                            <span class="faq-icon material-symbols-outlined text-rzp-navy-muted transition-transform">expand_more</span>
                        </label>
                        <div class="faq-content text-xs text-rzp-navy-muted leading-relaxed" data-i18n="faq_a4">
                            Bạn có thể đăng ký tài khoản trong 2 phút và bắt đầu thử nghiệm ngay trong môi trường Sandbox. Quy trình xác thực định danh số (eKYC) 100% trực tuyến và hoàn tất trong vòng 24 giờ.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq-item bg-white border border-rzp-border rounded-xl p-5 shadow-sm">
                        <label class="faq-header flex items-center justify-between cursor-pointer">
                            <input type="checkbox" class="hidden">
                            <span class="text-sm font-bold text-rzp-navy" data-i18n="faq_q5">Nền tảng Razorepay có an toàn và đạt chứng nhận bảo mật không?</span>
                            <span class="faq-icon material-symbols-outlined text-rzp-navy-muted transition-transform">expand_more</span>
                        </label>
                        <div class="faq-content text-xs text-rzp-navy-muted leading-relaxed" data-i18n="faq_a5">
                            Có. Razorepay đạt chứng nhận bảo mật cao nhất chuẩn quốc tế PCI-DSS Level 1, chứng chỉ ISO/IEC 27001 và mã hóa dữ liệu 256-bit SSL chuẩn ngân hàng.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 10. SUPERCHARGE YOUR BUSINESS (FINAL CTA) ==================== -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-10 sm:p-14 text-white shadow-xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight" data-i18n="cta_title">
                Tăng tốc doanh nghiệp của bạn cùng Razorepay
            </h2>
            <p class="text-sm sm:text-base text-blue-100 max-w-xl mx-auto mt-3" data-i18n="cta_desc">
                Đăng ký chỉ trong 2 phút và bắt đầu chấp nhận thanh toán tức thì với tỷ lệ chuyển đổi cao nhất thị trường.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button onclick="openContactModal()" class="w-full sm:w-auto px-8 py-3.5 rounded bg-white text-rzp-blue hover:bg-slate-50 font-bold text-sm shadow-md transition" data-i18n="cta_signup">
                    Đăng ký ngay
                </button>
                <button onclick="openContactModal()" class="w-full sm:w-auto px-8 py-3.5 rounded border border-white/30 hover:bg-white/10 text-white font-semibold text-sm transition" data-i18n="cta_contact">
                    Liên hệ tư vấn
                </button>
            </div>
        </div>
    </section>

</div>
@endsection

@section('javascript')
<script>
    // FAQ Accordion Interaction
    document.querySelectorAll('.faq-header').forEach(header => {
        header.addEventListener('click', () => {
            const checkbox = header.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
        });
    });

    // Developer Snippet Switcher
    function switchDevLang(lang) {
        document.querySelectorAll('.dev-lang-btn').forEach(b => {
            b.classList.remove('active', 'bg-rzp-blue', 'text-white');
            b.classList.add('text-slate-400');
        });
        document.querySelectorAll('.dev-snippet').forEach(s => s.classList.add('hidden'));

        const btn = document.getElementById('dev-btn-' + lang);
        const code = document.getElementById('dev-code-' + lang);

        if (btn && code) {
            btn.classList.add('active', 'bg-rzp-blue', 'text-white');
            btn.classList.remove('text-slate-400');
            code.classList.remove('hidden');
        }
    }
</script>
@endsection
