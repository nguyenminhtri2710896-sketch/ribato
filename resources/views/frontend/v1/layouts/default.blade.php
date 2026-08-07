<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RIBATO LLC | Global Payment Solutions</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#8B0000", // Crimson red
                        "primary-dark": "#4A0E0E", // Dark burgundy
                        "background-light": "#FAF8F8",
                        "background-dark": "#1F1111",
                        accent: "#A31D1D",
                    },
                    fontFamily: {
                        display: ["Plus Jakarta Sans", "sans-serif"],
                        serif: ["Playfair Display", "serif"],
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
            background: rgba(255, 255, 255, 0.95);
            @apply backdrop-blur-md;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .dark .glass-card {
            background: rgba(30, 20, 20, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            filter: drop-shadow(0 4px 6px rgba(139, 0, 0, 0.15));
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 transition-colors duration-300">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 px-6 py-4 flex justify-center">
        <div class="glass-card px-8 py-3 rounded-full flex items-center gap-8 shadow-sm text-sm font-semibold">
            <div class="flex items-center gap-2">
                <div
                    class="w-6 h-6 flex items-center justify-center rounded-full bg-primary text-white font-bold text-xs logo-shadow">
                    R
                </div>
                <span class="font-bold tracking-tight text-primary">RIBATO</span>
            </div>
            <a class="text-primary hover:opacity-80 transition-opacity" href="#">Home</a>
            <a class="text-primary hover:opacity-80 transition-opacity" href="#features">Features</a>
            <a class="text-primary hover:opacity-80 transition-opacity" href="#partners">Partners</a>
            <a class="text-primary hover:opacity-80 transition-opacity" href="#terms-modal">Terms</a>
            <a class="text-primary hover:opacity-80 transition-opacity" href="#solutions">Article</a>
            <a class="text-primary hover:opacity-80 transition-opacity" href="#contact-modal">Contact</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-24 px-6 min-h-[85vh] flex items-center bg-cover bg-center"
        style="background-image: linear-gradient(rgba(31, 17, 17, 0.45), rgba(74, 14, 14, 0.75)), url('/images/hero_skyline.png');">
        <div class="max-w-7xl mx-auto w-full text-center text-white z-10">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 max-w-4xl mx-auto leading-tight">
                Transform Your Global Financial Operations
            </h1>
            <p class="text-lg md:text-xl text-slate-200 mb-10 max-w-2xl mx-auto leading-relaxed">
                Transforming payments to make them faster, more secure, and more affordable for businesses of all sizes.
            </p>
            <div>
                <a class="inline-block bg-primary hover:bg-red-800 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-xl shadow-primary/30"
                    href="#contact-modal">
                    Sign up
                </a>
            </div>
        </div>
    </section>

    <!-- Stay Simple Section -->
    <section class="py-16 px-6 bg-white dark:bg-slate-900/20 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-16 text-slate-800 dark:text-white">
            Stay <span class="text-primary">Simple</span>, Reach <span class="text-primary">Global</span>
        </h2>

        <!-- 3 columns Features -->
        <div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-12 text-center" id="features">
            <!-- Column 1 -->
            <div class="flex flex-col items-center space-y-4">
                <span class="material-icons-round text-primary text-5xl">payments</span>
                <h3 class="text-xl font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Pay In</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-sm text-primary">Multi Currency Deposit</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xs leading-relaxed">Hold and
                            exchange 40+ currencies at real-time interbank rates.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-primary">Low Currency Deposit</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xs leading-relaxed">Start with
                            small amounts with minimal fees.</p>
                    </div>
                </div>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col items-center space-y-4">
                <span class="material-icons-round text-primary text-5xl">account_balance_wallet</span>
                <h3 class="text-xl font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Digital
                    Wallet</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-sm text-primary">Instant transaction</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xs leading-relaxed">Send and
                            receive payments instantly across borders.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-primary">Competitive Rates</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xs leading-relaxed">Save money
                            with our industry-leading exchange rates.</p>
                    </div>
                </div>
            </div>

            <!-- Column 3 -->
            <div class="flex flex-col items-center space-y-4">
                <span class="material-icons-round text-primary text-5xl">send</span>
                <h3 class="text-xl font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Pay Out</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-sm text-primary">Zero Setup Fee</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xs leading-relaxed">No hidden
                            charges or onboarding fees.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-primary">Local Payments</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1 max-w-xs leading-relaxed">Direct
                            deposits to local banks worldwide.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Speed, Security, and Simplicity Section -->
    <section class="py-24 px-6 bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-white">
                    Speed, Security, and Simplicity
                </h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                    Our platform is engineered for high-performance financial transactions. We combine state-of-the-art
                    encryption with an intuitive design to deliver payments at unmatched speeds.
                </p>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Whether you are paying global vendors or collecting customer invoices, our system automates the
                    complexity so you can focus entirely on growing your business operations.
                </p>
            </div>
            <div class="flex justify-center">
                <img src="/images/illustration_workspace.png" alt="Workspace Illustration"
                    class="max-w-full h-auto rounded-3xl" />
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-24 px-6 bg-white dark:bg-slate-950">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-16 text-slate-900 dark:text-white">
                How It <span class="text-primary">Works</span> - 4 Simple Steps
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div
                    class="p-8 border border-primary-dark/80 bg-slate-50/30 dark:bg-slate-900/10 flex flex-col items-center text-center space-y-4">
                    <span class="text-slate-900 dark:text-white font-bold text-lg">Step 1</span>
                    <div
                        class="w-16 h-16 flex items-center justify-center text-primary border border-primary/20 rounded-xl bg-white dark:bg-slate-800">
                        <span class="material-icons-round text-3xl">touch_app</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-[200px]">Create your
                        personal or business account in just a few minutes.</p>
                </div>

                <!-- Step 2 -->
                <div
                    class="p-8 border border-primary-dark/80 bg-slate-50/30 dark:bg-slate-900/10 flex flex-col items-center text-center space-y-4">
                    <span class="text-slate-900 dark:text-white font-bold text-lg">Step 2</span>
                    <div
                        class="w-16 h-16 flex items-center justify-center text-primary border border-primary/20 rounded-xl bg-white dark:bg-slate-800">
                        <span class="material-icons-round text-3xl">person</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-[200px]">Securely verify
                        your identity to enable all payment features.</p>
                </div>

                <!-- Step 3 -->
                <div
                    class="p-8 border border-primary-dark/80 bg-slate-50/30 dark:bg-slate-900/10 flex flex-col items-center text-center space-y-4">
                    <span class="text-slate-900 dark:text-white font-bold text-lg">Step 3</span>
                    <div
                        class="w-16 h-16 flex items-center justify-center text-primary border border-primary/20 rounded-xl bg-white dark:bg-slate-800">
                        <span class="material-icons-round text-3xl">verified_user</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-[200px]">Deposit funds
                        securely using bank transfer or digital channels.</p>
                </div>

                <!-- Step 4 -->
                <div
                    class="p-8 border border-primary-dark/80 bg-slate-50/30 dark:bg-slate-900/10 flex flex-col items-center text-center space-y-4">
                    <span class="text-slate-900 dark:text-white font-bold text-lg">Step 4</span>
                    <div
                        class="w-16 h-16 flex items-center justify-center text-primary border border-primary/20 rounded-xl bg-white dark:bg-slate-800">
                        <span class="material-icons-round text-3xl">done_all</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-[200px]">Start
                        transferring globally with minimal fees and complete safety.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section class="py-24 px-6 bg-primary-dark text-white" id="solutions">
        <div class="max-w-4xl mx-auto text-center space-y-6">
            <h2 class="text-4xl md:text-5xl font-bold">Solutions</h2>
            <h3 class="text-xl font-medium text-red-300">Exchange/Payments</h3>
            <p class="text-lg text-red-100/80 leading-relaxed">
                All Cost-effective payments services and solution for your requirements. From peer-to-peer transfers to
                merchant payouts, our solutions are customized to meet your standard and reliability.
            </p>
        </div>
    </section>

    <!-- Industries Covered Section -->
    <section class="py-24 px-6 bg-white dark:bg-slate-900/20">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-4xl font-bold mb-6 text-slate-900 dark:text-white font-serif">
                    Industries<br />Covered
                </h2>
                <p class="text-slate-500 dark:text-slate-400 leading-relaxed max-w-md">
                    We cater to diverse sectors, offering customized digital wallet and payment solutions tailored to
                    meet compliance and high volume needs.
                </p>
            </div>
            <div class="space-y-6 font-serif text-3xl md:text-4xl text-slate-800 dark:text-slate-200">
                <div
                    class="border-b border-slate-100 dark:border-slate-800 pb-3 hover:text-primary transition-colors cursor-default">
                    Trading</div>
                <div
                    class="border-b border-slate-100 dark:border-slate-800 pb-3 hover:text-primary transition-colors cursor-default">
                    Importer/Exporter</div>
                <div
                    class="border-b border-slate-100 dark:border-slate-800 pb-3 hover:text-primary transition-colors cursor-default">
                    Wholesales</div>
                <div
                    class="border-b border-slate-100 dark:border-slate-800 pb-3 hover:text-primary transition-colors cursor-default">
                    Retails</div>
                <div
                    class="border-b border-slate-100 dark:border-slate-800 pb-3 hover:text-primary transition-colors cursor-default">
                    Education</div>
                <div class="hover:text-primary transition-colors cursor-default">And More...</div>
            </div>
        </div>
    </section>

    <!-- Partnership Section -->
    <section class="py-24 px-6 bg-slate-50 dark:bg-slate-900/50" id="partners">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-5 space-y-6">
                <h2 class="text-3xl md:text-4xl font-bold font-serif text-slate-900 dark:text-white">Partnership</h2>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Your payment solution partner. From cross-border to local payout, our network is designed to help
                    you expand your footprint globally.
                </p>
            </div>
            <div class="lg:col-span-7 grid grid-cols-2 gap-4">
                <!-- Left image takes 1 full column -->
                <div>
                    <img src="/images/partner_building.png" alt="Office Building"
                        class="rounded-xl w-full h-[400px] object-cover" />
                </div>
                <!-- Right column has two smaller stacked images -->
                <div class="flex flex-col gap-4">
                    <img src="/images/partner_desk.png" alt="Laptop Working"
                        class="rounded-xl w-full h-[192px] object-cover" />
                    <img src="/images/partner_meeting.png" alt="Business Meeting"
                        class="rounded-xl w-full h-[192px] object-cover" />
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-primary-dark text-white py-20 px-6">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-start">
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full border-2 border-white flex items-center justify-center font-bold text-2xl">
                        R
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight">RIBATO</span>
                </div>
                <p class="text-red-200/80 text-lg max-w-sm">
                    Your Payment Solution Partner.
                </p>
                <p class="text-xs text-red-300/60">
                    Reg Number: LLC-12049
                </p>
            </div>

            <div class="space-y-6 md:justify-self-end text-sm">
                <div class="flex items-start gap-3 text-red-200">
                    <span class="material-icons-round mt-0.5 text-primary">place</span>
                    <div>
                        <p class="font-bold text-white">Place</p>
                        <p class="text-red-100/80 mt-1">732 S 6th ST, STE N, Las Vegas, NV 89101, USA</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-red-200">
                    <span class="material-icons-round mt-0.5 text-primary">phone</span>
                    <div>
                        <p class="font-bold text-white">Phone</p>
                        <a class="text-red-100/80 hover:text-white mt-1 block" href="tel:+17026098830">+1 (702)
                            577-4256</a>
                    </div>
                </div>
                <div class="flex items-start gap-3 text-red-200">
                    <span class="material-icons-round mt-0.5 text-primary">email</span>
                    <div>
                        <p class="font-bold text-white">Email</p>
                        <a class="text-red-100/80 hover:text-white mt-1 block"
                            href="mailto:info@ribato.com">info@ribato.com</a>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="max-w-7xl mx-auto mt-16 pt-8 border-t border-red-800/40 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-red-300/60">
            <p>© 2023 RIBATO LLC. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="#privacy-modal" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#terms-modal" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden bg-slate-900/40 backdrop-blur-md"
        id="contact-modal">
        <a class="absolute inset-0 cursor-default" href="#"></a>
        <div
            class="modal-content relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
            <a class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                href="#">
                <span class="material-icons-round">close</span>
            </a>
            <div class="flex justify-center mb-8">
                <div
                    class="w-14 h-14 rounded-full bg-primary flex items-center justify-center text-white font-bold text-2xl logo-shadow">
                    R
                </div>
            </div>
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold mb-3">Get in Touch with RIBATO</h2>
                <p class="text-slate-500 dark:text-slate-400">Tell us how we can help your business grow.</p>
            </div>
            <form class="space-y-6" id="contact-form">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">Full
                            Name</label>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm"
                            name="full_name" placeholder="John Doe" required type="text" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">Business
                            Email</label>
                        <input
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm"
                            name="email" placeholder="john@company.com" required type="email" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">Company
                        Name</label>
                    <input
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm"
                        name="company" placeholder="Your Business" type="text" />
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 ml-1 text-slate-700 dark:text-slate-300">How can we
                        help?</label>
                    <textarea
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 focus:ring-primary focus:border-primary transition-all text-sm resize-none"
                        name="message" placeholder="Briefly describe your requirements..." required rows="4"></textarea>
                </div>
                <div id="form-feedback" class="hidden p-4 rounded-2xl text-sm font-bold"></div>
                <button
                    class="w-full bg-primary hover:bg-red-800 text-white py-5 rounded-2xl font-bold text-lg shadow-xl shadow-primary/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                    id="submit-btn" type="submit">
                    Send Message
                </button>
            </form>
        </div>
    </div>

    <div class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden bg-slate-900/40 backdrop-blur-md"
        id="privacy-modal">
        <a class="absolute inset-0 cursor-default" href="#"></a>
        <div
            class="modal-content relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
            <a class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors z-10"
                href="#">
                <span class="material-icons-round">close</span>
            </a>

            <div class="prose prose-slate dark:prose-invert max-w-none">
                <header class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-6">
                    <h1 class="text-3xl font-extrabold mb-2 text-slate-900 dark:text-white">RIBATO LLC — Privacy Policy
                        (Official)</h1>
                    <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                        <p><strong>Last Updated:</strong> <span>November 13, 2025</span></p>
                        <p><strong>Effective Date:</strong> <span>November 13, 2025</span></p>
                    </div>
                </header>

                <main class="content">
                    <section class="intro">
                        <h2>1. Introduction</h2>
                        <p>
                            RIBATO LLC ("we", "our", "the App", "the Service") is committed to protecting your privacy
                            and personal data. This Privacy Policy ("Policy") explains how we collect, use, store,
                            share, and protect user information.
                        </p>
                        <p
                            class="font-bold p-4 bg-slate-50 dark:bg-slate-800 rounded-xl my-4 border border-slate-100 dark:border-slate-700">
                            By using our Service, you confirm that you have read and agree to this Policy.
                        </p>
                    </section>

                    <section id="scope">
                        <h2>2. Scope of Application</h2>
                        <p>This Policy applies to our application, our website, customer service, identity verification
                            systems, and related payment integrations.</p>
                    </section>

                    <section id="dataCollection">
                        <h2>3. Data We Collect</h2>
                        <h3>3.1 Data You Provide Directly</h3>
                        <ul>
                            <li>Personal information: full name, date of birth, nationality.</li>
                            <li>KYC documents: ID card/Citizen ID/Passport, selfie photos, biometrics.</li>
                            <li>Contact information: email, phone number.</li>
                            <li>Linked bank accounts.</li>
                        </ul>
                        <h3>3.2 Automatically Collected Data</h3>
                        <ul>
                            <li>Device information, IP address, geographical region.</li>
                            <li>Application usage logs.</li>
                        </ul>
                    </section>

                    <section id="purpose">
                        <h2>4. Purpose of Data Processing</h2>
                        <p>We use your data to operate services, verify identities (KYC/AML), detect fraud, support
                            customers, and comply with international regulations.</p>
                    </section>

                    <section id="contact">
                        <h2>5. Contact</h2>
                        <div class="contact-info">
                            <p><strong>RIBATO LLC Support Team</strong></p>
                            <p><strong>Email:</strong> <a href="mailto:info@ribato.com">info@ribato.com</a></p>
                            <p><strong>Address:</strong> 732 S 6th ST, STE N, Las Vegas, NV 89101, USA</p>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden bg-slate-900/40 backdrop-blur-md"
        id="terms-modal">
        <a class="absolute inset-0 cursor-default" href="#"></a>
        <div
            class="modal-content relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
            <a class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors z-10"
                href="#">
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
                        <p>By accessing or using the RIBATO LLC application and services ("Service"), you agree to be
                            bound by these Terms of Service. If you disagree with any part, you may not access the
                            Service.</p>
                    </section>

                    <section>
                        <h2>2. User Accounts</h2>
                        <p>When you create an account, you must provide information that is accurate, complete, and
                            current. You are responsible for safeguarding your access credentials.</p>
                    </section>

                    <section>
                        <h2>3. Contact Us</h2>
                        <p>If you have any questions about these Terms, please contact us at <a
                                href="mailto:info@ribato.com">info@ribato.com</a>.</p>
                    </section>
                </main>
            </div>
        </div>
    </div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contact-form');
        const submitBtn = document.getElementById('submit-btn');
        const feedback = document.getElementById('form-feedback');
        const originalBtnText = submitBtn.innerHTML;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            feedback.classList.add('hidden');
            feedback.className = 'hidden p-4 rounded-2xl text-sm font-bold';

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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });
</script>

</html>