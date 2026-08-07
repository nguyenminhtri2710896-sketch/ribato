<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>RB Wallet | Secure Digital Payment Gateway</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#0E76BC",
                        "background-light": "#F4F9F9",
                        "background-dark": "#0F172A",
                        accent: "#26C6A1",
                        "leaf-blue": "#1488CC",
                    },
                    fontFamily: {
                        display: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "1.5rem",
                        'xl': '2rem',
                    },
                },
            },
        };
    </script>
<style type="text/tailwindcss">
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            @apply backdrop-blur-md;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #e0f2f1 0%, #e1f5fe 100%);
        }
        .dark .gradient-bg {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #26C6A1 0%, #0E76BC 100%);
        }
        .text-gradient {
            background: linear-gradient(to right, #0E76BC, #26C6A1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 48;
        }
        #contact-modal, #privacy-modal, #terms-modal {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        #contact-modal:target, #privacy-modal:target, #terms-modal:target {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        #contact-modal:target .modal-content, #privacy-modal:target .modal-content, #terms-modal:target .modal-content {
            transform: scale(1);
        }
        .modal-content {
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .logo-shadow {
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 transition-colors duration-300">
<nav class="fixed top-0 w-full z-50 px-6 py-4">
<div class="max-w-7xl mx-auto glass-card px-8 py-3 rounded-full flex items-center justify-between shadow-sm">
<div class="flex items-center gap-3">
<div class="w-10 h-10 flex items-center justify-center logo-shadow">
<img alt="RB Wallet Logo" class="w-full h-full object-contain" src="/icons/app_icon_trans.png"/>
</div>
<span class="text-xl font-bold tracking-tight">RB Wallet</span>
</div>
<div class="hidden md:flex items-center gap-8 font-medium">
<a class="hover:text-primary transition-colors" href="#features">Features</a>
<a class="hover:text-primary transition-colors" href="#key-features">Key Features</a>
<a class="hover:text-primary transition-colors" href="#security">Security</a>
<a class="hover:text-primary transition-colors" href="#faq">FAQ</a>
</div>
<div class="flex items-center gap-4">
<button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" onclick="document.documentElement.classList.toggle('dark')">
<span class="material-icons-round text-slate-600 dark:text-slate-400">dark_mode</span>
</button>
<a class="bg-primary hover:bg-blue-700 text-white px-6 py-2.5 rounded-full font-semibold transition-all shadow-lg shadow-primary/20" href="#contact-modal">
                    Get Started
                </a>
</div>
</div>
</nav>
<section class="relative pt-32 pb-20 px-6 overflow-hidden gradient-bg min-h-screen flex items-center">
<div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-blue-200/30 rounded-full blur-3xl pointer-events-none"></div>
<div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[600px] h-[600px] bg-teal-200/20 rounded-full blur-3xl pointer-events-none"></div>
<div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
<div class="z-10 text-center lg:text-left">
<span class="inline-block py-2 px-4 rounded-full bg-white/80 dark:bg-slate-800/80 text-primary font-bold text-sm mb-6 border border-white dark:border-slate-700 shadow-sm uppercase tracking-widest">
                    Next-Gen Payment Ecosystem
                </span>
<h1 class="text-5xl lg:text-7xl font-extrabold leading-tight mb-8">
                    Seamless Payments for <span class="text-gradient">Modern Living.</span>
</h1>
<p class="text-lg text-slate-600 dark:text-slate-400 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Experience the ultimate digital wallet. Instant deposits, lightning-fast transfers, and global accessibility—all wrapped in a secure, airy design.
                </p>
<div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
<button class="bg-primary text-white px-8 py-4 rounded-2xl font-bold flex items-center gap-3 hover:scale-105 transition-transform shadow-xl shadow-primary/30 w-full sm:w-auto justify-center">
<span class="material-icons-round">download</span>
                        Download App
                    </button>
<a class="bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-8 py-4 rounded-2xl font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors w-full sm:w-auto flex items-center justify-center" href="#contact-modal">
                        Contact Sales
                    </a>
</div>
<div class="mt-12 flex items-center gap-6 justify-center lg:justify-start">
<div class="flex -space-x-3">
<img alt="User" class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-900" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwQMuWp5uActl0ZtKQtxQEopITf6NJtzzsss_hHTJDDTJSCsnx2cvncEGgFgY8R1bjcE3UXEL67YFpy5Sc0zReBHKw4Xm-8tzAn0GxT4Pw7wp1BmVzuGMHakrxboyWaqQ9tqPQMKo6pOVS-z-7tSi6qx-691wMOZEkb1Sdy_yuFBDUXHR7jwqTlyQ-tXjouIt-tHmSxM8Ds3ndwIyWv3jQEMc5nwWbwnMcvYMXsX85TqnzXQ1Tp0Ic05YsYjxXm-jGLN59cyvOYAg"/>
<img alt="User" class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-900" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA7l8xP52EvHQhKDeJCTPWT0FuWoepiql_nh9wkkX2xEqLcIn338ZJ5XujNgQkxSA3oj72b4JtL3mL5qCL7csMR0Z57-cMKytchu1J_mqHap8wtAx89BZfbRfDoiotgFHJMgDGBr6CUemN4KEANlAqzSnmWzkcv76o-8oP3lsZkL3aik_uLcmVgW54spS0EGq-PVmxvI06RGO2zA_Y_7UThBE9CIy5EyZhlJei78DEOUk4p_G5wu0OL-ju1bgwDK2CNmf-wksPJ61k"/>
<img alt="User" class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-900" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDXKuKQMarloCGCJF7pWimUXvENGM9Xdhv2bSqjlJ0zENHd_Xm31EG3GBuUvH2qCbzvDdq56TgE2WEMqkOPF2PAqpTGae0PZKYc8XN34WFDhuMpqvnG7cirKBG8FxE9SJSmuZQnCeyqXVbxUcG6haYhA7nfZpRxIknAPlXAhhmB-n63G1vwHkM2RM7HX1QaQ9hO8njzEa20EtM5Lpnccv3kH7xH5N4YOdaS_6IwSA31fa8WPAZ0ND3xGfu0zletck0AmTm_3xStBfk"/>
</div>
<p class="text-sm text-slate-500 font-medium">Trusted by 2M+ active users</p>
</div>
</div>
<div class="relative z-10 flex justify-center">
<div class="relative w-full max-w-[500px]">
<div class="absolute -top-10 -right-10 w-full h-[320px] glass-card rounded-3xl p-6 hidden md:block shadow-2xl overflow-hidden border border-white/50">
<div class="flex items-center justify-between mb-4">
<div class="flex gap-1.5">
<div class="w-3 h-3 rounded-full bg-red-400"></div>
<div class="w-3 h-3 rounded-full bg-amber-400"></div>
<div class="w-3 h-3 rounded-full bg-emerald-400"></div>
</div>
</div>
<div class="space-y-4">
<div class="h-8 w-1/3 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
<div class="grid grid-cols-3 gap-4">
<div class="h-20 bg-blue-100/50 dark:bg-blue-900/30 rounded-2xl"></div>
<div class="h-20 bg-teal-100/50 dark:bg-teal-900/30 rounded-2xl"></div>
<div class="h-20 bg-purple-100/50 dark:bg-purple-900/30 rounded-2xl"></div>
</div>
</div>
</div>
<div class="relative mx-auto w-[280px] h-[580px] bg-slate-900 rounded-[3rem] border-[8px] border-slate-900 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.3)] overflow-hidden">
<div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-900 rounded-b-2xl z-20"></div>
<div class="bg-background-light w-full h-full overflow-y-auto no-scrollbar">
<div class="p-6 flex justify-between items-center bg-white">
<div class="w-8 h-8 flex items-center justify-center logo-shadow">
<img alt="Logo Icon" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA0NZZHK010_Dpsm0SGr-4nwaqiejjoik-0DbsfUSg1Cvi-3VaYB8l0M6FqBJiYLzVARiz1dNGHKWGssOQuj9s6RzjOAR2BG2x7cT_xjezdK_rO8YTQiyB45dbb3R0LyADksmm-9sgn8UEY-CxuVgNytJpoRSakJ3i_tdcfwCxqMGxSfpLCRet6EmzpfCtE496LIrcfQ-dM3eQovHJ981D2gxfe2i33hwcly7d4RzkBoG_VatWnwNjM_zwpHRcKZaY0NdsDTLIwBaU"/>
</div>
<div class="text-xs font-bold text-slate-800">Ví VND</div>
<span class="material-icons-round text-slate-800">notifications</span>
</div>
<div class="mx-4 mt-2 p-5 hero-gradient rounded-3xl text-white shadow-lg">
<div class="flex justify-between items-center opacity-80 mb-1">
<span class="text-xs">Total Balance</span>
<span class="material-icons-round text-sm">visibility</span>
</div>
<div class="text-2xl font-bold mb-6">120,000,000 <span class="text-sm font-normal">VND</span></div>
<div class="grid grid-cols-3 gap-2">
<div class="flex flex-col items-center gap-1">
<div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
<span class="material-icons-round">add_circle_outline</span>
</div>
<span class="text-[10px] font-medium">Deposit</span>
</div>
<div class="flex flex-col items-center gap-1">
<div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
<span class="material-icons-round">send</span>
</div>
<span class="text-[10px] font-medium">Send</span>
</div>
<div class="flex flex-col items-center gap-1">
<div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
<span class="material-icons-round">file_download</span>
</div>
<span class="text-[10px] font-medium">Withdraw</span>
</div>
</div>
</div>
<div class="p-4 mt-4">
<div class="text-sm font-bold mb-3 flex justify-between items-center">
                                    Recent Transactions
                                    <span class="material-icons-round text-sm">chevron_right</span>
</div>
<div class="space-y-2">
<div class="p-3 bg-white rounded-2xl flex items-center justify-between shadow-sm border border-slate-50">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-teal-50 flex items-center justify-center">
<span class="material-icons-round text-accent text-sm">arrow_downward</span>
</div>
<div>
<div class="text-[11px] font-bold">Nhận tiền</div>
<div class="text-[9px] text-slate-400">Dec 17, 4:11 PM</div>
</div>
</div>
<div class="text-[11px] font-bold text-accent">+10,000 VND</div>
</div>
<div class="p-3 bg-white rounded-2xl flex items-center justify-between shadow-sm border border-slate-50">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
<span class="material-icons-round text-red-500 text-sm">arrow_upward</span>
</div>
<div>
<div class="text-[11px] font-bold">Chuyển tiền</div>
<div class="text-[9px] text-slate-400">Nov 14, 3:56 PM</div>
</div>
</div>
<div class="text-[11px] font-bold text-slate-800">-50,000 VND</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<section class="py-24 px-6 bg-white dark:bg-slate-900/50" id="features">
<div class="max-w-7xl mx-auto">
<div class="text-center mb-16">
<h2 class="text-4xl font-bold mb-4">Core Features</h2>
<p class="text-slate-500 max-w-2xl mx-auto">Designed for speed, built for reliability. Discover why RB Wallet is the preferred choice for digital assets.</p>
</div>
<div class="grid md:grid-cols-3 gap-8">
<div class="p-10 rounded-[2.5rem] bg-slate-50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-100 dark:hover:border-slate-700 hover:shadow-xl group">
<div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-icons-round text-primary text-3xl">account_balance_wallet</span>
</div>
<h3 class="text-2xl font-bold mb-4">Instant Deposits</h3>
<p class="text-slate-500 dark:text-slate-400 leading-relaxed">Top up your wallet instantly from any bank or mobile money provider. No waiting periods, just pure speed.</p>
</div>
<div class="p-10 rounded-[2.5rem] bg-slate-50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-100 dark:hover:border-slate-700 hover:shadow-xl group">
<div class="w-16 h-16 bg-teal-100 dark:bg-teal-900/30 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-icons-round text-accent text-3xl">bolt</span>
</div>
<h3 class="text-2xl font-bold mb-4">Secure Transfers</h3>
<p class="text-slate-500 dark:text-slate-400 leading-relaxed">Send funds to contacts or via phone numbers with end-to-end encryption. Security you can trust at every step.</p>
</div>
<div class="p-10 rounded-[2.5rem] bg-slate-50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 transition-all border border-transparent hover:border-slate-100 dark:hover:border-slate-700 hover:shadow-xl group">
<div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-icons-round text-purple-600 text-3xl">payments</span>
</div>
<h3 class="text-2xl font-bold mb-4">Easy Withdrawals</h3>
<p class="text-slate-500 dark:text-slate-400 leading-relaxed">Cashing out is as simple as a few taps. Connect to saved banks or enter new details for rapid withdrawal.</p>
</div>
</div>
</div>
</section>
<section class="py-24 px-6 bg-slate-50 dark:bg-slate-900" id="key-features">
<div class="max-w-7xl mx-auto">
<div class="grid lg:grid-cols-2 gap-16 items-center">
<div class="order-2 lg:order-1">
<span class="text-primary font-bold text-sm uppercase tracking-widest mb-4 block">Advanced Capabilities</span>
<h2 class="text-4xl lg:text-5xl font-extrabold mb-10 leading-tight">Elevate Your Financial Experience</h2>
<div class="space-y-8">
<div class="flex gap-6 items-start p-6 rounded-3xl hover:bg-white dark:hover:bg-slate-800 transition-all hover:shadow-lg group">
<div class="w-14 h-14 shrink-0 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center group-hover:bg-primary transition-colors">
<span class="material-symbols-outlined text-primary group-hover:text-white transition-colors">currency_exchange</span>
</div>
<div>
<h4 class="text-xl font-bold mb-2">Multi-currency Support</h4>
<p class="text-slate-500 dark:text-slate-400">Hold and exchange 40+ currencies at real-time interbank rates. Perfect for globetrotters and digital nomads.</p>
</div>
</div>
<div class="flex gap-6 items-start p-6 rounded-3xl hover:bg-white dark:hover:bg-slate-800 transition-all hover:shadow-lg group border border-transparent">
<div class="w-14 h-14 shrink-0 bg-teal-50 dark:bg-teal-900/20 rounded-2xl flex items-center justify-center group-hover:bg-accent transition-colors">
<span class="material-symbols-outlined text-accent group-hover:text-white transition-colors">monitoring</span>
</div>
<div>
<h4 class="text-xl font-bold mb-2">Real-time Analytics</h4>
<p class="text-slate-500 dark:text-slate-400">Track your spending patterns with AI-powered insights. Personalized budgets that help you save more every month.</p>
</div>
</div>
<div class="flex gap-6 items-start p-6 rounded-3xl hover:bg-white dark:hover:bg-slate-800 transition-all hover:shadow-lg group border border-transparent">
<div class="w-14 h-14 shrink-0 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center group-hover:bg-red-500 transition-colors">
<span class="material-symbols-outlined text-red-500 group-hover:text-white transition-colors">shield_lock</span>
</div>
<div>
<h4 class="text-xl font-bold mb-2">Fraud Prevention</h4>
<p class="text-slate-500 dark:text-slate-400">24/7 proactive monitoring with biometric authentication and dynamic CVV for your virtual cards.</p>
</div>
</div>
</div>
</div>
<div class="order-1 lg:order-2 relative">
<div class="relative z-10 glass-card p-8 rounded-[3rem] shadow-2xl border-white/50">
<div class="mb-8">
<div class="text-sm font-bold text-slate-400 mb-4 uppercase tracking-widest">Portfolio Performance</div>
<div class="flex items-end gap-3 mb-6">
<div class="text-4xl font-bold">$54,230.15</div>
<div class="text-accent flex items-center text-sm font-bold mb-1">
<span class="material-icons-round text-sm">arrow_upward</span>
                                    +12.5%
                                </div>
</div>
<div class="flex items-end gap-2 h-40">
<div class="w-full bg-blue-100 dark:bg-blue-900/30 rounded-t-lg h-[40%] transition-all hover:h-[45%]"></div>
<div class="w-full bg-blue-200 dark:bg-blue-900/40 rounded-t-lg h-[65%] transition-all hover:h-[70%]"></div>
<div class="w-full bg-teal-300 dark:bg-teal-900/50 rounded-t-lg h-[50%] transition-all hover:h-[55%]"></div>
<div class="w-full bg-primary rounded-t-lg h-[90%] transition-all hover:h-[95%]"></div>
<div class="w-full bg-teal-400 dark:bg-teal-900/60 rounded-t-lg h-[75%] transition-all hover:h-[80%]"></div>
<div class="w-full bg-blue-200 dark:bg-blue-900/40 rounded-t-lg h-[60%] transition-all hover:h-[65%]"></div>
</div>
</div>
<div class="space-y-4">
<div class="p-4 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-between shadow-sm border border-slate-100 dark:border-slate-700">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center">
<span class="material-symbols-outlined text-primary">euro</span>
</div>
<span class="font-bold">EUR Wallet</span>
</div>
<span class="font-bold text-slate-400">€2,450.00</span>
</div>
<div class="p-4 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-between shadow-sm border border-slate-100 dark:border-slate-700">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center">
<span class="material-symbols-outlined text-primary">currency_pound</span>
</div>
<span class="font-bold">GBP Wallet</span>
</div>
<span class="font-bold text-slate-400">£1,890.50</span>
</div>
</div>
</div>
<div class="absolute -top-10 -right-10 w-40 h-40 bg-accent/20 rounded-full blur-3xl -z-10"></div>
<div class="absolute -bottom-10 -left-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl -z-10"></div>
</div>
</div>
</div>
</section>
<section class="py-24 px-6 bg-background-light dark:bg-background-dark">
<div class="max-w-7xl mx-auto glass-card rounded-[3rem] p-12 md:p-20 text-center relative overflow-hidden shadow-lg border-white/20">
<div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
<div>
<div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">2M+</div>
<div class="text-slate-500 font-medium">Active Users</div>
</div>
<div>
<div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">$4.5B</div>
<div class="text-slate-500 font-medium">Volume Processed</div>
</div>
<div>
<div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">99.9%</div>
<div class="text-slate-500 font-medium">Uptime Guarantee</div>
</div>
<div>
<div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">4.9/5</div>
<div class="text-slate-500 font-medium">App Store Rating</div>
</div>
</div>
</div>
</section>

<section id="security" class="py-24 px-6 bg-slate-900 text-white relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-primary/20 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[600px] h-[600px] bg-accent/10 rounded-full blur-[120px]"></div>
        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz4KPHBhdGggZD0iTTAgNDBMMDQwIDBoMXY0MHoiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz4KPC9zdmc+')] opacity-20"></div>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center mb-20">
            <span class="inline-block py-1 px-3 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-xs uppercase tracking-widest border border-emerald-500/30 mb-6">
                Bank-Grade Protection
            </span>
            <h2 class="text-4xl md:text-5xl font-extrabold mb-6">Uncompromising Security</h2>
            <p class="text-slate-400 max-w-2xl mx-auto text-lg">
                Your peace of mind is our top priority. We employ defense-in-depth strategies to safeguard your identity, data, and assets at every level.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="p-8 rounded-3xl bg-slate-800/50 border border-slate-700 hover:bg-slate-800 transition-all hover:-translate-y-1 group">
                <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-icons-round text-blue-400 text-3xl">lock</span>
                </div>
                <h3 class="text-xl font-bold mb-3">E2E Encryption</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    All sensitive data is encrypted in transit and at rest using military-grade AES-256 encryption protocols.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="p-8 rounded-3xl bg-slate-800/50 border border-slate-700 hover:bg-slate-800 transition-all hover:-translate-y-1 group">
                <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-icons-round text-emerald-400 text-3xl">fingerprint</span>
                </div>
                <h3 class="text-xl font-bold mb-3">Biometric Auth</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Secure access with FaceID and TouchID. Multi-factor authentication (MFA) is enforced for sensitive actions.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="p-8 rounded-3xl bg-slate-800/50 border border-slate-700 hover:bg-slate-800 transition-all hover:-translate-y-1 group">
                <div class="w-14 h-14 bg-purple-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-icons-round text-purple-400 text-3xl">security</span>
                </div>
                <h3 class="text-xl font-bold mb-3">Fraud Detection</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Our AI monitors transactions 24/7 to instantly detect and block suspicious anomalies before they happen.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="p-8 rounded-3xl bg-slate-800/50 border border-slate-700 hover:bg-slate-800 transition-all hover:-translate-y-1 group">
                <div class="w-14 h-14 bg-amber-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-icons-round text-amber-400 text-3xl">verified_user</span>
                </div>
                <h3 class="text-xl font-bold mb-3">Fully Compliant</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    We adhere to strict global financial regulations (PCI-DSS, GDPR) to ensure your legal protection.
                </p>
            </div>
        </div>


    </div>
</section>
<div class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden bg-slate-900/40 backdrop-blur-md" id="contact-modal">
<a class="absolute inset-0 cursor-default" href="#"></a>
<div class="modal-content relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
<a class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" href="#">
<span class="material-icons-round">close</span>
</a>
<div class="flex justify-center mb-8">
<div class="w-14 h-14 logo-shadow">
<img alt="RIBATO LLC Logo" class="w-full h-full object-contain" src="/icons/app_icon_trans.png"/>
</div>
</div>
<div class="text-center mb-10">
<h2 class="text-3xl font-extrabold mb-3">Get in Touch with RIBATO</h2>
<p class="text-slate-500 dark:text-slate-400">Tell us how we can help your business grow.</p>
</div>
<form class="space-y-6" id="contact-form">
<div class="grid md:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">Full Name</label>
<input class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm" name="full_name" placeholder="John Doe" required type="text"/>
</div>
<div>
<label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">Business Email</label>
<input class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm" name="email" placeholder="john@company.com" required type="email"/>
</div>
</div>
<div>
<label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">Company Name</label>
<input class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm" name="company" placeholder="Your Business" type="text"/>
</div>
<div>
<label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">How can we help?</label>
<textarea class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm resize-none" name="message" placeholder="Briefly describe your requirements..." required rows="4"></textarea>
</div>
<div id="form-feedback" class="hidden p-4 rounded-2xl text-sm font-bold"></div>
<button class="w-full bg-primary hover:bg-blue-700 text-white py-5 rounded-2xl font-bold text-lg shadow-xl shadow-primary/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed" id="submit-btn" type="submit">
                    Send Message
                </button>
</form>
</div>
</div>
<div class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden bg-slate-900/40 backdrop-blur-md" id="privacy-modal">
    <a class="absolute inset-0 cursor-default" href="#"></a>
    <div class="modal-content relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
        <a class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors z-10" href="#">
            <span class="material-icons-round">close</span>
        </a>
        
        <div class="prose prose-slate dark:prose-invert max-w-none">
            <header class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-6">
                <h1 class="text-3xl font-extrabold mb-2 text-slate-900 dark:text-white">RbWallet — Privacy Policy (Official)</h1>
                <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                    <p><strong>Last Updated:</strong> <span>November 13, 2025</span></p>
                    <p><strong>Effective Date:</strong> <span>November 13, 2025</span></p>
                </div>
            </header>

            <main class="content">
                <section class="intro">
                  <h2>1. Introduction</h2>
                  <p>
                    RbWallet ("we", "our", "the App", "the Service") is committed to protecting your privacy and personal data. This Privacy Policy ("Policy") explains how we collect, use, store, share, and protect user information.
                  </p>
                  <p class="font-bold p-4 bg-slate-50 dark:bg-slate-800 rounded-xl my-4 border border-slate-100 dark:border-slate-700">
                    By using RbWallet, you confirm that you have read and agree to this Policy.
                  </p>
                </section>
        
                <section id="scope">
                  <h2>2. Scope of Application</h2>
                  <p>This Policy applies to:</p>
                  <ul>
                    <li>the RbWallet application,</li>
                    <li>the website (when launched),</li>
                    <li>customer service,</li>
                    <li>identity verification systems,</li>
                    <li>related payment and banking integrations.</li>
                  </ul>
                </section>
        
                <section id="dataCollection">
                  <h2>3. Data We Collect</h2>
        
                  <h3>3.1 Data You Provide Directly</h3>
                  <ul>
                    <li>Personal information: full name, date of birth, nationality.</li>
                    <li>KYC documents: ID card/Citizen ID/Passport, selfie photos, biometrics.</li>
                    <li>Contact information: email, phone number.</li>
                    <li>Linked bank accounts.</li>
                    <li>Support feedback, complaint reports.</li>
                  </ul>
        
                  <h3>3.2 Automatically Collected Data</h3>
                  <ul>
                    <li>Device information (model, operating system, language).</li>
                    <li>IP address, approximate geographic region.</li>
                    <li>Advertising ID (if permitted).</li>
                    <li>Application usage logs.</li>
                  </ul>
        
                  <h3>3.3 Transaction Data</h3>
                  <ul>
                    <li>deposit, withdrawal, transfer history,</li>
                    <li>amount, time, transaction status,</li>
                    <li>recipient/sender information.</li>
                  </ul>
        
                  <h3>3.4 Data from Third Parties</h3>
                  <ul>
                    <li>partner banks,</li>
                    <li>payment service providers,</li>
                    <li>identity verification systems (eKYC),</li>
                    <li>law enforcement agencies (when legally required).</li>
                  </ul>
                </section>
        
                <section id="purpose">
                  <h2>4. Purpose of Data Processing</h2>
                  <p>We use your data to:</p>
        
                  <h3>4.1 Service Operations</h3>
                  <ul>
                    <li>create and manage accounts,</li>
                    <li>process transactions,</li>
                    <li>verify identity (KYC/AML),</li>
                    <li>detect fraud and ensure security.</li>
                  </ul>
        
                  <h3>4.2 Customer Care</h3>
                  <ul>
                    <li>provide support,</li>
                    <li>handle disputes,</li>
                    <li>send important notifications.</li>
                  </ul>
        
                  <h3>4.3 Legal Compliance</h3>
                  <ul>
                    <li>anti-money laundering (AML),</li>
                    <li>counter-terrorism financing (CFT),</li>
                    <li>reporting to regulatory authorities when required.</li>
                  </ul>
        
                  <h3>4.4 Service Improvement</h3>
                  <ul>
                    <li>analyze aggregated data,</li>
                    <li>optimize application performance,</li>
                    <li>develop new features.</li>
                  </ul>
        
                  <p class="font-bold">We do not sell your personal data.</p>
                </section>
        
                <section id="sharing">
                  <h2>5. Data Sharing</h2>
                  <p>We only share data when necessary and legal, including:</p>
                  <ul>
                    <li>Partner banks: processing deposits/withdrawals.</li>
                    <li>Payment partners: confirming transactions.</li>
                    <li>eKYC providers: identity verification.</li>
                    <li>Security companies: fraud detection.</li>
                    <li>Law enforcement: when legally required.</li>
                    <li>Infrastructure service providers (cloud, servers).</li>
                  </ul>
                  <p>All related parties are bound by confidentiality agreements.</p>
                </section>
        
                <section id="storage">
                  <h2>6. Data Storage & Security</h2>
        
                  <h3>6.1 Storage</h3>
                  <p>Data is stored on servers meeting international security standards (e.g., ISO 27001) for:</p>
                  <ul>
                    <li>as required by law, or</li>
                    <li>as long as you continue using the service.</li>
                  </ul>
        
                  <h3>6.2 Security Measures</h3>
                  <ul>
                    <li>Bank-level encryption (AES-256, TLS 1.3).</li>
                    <li>Real-time fraud detection systems.</li>
                    <li>Strict internal access controls.</li>
                    <li>2FA and device protection.</li>
                  </ul>
                  <p>While we strive to the maximum, no system can guarantee 100% security.</p>
                </section>
        
                <section id="rights">
                  <h2>7. User Rights</h2>
                  <p>You have the right to:</p>
                  <ul>
                    <li>request access to personal data,</li>
                    <li>request correction of incorrect information,</li>
                    <li>request account deletion (as permitted by law),</li>
                    <li>request restriction or object to data processing,</li>
                    <li>download your personal data.</li>
                  </ul>
                  <div class="p-4 bg-amber-50 dark:bg-amber-900/20 text-amber-900 dark:text-amber-100 rounded-xl my-4 border border-amber-100 dark:border-amber-900/30">
                    <strong>We may refuse requests if related to:</strong>
                    <ul class="mt-2 mb-0">
                      <li>legal investigations,</li>
                      <li>compliance obligations,</li>
                      <li>fraud prevention.</li>
                    </ul>
                  </div>
                </section>
        
                <section id="cookies">
                  <h2>8. Cookies & Tracking Technologies</h2>
                  <p class="italic text-sm text-slate-500">(Applies when website is launched)</p>
                  <p>We may use:</p>
                  <ul>
                    <li>functional cookies,</li>
                    <li>analytics cookies,</li>
                    <li>security cookies.</li>
                  </ul>
                  <p>You can disable cookies in your browser, but some features may not work.</p>
                </section>
        
                <section id="transfer">
                  <h2>9. International Data Transfer</h2>
                  <p>If necessary (e.g., international servers), we will ensure:</p>
                  <ul>
                    <li>data is encrypted,</li>
                    <li>processing only with security standards equivalent to or higher than Vietnamese law.</li>
                  </ul>
                </section>
        
                <section id="children">
                  <h2>10. Children's Privacy</h2>
                  <p>RbWallet is not intended for users under 18 years of age.</p>
                  <p>We do not knowingly collect data from children.</p>
                </section>
        
                <section id="changes">
                  <h2>11. Policy Changes</h2>
                  <p>We may update this Policy at any time.</p>
                  <p>Notifications will be sent through:</p>
                  <ul>
                    <li>the application,</li>
                    <li>email,</li>
                    <li>the website.</li>
                  </ul>
                  <p>Continued use of the service means you accept the updates.</p>
                </section>
        
                <section id="contact">
                  <h2>12. Contact</h2>
                  <div class="contact-info">
                    <p><strong>RbWallet Support Team</strong></p>
                    <p>
                      <strong>Email:</strong>
                      <a href="mailto:info@ribato.com">info@ribato.com</a>
                    </p>
                    <p><strong>Website:</strong> <span>ribato.com</span></p>
                  </div>
                </section>
              </main>
        </div>
    </div>
</div>

</div>
</div>

<div class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden bg-slate-900/40 backdrop-blur-md" id="terms-modal">
    <a class="absolute inset-0 cursor-default" href="#"></a>
    <div class="modal-content relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
        <a class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors z-10" href="#">
            <span class="material-icons-round">close</span>
        </a>
        
        <div class="prose prose-slate dark:prose-invert max-w-none">
            <header class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-6">
                <h1 class="text-3xl font-extrabold mb-2 text-slate-900 dark:text-white">Terms of Service</h1>
                <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                    <p><strong>Effective Date:</strong> <span>November 13, 2025</span></p>
                </div>
            </header>

            <main class="content">
                <section>
                    <h2>1. Agreement to Terms</h2>
                    <p>By accessing or using the RbWallet application and services ("Service"), you agree to be bound by these Terms of Service ("Terms"). If you disagree with any part of the terms, you may not access the Service.</p>
                </section>

                <section>
                    <h2>2. User Accounts</h2>
                    <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
                    <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password.</p>
                </section>

                <section>
                    <h2>3. Financial Services</h2>
                    <p>RbWallet provides digital wallet services, including but not limited to deposits, withdrawals, and transfers. All financial transactions are subject to verification and may be delayed or rejected for security or compliance reasons.</p>
                </section>

                <section>
                    <h2>4. Prohibited Uses</h2>
                    <p>You may not use the Service for any illegal or unauthorized purpose. You agree not to:</p>
                    <ul>
                        <li>Use the service for money laundering or terrorist financing.</li>
                        <li>Violate any laws in your jurisdiction.</li>
                        <li>Infringe upon the rights of others.</li>
                        <li>Interfere with or disrupt the Service or servers.</li>
                    </ul>
                </section>

                <section>
                    <h2>5. Intellectual Property</h2>
                    <p>The Service and its original content (excluding Content provided by users), features, and functionality are and will remain the exclusive property of RbWallet and its licensors.</p>
                </section>

                <section>
                    <h2>6. Termination</h2>
                    <p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>
                </section>

                <section>
                    <h2>7. Limitation of Liability</h2>
                    <p>In no event shall RbWallet, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.</p>
                </section>

                <section>
                    <h2>8. Changes</h2>
                    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. What constitutes a material change will be determined at our sole discretion.</p>
                </section>

                <section>
                    <h2>9. Contact Us</h2>
                    <p>If you have any questions about these Terms, please contact us at <a href="mailto:info@ribato.com">info@ribato.com</a>.</p>
                </section>
            </main>
        </div>
    </div>
</div>

<footer class="footer py-20 px-6 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors">
<div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12 lg:gap-24">
<div class="col-span-2 md:col-span-1">
<div class="flex items-center gap-2 mb-6">
<div class="w-8 h-8 flex items-center justify-center logo-shadow">
<img alt="RB Wallet Logo" class="w-full h-full object-contain" src="/icons/app_icon_trans.png"/>
</div>
<span class="text-xl font-bold tracking-tight">RB Wallet</span>
</div>
<div class="space-y-4 mb-8">
<p class="text-slate-900 dark:text-slate-100 font-bold text-sm tracking-wide">RIBATO LLC</p>
<div class="flex items-start gap-3 text-slate-500 text-sm">
<span class="material-icons-round text-base mt-0.5 text-primary">place</span>
<span class="leading-relaxed">732 S 6th ST, STE N, Las Vegas, NV 89101, USA</span>
</div>
<div class="flex items-center gap-3 text-slate-500 text-sm">
<span class="material-icons-round text-base text-primary">phone</span>
<a class="hover:text-primary transition-colors" href="tel:+17026098830">+1 (702) 609-8830</a>
</div>
<div class="flex items-center gap-3 text-slate-500 text-sm">
<span class="material-icons-round text-base text-primary">email</span>
<a class="hover:text-primary transition-colors" href="mailto:info@ribato.com">info@ribato.com</a>
</div>
</div>
<div class="flex gap-4">
<a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="#">
<span class="material-icons-round text-lg">facebook</span>
</a>
<a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="#">
<span class="material-icons-round text-lg">alternate_email</span>
</a>
<a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="#">
<span class="material-icons-round text-lg">public</span>
</a>
</div>
</div>
<div>
<h4 class="font-bold mb-6">Company</h4>
<ul class="space-y-4 text-slate-500 text-sm">
<li><a class="hover:text-primary transition-colors" href="#">About Us</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Careers</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Press Kit</a></li>
<li><a class="hover:text-primary transition-colors" href="#contact-modal">Contact</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-6">Product</h4>
<ul class="space-y-4 text-slate-500 text-sm">
<li><a class="hover:text-primary transition-colors" href="#">Personal Wallet</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Business API</a></li>
<li><a class="hover:text-primary transition-colors" href="#security">Security</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Pricing</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-6">Legal</h4>
<ul class="space-y-4 text-slate-500 text-sm">
<li><a class="hover:text-primary transition-colors" href="#privacy-modal">Privacy Policy</a></li>
<li><a class="hover:text-primary transition-colors" href="#terms-modal">Terms of Service</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Cookie Policy</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Compliance</a></li>
</ul>
</div>
</div>
<div class="max-w-7xl mx-auto mt-20 pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
<p>© 2023 RIBATO LLC. All rights reserved.</p>
<div class="flex gap-6">

</div>
</div>
</footer>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contact-form');
        const submitBtn = document.getElementById('submit-btn');
        const feedback = document.getElementById('form-feedback');
        const originalBtnText = submitBtn.innerHTML;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Clear previous feedback
            feedback.classList.add('hidden');
            feedback.className = 'hidden p-4 rounded-2xl text-sm font-bold';
            
            // Set loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-icons-round animate-spin mr-2">refresh</span> Sending...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch("{{ route('frontend.contact.submit') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                feedback.classList.remove('hidden');
                
                if (response.ok && result.success) {
                    feedback.classList.add('bg-green-100', 'text-green-700', 'dark:bg-green-900/30', 'dark:text-green-400');
                    feedback.innerHTML = `<div class="flex items-center gap-2"><span class="material-icons-round">check_circle</span> ${result.message}</div>`;
                    form.reset();
                } else {
                    throw new Error(result.message || 'Something went wrong');
                }
            } catch (error) {
                console.error('Error:', error);
                feedback.classList.remove('hidden');
                feedback.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-900/30', 'dark:text-red-400');
                feedback.innerHTML = `<div class="flex items-center gap-2"><span class="material-icons-round">error</span> ${error.message || 'Failed to send message. Please try again.'}</div>`;
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });
</script>
</html>