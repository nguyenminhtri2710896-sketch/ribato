/**
 * Razorepay v2 Multi-Language Translation System
 * Supported locales:
 * - 'vi': Tiếng Việt (Việt Nam 🇻🇳)
 * - 'ms': Bahasa Melayu (Malaysia 🇲🇾)
 * - 'sg': English (Singapore 🇸🇬)
 * - 'us': English (United States 🇺🇸)
 * - 'in': English (India 🇮🇳)
 */

const RAZORPAY_TRANSLATIONS = {
    // ==================== VIETNAMESE (🇻🇳) ====================
    vi: {
        meta_label: "Việt Nam",
        flag: "🇻🇳",
        currency: "₫",
        
        // Header & Nav
        announcement_badge: "MỚI",
        announcement_text: "Cổng thanh toán thế hệ mới & AI Payments đã sẵn sàng! Trải nghiệm thanh toán nhanh gấp 5 lần.",
        announcement_link: "Khám phá ngay →",
        nav_payments: "Thanh toán",
        nav_banking: "Ngân hàng+",
        nav_ai: "Giải pháp AI Native",
        nav_nocode: "Bộ công cụ No-Code",
        nav_devs: "Lập trình viên",
        nav_pricing: "Bảng giá",
        nav_contact: "Liên hệ tư vấn",
        nav_pg: "Cổng thanh toán",
        nav_pg_desc: "100+ phương thức thanh toán, thẻ & mã QR",
        nav_qr: "Mã QR & Thu tiền thông minh",
        nav_qr_desc: "Mã QR tức thì với webhook thời gian thực",
        nav_links: "Link & Trang thanh toán",
        nav_links_desc: "Link thanh toán không cần lập trình",
        nav_ca: "Tài khoản Doanh nghiệp",
        nav_ca_desc: "Ngân hàng số tự động cho doanh nghiệp",
        nav_payouts: "Chi trả đối tác tức thì",
        nav_payouts_desc: "Chi trả ngân hàng 24/7 tự động",
        nav_payroll: "Tự động hóa Bảng lương",
        nav_payroll_desc: "Chuyển lương & quyết toán thuế tự động",
        
        // Hero Fold
        hero_badge: "Hạ tầng thanh toán uy tín hàng đầu",
        hero_title_1: "Giải pháp Thanh toán Nâng cao",
        hero_title_2: "dành cho doanh nghiệp dẫn đầu",
        hero_desc: "Chấp nhận thanh toán từ khách hàng toàn cầu, tự động hóa chi trả nhà cung cấp, quản lý dòng vốn liền mạch và bứt phá tăng trưởng với nền tảng tài chính thông minh.",
        hero_signup: "Bắt đầu ngay",
        hero_trust_modes: "100+ Phương thức thanh toán",
        hero_trust_settle: "Quyết toán tức thì",
        hero_trust_fees: "Miễn phí thiết lập",
        hero_card_received: "Đã nhận thanh toán",
        hero_card_sub: "qua Chuyển khoản QR • Tức thì",
        hero_uptime: "Hoạt động 99.99%",
        trust_partners: "Hỗ trợ thanh toán cho hàng triệu doanh nghiệp & kỳ lân công nghệ",
        
        // Startup 26 Banner
        startup26_badge: "HỘI NGHỊ THƯỢNG ĐỈNH FINTECH",
        startup26_title_1: "STARTUP 26",
        startup26_title_2: "Tương lai của Thương mại Tự động hóa",
        startup26_desc: "Cùng hơn 10.000 nhà sáng lập, lập trình viên và chuyên gia khám phá thanh toán AI, định tuyến thông minh và kiến trúc tài chính thế hệ mới.",
        startup26_cta: "Đăng ký tham dự",
        
        // Category Tabs
        main_sec_title_1: "Nền tảng tài chính toàn diện",
        main_sec_title_2: "bạn luôn tìm kiếm",
        tab_ai: "Xây dựng AI Native",
        tab_accept: "Nhận thanh toán",
        tab_payouts: "Chi trả tiền",
        tab_banking: "Ngân hàng số",
        tab_payroll: "Bảng lương",
        tab_credit: "Tín dụng & Vốn vay",
        tab_cta: "Bắt đầu ngay",
        
        // Products - AI Native
        card_ai_agentic_title: "Thanh toán Agentic AI",
        card_ai_agentic_desc: "Biến mọi đoạn chat thành luồng thanh toán liền mạch với AI-native.",
        card_ai_studio_title: "Agent Studio",
        card_ai_studio_desc: "Ủy quyền quy trình vận hành tài chính cho các trợ lý AI tự động hoàn thành nhiệm vụ.",
        card_ai_builders_title: "Thanh toán cho AI Builders",
        card_ai_builders_desc: "Node thanh toán 1-click tích hợp cho luồng xử lý n8n, Replit và Vercel.",
        card_ai_routing_title: "Định tuyến thông minh AI",
        card_ai_routing_desc: "Tự động tối ưu hóa tuyến thanh toán đạt tỷ lệ giao dịch thành công 99.9%.",
        
        // Products - Accept Payments
        card_pg_title: "Cổng thanh toán",
        card_pg_desc: "Chấp nhận 100+ phương thức thanh toán với tỷ lệ chuyển đổi cao nhất thị trường.",
        card_pl_title: "Link thanh toán",
        card_pl_desc: "Chia sẻ link qua SMS, Zalo, WhatsApp, Email và nhận tiền tức thì.",
        card_pp_title: "Trang thanh toán",
        card_pp_desc: "Tạo trang thanh toán mang thương hiệu riêng mà không cần lập trình.",
        card_sub_title: "Thanh toán định kỳ",
        card_sub_desc: "Tự động hóa thu phí định kỳ qua thẻ, ví điện tử và trích nợ tự động.",
        
        // Products - Make Payouts
        card_bulk_title: "Chi trả tự động qua API & Hàng loạt",
        card_bulk_desc: "Chi trả tức thì 24/7 qua chuyển khoản liên ngân hàng nhanh và ví điện tử cả ngày lễ.",
        card_s2p_title: "Quản trị thanh toán NCC",
        card_s2p_desc: "Tự động hóa đối soát hóa đơn nhà cung cấp và phê duyệt chi nhiều cấp.",
        card_plinks_title: "Link chi trả nhận tiền",
        card_plinks_desc: "Hoàn tiền, trả thưởng & chiết khấu trực tiếp tới khách hàng chỉ bằng 1 đường link.",
        card_multibank_title: "Định tuyến đa ngân hàng",
        card_multibank_desc: "Tự động chuyển tiếp thông minh giữa các ngân hàng đối tác đảm bảo không gián đoạn.",
        
        // Products - Business Banking
        card_ca_title: "Tài khoản thanh toán Doanh nghiệp",
        card_ca_desc: "Trung tâm ngân hàng số tự động hóa thiết kế cho doanh nghiệp hiện đại & startup.",
        card_cards_title: "Thẻ tín dụng Doanh nghiệp",
        card_cards_desc: "Thẻ không cần thế chấp với hạn mức cao và hoàn tiền khi chi tiêu SaaS/quảng cáo.",
        card_tax_title: "Nộp thuế điện tử",
        card_tax_desc: "Nộp thuế thu nhập, thuế GTGT và thuế doanh nghiệp tự động chỉ với 1 cú nhấp.",
        card_acc_title: "Tích hợp phần mềm kế toán",
        card_acc_desc: "Đồng bộ hóa 2 chiều thời gian thực với MISA, Zoho Books, QuickBooks, Xero.",
        
        // Products - Automate Payroll
        card_pay_title: "Tính & Trả lương tự động",
        card_pay_desc: "Chuyển lương cho toàn bộ nhân sự chỉ với 3 thao tác kèm bảng tính thuế tự động.",
        card_comp_title: "Tuân thủ Bảo hiểm & Thuế",
        card_comp_desc: "Tự động khấu trừ bảo hiểm xã hội, thuế TNCN và xuất tờ khai chuẩn quy định.",
        card_emp_title: "Cổng tự phục vụ cho nhân viên",
        card_emp_desc: "Ứng dụng di động cho nhân viên tra cứu phiếu lương, ngày phép và chứng từ thuế.",
        
        // Products - Credit & Loans
        card_wc_title: "Vốn lưu động tức thì",
        card_wc_desc: "Hạn mức tín dụng không thế chấp phê duyệt nhanh trong 2 giờ hỗ trợ quay vòng vốn.",
        card_line_title: "Hạn mức tín dụng Doanh nghiệp",
        card_line_desc: "Hạn mức tín dụng tuần hoàn linh hoạt, hoàn trả tự động theo doanh thu hàng ngày.",
        
        // Merchant Showcase
        case_tag: "Bán lẻ & Thương mại điện tử",
        case_quote: '"Razorepay đã giúp chúng tôi nâng tỷ lệ thanh toán thành công lên 94.2%."',
        case_desc: "Với mã QR động và công nghệ định tuyến giao dịch thông minh, hàng ngàn thương hiệu bán lẻ mang đến trải nghiệm thanh toán không độ trễ cho hàng triệu khách hàng.",
        case_btn: "Xem câu chuyện thành công",
        
        // Dev Section
        dev_tag: "ĐỘ TIN CẬY • QUY MÔ • BẢO MẬT • TIÊN TIẾN CHO LẬP TRÌNH VIÊN",
        dev_title_1: "Razorepay được kiến tạo",
        dev_title_2: "dành cho lập trình viên, bởi lập trình viên",
        dev_desc: "Khám phá API REST chuẩn mực, webhook tự động gửi lại và các bộ SDK tối ưu giúp bạn chuyển từ thử nghiệm sang vận hành thực tế trong vài phút.",
        dev_bullet_1: "API RESTful JSON chuẩn mực hỗ trợ Idempotency",
        dev_bullet_2: "Bộ SDK hoàn chỉnh cho PHP, Node.js, Python, Java, Go, React, Flutter",
        dev_bullet_3: "Bộ sưu tập Postman và trình giả lập Webhook trực quan",
        dev_btn_keys: "Lấy API Keys",
        dev_btn_docs: "Xem tài liệu API",
        
        // No Code Section
        nocode_sub: "<không cần viết code?>",
        nocode_title_1: "Bạn không phải lập trình viên?",
        nocode_title_2: "Bộ công cụ No-Code của chúng tôi sẽ hỗ trợ bạn",
        nocode_1_title: "Link thanh toán",
        nocode_1_desc: "Nhận tiền ngay tức thì: Chia sẻ link thanh toán qua Email, tin nhắn hoặc mạng xã hội.",
        nocode_2_title: "Trang thanh toán",
        nocode_2_desc: "Nhận thanh toán online không cần website với trang bán hàng mang thương hiệu riêng.",
        nocode_3_title: "Nút thanh toán",
        nocode_3_desc: "Dễ dàng nhúng nút Thanh Toán Ngay vào bất kỳ trang web nào mà không cần biết code.",
        
        // Stats & Testimonial
        stat_scale_tag: "Quy mô Doanh nghiệp",
        stat_scale_title: "Xử lý hơn 10.000+ Tỷ VNĐ giao dịch mỗi tháng với độ sẵn sàng hệ thống 99.99%.",
        stat_scale_desc: "Kiến trúc ngân hàng phân tán đa vùng xử lý các đợt flash sale trên 1.000 giao dịch/giây mượt mà.",
        stat_scale_link: "Khám phá hạ tầng →",
        testi_tag: "Đánh giá xác thực",
        testi_quote: '"Các API và hệ thống chi trả tự động của Razorepay giúp đội ngũ kỹ thuật của chúng tôi tập trung phát triển sản phẩm cốt lõi mà không phải tự xây dựng hạ tầng ngân hàng."',
        
        // Founders
        founders_title_1: "Đồng hành tăng trưởng",
        founders_title_2: "cùng những câu chuyện thực tế",
        founders_count: "10.000.000+ doanh nghiệp tin cậy",
        
        // FAQs
        faq_title: "Câu hỏi thường gặp",
        faq_desc: "Giải đáp các thắc mắc phổ biến về kích hoạt tài khoản, biểu phí giao dịch, phương thức thanh toán và bảo mật.",
        faq_support: "Bạn còn câu hỏi khác? Liên hệ hỗ trợ →",
        faq_q1: "Razorepay là gì và hoạt động như thế nào?",
        faq_a1: "Razorepay là giải pháp thanh toán toàn diện cho phép doanh nghiệp chấp nhận, xử lý và chi trả thanh toán. Chỉ với một lần tích hợp duy nhất, bạn có thể kết nối Thẻ quốc tế (Visa/Mastercard), mã QR ngân hàng, ví điện tử và chuyển khoản trực tuyến.",
        faq_q2: "Biểu phí giao dịch và chi phí sử dụng là bao nhiêu?",
        faq_a2: "Razorepay áp dụng mức phí cạnh tranh và minh bạch trên mỗi giao dịch thành công. Không phí thiết lập ban đầu, không phí duy trì hàng năm, và có chính sách chiết khấu riêng cho doanh nghiệp có sản lượng lớn.",
        faq_q3: "Các phương thức thanh toán nào được hỗ trợ?",
        faq_a3: "Chúng tôi hỗ trợ 100+ phương thức thanh toán bao gồm thẻ quốc tế (Visa, MasterCard, JCB, Amex), thẻ nội địa, quét mã QR chuyển khoản và các ví điện tử phổ biến.",
        faq_q4: "Thời gian đăng ký và kích hoạt tài khoản mất bao lâu?",
        faq_a4: "Bạn có thể đăng ký tài khoản trong 2 phút và bắt đầu thử nghiệm ngay trong môi trường Sandbox. Quy trình xác thực định danh số (eKYC) 100% trực tuyến và hoàn tất trong vòng 24 giờ.",
        faq_q5: "Nền tảng Razorepay có an toàn và đạt chứng nhận bảo mật không?",
        faq_a5: "Có. Razorepay đạt chứng nhận bảo mật cao nhất chuẩn quốc tế PCI-DSS Level 1, chứng chỉ ISO/IEC 27001 và mã hóa dữ liệu 256-bit SSL chuẩn ngân hàng.",
        
        // Final CTA
        cta_title: "Tăng tốc doanh nghiệp của bạn cùng Razorepay",
        cta_desc: "Đăng ký chỉ trong 2 phút và bắt đầu chấp nhận thanh toán tức thì với tỷ lệ chuyển đổi cao nhất thị trường.",
        cta_signup: "Đăng ký ngay",
        cta_contact: "Liên hệ tư vấn",
        
        // Footer & Modal
        footer_brand_desc: "Razorepay là nền tảng dịch vụ tài chính toàn diện hàng đầu giúp doanh nghiệp nhận thanh toán, tự động hóa ngân hàng, quản lý bảng lương và tiếp cận tín dụng doanh nghiệp.",
        footer_col_accept: "Nhận thanh toán",
        footer_col_banking: "Ngân hàng & Chi trả",
        footer_col_devs: "Lập trình viên",
        footer_col_company: "Doanh nghiệp & Pháp lý",
        contact_modal_title: "Trao đổi cùng Đội ngũ Giải pháp",
        contact_modal_desc: "Khám phá bảng giá ưu đãi, kiến trúc doanh nghiệp và giải pháp tích hợp tùy chỉnh."
    },

    // ==================== MALAYSIA (🇲🇾 Bahasa Melayu) ====================
    ms: {
        meta_label: "Malaysia",
        flag: "🇲🇾",
        currency: "RM",
        
        announcement_badge: "BAHARU",
        announcement_text: "Razorepay Turbo & Pembayaran Agentic AI kini tersedia! Alami pembayaran 5X lebih pantas.",
        announcement_link: "Terokai sekarang →",
        nav_payments: "Pembayaran",
        nav_banking: "Perbankan+",
        nav_ai: "Bina AI Native",
        nav_nocode: "Sut Tanpa Kod",
        nav_devs: "Pembangun",
        nav_pricing: "Harga",
        nav_contact: "Hubungi Jualan",
        nav_pg: "Gerbang Pembayaran",
        nav_pg_desc: "100+ mod pembayaran, Kad & FPX",
        nav_qr: "Kod QR & Kutipan Pintar",
        nav_qr_desc: "Kod QR segera dengan webhook masa nyata",
        nav_links: "Pautan & Halaman Pembayaran",
        nav_links_desc: "Pautan pembayaran tanpa kod",
        nav_ca: "Akaun Semasa Perniagaan",
        nav_ca_desc: "Perbankan automatik untuk perniagaan",
        nav_payouts: "Pembayaran Vendor Segera",
        nav_payouts_desc: "Pengeluaran 24x7 melalui pindahan bank",
        nav_payroll: "Pengurusan Gaji Automatik",
        nav_payroll_desc: "Pemindahan gaji & pemfailan cukai",
        
        hero_badge: "Gerbang Pembayaran Paling Dipercayai",
        hero_title_1: "Penyelesaian Pembayaran Termaju",
        hero_title_2: "untuk Perniagaan Terkemuka",
        hero_desc: "Terima pembayaran dari seluruh dunia, automatikkan pembayaran vendor, urus modal dengan lancar dan pacu pertumbuhan dengan perbankan terbina dalam.",
        hero_signup: "Daftar Sekarang",
        hero_trust_modes: "100+ Mod Pembayaran",
        hero_trust_settle: "Penyelesaian Segera",
        hero_trust_fees: "Tiada Yuran Persediaan",
        hero_card_received: "Pembayaran Diterima",
        hero_card_sub: "melalui AutoPay • Segera",
        hero_uptime: "99.99% Masa Beroperasi",
        trust_partners: "Memperkasakan pembayaran untuk syarikat pemula & unicorn terkemuka",
        
        startup26_badge: "SIDANG KEMUNCAK FINTECH TAHUNAN",
        startup26_title_1: "STARTUP 26",
        startup26_title_2: "Masa Depan Perdagangan Autonomi",
        startup26_desc: "Sertai 10,000+ pengasas dan pembangun untuk meneroka pembayaran AI dan penghalaan pintar.",
        startup26_cta: "Daftar untuk Ucapan Dasar",
        
        main_sec_title_1: "Platform kewangan serba lengkap",
        main_sec_title_2: "yang anda cari",
        tab_ai: "Bina AI Native",
        tab_accept: "Terima Pembayaran",
        tab_payouts: "Buat Pembayaran",
        tab_banking: "Perbankan Perniagaan",
        tab_payroll: "Automatikkan Gaji",
        tab_credit: "Kredit & Pinjaman",
        tab_cta: "Mula Sekarang",
        
        card_ai_agentic_title: "Pembayaran Agentic AI",
        card_ai_agentic_desc: "Tukarkan setiap sembang menjadi pembayaran dengan aliran AI-native.",
        card_ai_studio_title: "Agent Studio",
        card_ai_studio_desc: "Serahkan tugas operasi kepada ejen AI yang menyelesaikan tugasan secara autonomi.",
        card_ai_builders_title: "Pembayaran untuk Pembangun AI",
        card_ai_builders_desc: "Nod pembayaran 1-klik untuk n8n, Replit dan aliran kerja Vercel.",
        card_ai_routing_title: "Enjin Penghalaan AI",
        card_ai_routing_desc: "Pengoptimuman laluan transaksi automatik untuk kadar kejayaan 99.9%.",
        
        card_pg_title: "Gerbang Pembayaran",
        card_pg_desc: "Terima 100+ kaedah pembayaran termasuk FPX, Kad dan DuitNow.",
        card_pl_title: "Pautan Pembayaran",
        card_pl_desc: "Kongsi pautan melalui SMS, WhatsApp, Emel dan terima bayaran segera.",
        card_pp_title: "Halaman Pembayaran",
        card_pp_desc: "Lancarkan halaman pembayaran berjenama tanpa menulis sebarang kod.",
        card_sub_title: "Langganan Berkala",
        card_sub_desc: "Automatikkan bil berulang dengan kad dan debit automatik.",
        
        card_bulk_title: "Pembayaran Pukal & API",
        card_bulk_desc: "Pembayaran segera 24x7 melalui pindahan bank segera walaupun cuti umum.",
        card_s2p_title: "Pengurusan Invois Vendor",
        card_s2p_desc: "Automatikkan pengesahan invois vendor dan kelulusan pembayaran.",
        card_plinks_title: "Pautan Pengeluaran Wang",
        card_plinks_desc: "Salurkan bayaran balik, pulangan tunai & ganjaran melalui pautan.",
        card_multibank_title: "Penghalaan Pelbagai Bank",
        card_multibank_desc: "Penghalaan masa beroperasi dinamik merentasi rakan kongsi perbankan.",
        
        card_ca_title: "Akaun Semasa Perniagaan",
        card_ca_desc: "Hab perbankan perniagaan automatik dibina untuk syarikat pemula & skala besar.",
        card_cards_title: "Kad Korporat",
        card_cards_desc: "Kad tanpa cagaran dengan had tinggi & pulangan tunai untuk perbelanjaan SaaS.",
        card_tax_title: "Pembayaran Cukai",
        card_tax_desc: "Pembayaran cukai perniagaan dan SST automatik 1-klik.",
        card_acc_title: "Integrasi Perakaunan",
        card_acc_desc: "Segerak masa nyata dengan Zoho Books, QuickBooks dan Xero.",
        
        card_pay_title: "Pengurusan Gaji Automatik",
        card_pay_desc: "Salurkan gaji staf dalam 3 klik dengan pengiraan KWSP/cukai automatik.",
        card_comp_title: "Pematuhan Berkanun (KWSP/Cukai)",
        card_comp_desc: "Pematuhan statutori automatik dengan jaminan pemfailan 100%.",
        card_emp_title: "Portal Layan Diri Pekerja",
        card_emp_desc: "Aplikasi mudah alih untuk pekerja melihat slip gaji dan permohonan cuti.",
        
        card_wc_title: "Modal Kerja Segera",
        card_wc_desc: "Talian kredit tanpa cagaran dengan kelulusan pantas dalam 2 jam.",
        card_line_title: "Talian Kredit Korporat",
        card_line_desc: "Talian kredit pusingan fleksibel dengan pembayaran balik mikro harian automatik.",
        
        case_tag: "Runcit & Terus-ke-Pengguna",
        case_quote: '"Razorepay membantu kami meningkatkan kadar kejayaan pembayaran kepada 94.2%."',
        case_desc: "Dengan kod QR dinamik dan pertukaran laluan pintar, beribu-ribu jenama runcit menyampaikan pengalaman pembayaran tanpa geseran.",
        case_btn: "Baca Kajian Kes",
        
        dev_tag: "KEBOLEHPERCAYAAN • SKALA • KESELAMATAN • UTAMAKAN PEMBANGUN",
        dev_title_1: "Razorepay dibina",
        dev_title_2: "untuk pembangun, oleh pembangun",
        dev_desc: "Terokai API REST yang boleh diramal, webhook dengan percubaan semula automatik dan SDK yang terbukti.",
        dev_bullet_1: "Titik akhir JSON RESTful dengan Idempotency",
        dev_bullet_2: "SDK untuk PHP, Node.js, Python, Java, Go, React, Flutter",
        dev_bullet_3: "Koleksi Postman dan simulator webhook langsung",
        dev_btn_keys: "Dapatkan Kunci API",
        dev_btn_docs: "Terokai Dokumen API",
        
        nocode_sub: "<tanpa sebarang kod?>",
        nocode_title_1: "Bukan seorang pembangun?",
        nocode_title_2: "Produk Tanpa Kod kami sedia membantu anda",
        nocode_1_title: "Pautan Pembayaran",
        nocode_1_desc: "Terima bayaran segera: Kongsi pautan melalui emel, teks, atau media sosial.",
        nocode_2_title: "Halaman Pembayaran",
        nocode_2_desc: "Terima bayaran tanpa pengekodan pada kedai berjenama khusus.",
        nocode_3_title: "Butang Pembayaran",
        nocode_3_desc: "Tambah butang Bayar Sekarang dengan mudah tanpa sebarang pengetahuan kod.",
        
        stat_scale_tag: "Skala Perusahaan",
        stat_scale_title: "Memproses berbilion dalam transaksi bulanan dengan ketersediaan sistem 99.99%.",
        stat_scale_desc: "Seni bina perbankan berbilang wilayah mengendalikan jualan kilat melebihi 1,000 transaksi/saat.",
        stat_scale_link: "Terokai Infrastruktur →",
        testi_tag: "Testimoni Disahkan",
        testi_quote: '"API dan pembayaran vendor automatik Razorepay membolehkan pasukan kejuruteraan kami menumpukan pada produk teras."',
        
        founders_title_1: "Memacu pertumbuhan",
        founders_title_2: "dengan kisah kejayaan sebenar",
        founders_count: "10,000,000+ perniagaan berpuas hati",
        
        faq_title: "Soalan Lazim",
        faq_desc: "Cari jawapan kepada soalan lazim mengenai pengaktifan akaun, harga, kaedah pembayaran dan keselamatan.",
        faq_support: "Ada soalan lain? Hubungi sokongan →",
        faq_q1: "Apakah itu Razorepay dan bagaimana ia berfungsi?",
        faq_a1: "Razorepay ialah platform pembayaran menyeluruh yang membolehkan perniagaan menerima, memproses dan menyalurkan pembayaran merentasi kad, perbankan dalam talian dan e-dompet.",
        faq_q2: "Berapakah caj transaksi dan harga?",
        faq_a2: "Razorepay mengenakan kadar yuran yang telus bagi setiap transaksi yang berjaya. Tiada yuran persediaan dan tiada caj penyelenggaraan tahunan.",
        faq_q3: "Apakah kaedah pembayaran yang disokong?",
        faq_a3: "Kami menyokong 100+ kaedah pembayaran termasuk kad Visa, MasterCard, perbankan FPX, DuitNow dan e-dompet popular.",
        faq_q4: "Berapa pantas proses pendaftaran dan pengaktifan?",
        faq_a4: "Anda boleh mendaftar dalam masa 2 minit dan mula menguji dalam kotak pasir dengan segera. Pengesahan eKYC digital diselesaikan dalam masa 24 jam.",
        faq_q5: "Adakah Razorepay selamat dan diperakui?",
        faq_a5: "Ya. Razorepay diperakui PCI-DSS Tahap 1, mematuhi ISO 27001 dan dilindungi oleh penyulitan SSL 256-bit gred perbankan.",
        
        cta_title: "Tingkatkan perniagaan anda dengan Razorepay",
        cta_desc: "Daftar dalam masa 2 minit dan mula menerima bayaran serta-merta dengan kadar penukaran tertinggi.",
        cta_signup: "Daftar Sekarang",
        cta_contact: "Hubungi Jualan",
        
        footer_brand_desc: "Razorepay ialah platform perkhidmatan kewangan terkemuka yang membolehkan perniagaan menerima pembayaran, mengautomasikan operasi perbankan dan menguruskan gaji.",
        footer_col_accept: "Terima Pembayaran",
        footer_col_banking: "Perbankan & Pembayaran",
        footer_col_devs: "Pembangun",
        footer_col_company: "Syarikat & Undang-undang",
        contact_modal_title: "Bercakap dengan Pasukan Penyelesaian Kami",
        contact_modal_desc: "Ketahui harga khusus, seni bina perusahaan dan pilihan integrasi yang disesuaikan."
    },

    // ==================== SINGAPORE (🇸🇬 English - SGD) ====================
    sg: {
        meta_label: "Singapore",
        flag: "🇸🇬",
        currency: "S$",
        
        announcement_badge: "NEW",
        announcement_text: "Razorepay Turbo & Agentic Payments are live! Experience 5X faster checkouts across Southeast Asia.",
        announcement_link: "Explore now →",
        nav_payments: "Payments",
        nav_banking: "Banking+",
        nav_ai: "Build AI Native",
        nav_nocode: "No-Code Suite",
        nav_devs: "Developers",
        nav_pricing: "Pricing",
        nav_contact: "Contact Sales",
        nav_pg: "Payment Gateway",
        nav_pg_desc: "Cards, PayNow, GrabPay & Global Currencies",
        nav_qr: "PayNow QR & Smart Collect",
        nav_qr_desc: "Instant dynamic QR with real-time webhooks",
        nav_links: "Payment Links & Pages",
        nav_links_desc: "Zero-code branded checkout links",
        nav_ca: "Business Account",
        nav_ca_desc: "Automated multi-currency business banking",
        nav_payouts: "Instant Global Payouts",
        nav_payouts_desc: "24x7 local & cross-border disbursements",
        nav_payroll: "Automated Payroll",
        nav_payroll_desc: "CPF & salary disbursements automated",
        
        hero_badge: "Southeast Asia's Leading Payment Solution",
        hero_title_1: "Advanced Payment Solutions",
        hero_title_2: "for High-Growth Global Enterprises",
        hero_desc: "Accept payments from customers worldwide, automate vendor payouts, seamlessly manage working capital, and scale faster with modern fintech infrastructure.",
        hero_signup: "Get Started Now",
        hero_trust_modes: "100+ Payment Modes",
        hero_trust_settle: "Instant Settlements",
        hero_trust_fees: "Zero Setup Fees",
        hero_card_received: "Payment Received",
        hero_card_sub: "via PayNow • Instant",
        hero_uptime: "99.99% Uptime",
        trust_partners: "Powering payments for leading startups, enterprises & unicorns",
        
        startup26_badge: "ANNUAL FINTECH SUMMIT",
        startup26_title_1: "STARTUP 26",
        startup26_title_2: "The Future of Autonomous Commerce",
        startup26_desc: "Join 10,000+ founders, developers & tech leaders to explore AI-native payments, intelligent routing, and next-gen financial architecture.",
        startup26_cta: "Register for Keynote",
        
        main_sec_title_1: "The all in one",
        main_sec_title_2: "finance platform you’ve been looking for",
        tab_ai: "Build AI Native",
        tab_accept: "Accept Payments",
        tab_payouts: "Make Payouts",
        tab_banking: "Business Banking",
        tab_payroll: "Automate Payroll",
        tab_credit: "Credit & Capital",
        tab_cta: "Get Started Now",
        
        card_ai_agentic_title: "Agentic Payments",
        card_ai_agentic_desc: "Turn every chat into a checkout with AI-native payment flows.",
        card_ai_studio_title: "Agent Studio",
        card_ai_studio_desc: "Delegate financial workflows to autonomous AI agents that get things done.",
        card_ai_builders_title: "Payments for AI Builders",
        card_ai_builders_desc: "One-click payment nodes for n8n, Replit, and Vercel workflows.",
        card_ai_routing_title: "AI Routing Engine",
        card_ai_routing_desc: "Autonomous route optimization for 99.9% gateway success rates.",
        
        card_pg_title: "Payment Gateway",
        card_pg_desc: "Accept PayNow, Cards, GrabPay, and 100+ global payment methods.",
        card_pl_title: "Payment Links",
        card_pl_desc: "Share links via WhatsApp, SMS, Email and get paid instantly.",
        card_pp_title: "Payment Pages",
        card_pp_desc: "Launch custom-branded payment pages without writing any code.",
        card_sub_title: "Subscriptions",
        card_sub_desc: "Automate recurring billing with cards and direct debit rails.",
        
        card_bulk_title: "API & Bulk Payouts",
        card_bulk_desc: "24x7 instant payouts via FAST, GIRO & cross-border rails.",
        card_s2p_title: "Source to Pay",
        card_s2p_desc: "Automate vendor invoice OCR extraction and multi-level payout approvals.",
        card_plinks_title: "Payout Links",
        card_plinks_desc: "Disburse refunds, cashbacks & rewards directly with an instant link.",
        card_multibank_title: "Multi-Bank Routing",
        card_multibank_desc: "Dynamic uptime routing across leading tier-1 banking partners.",
        
        card_ca_title: "Current Account",
        card_ca_desc: "Automated business banking hub built for modern startups & scale-ups.",
        card_cards_title: "Corporate Cards",
        card_cards_desc: "Collateral-free cards with high credit limits & cashback on cloud/SaaS.",
        card_tax_title: "Tax & Compliance",
        card_tax_desc: "Automated corporate tax filing, GST calculations, and compliance checks.",
        card_acc_title: "Accounting Integrations",
        card_acc_desc: "Real-time 2-way sync with Xero, QuickBooks, and Zoho Books.",
        
        card_pay_title: "Automated Payroll",
        card_pay_desc: "Disburse employee salaries in 3 clicks with automated calculations.",
        card_comp_title: "Statutory Compliance (CPF)",
        card_comp_desc: "Automated CPF, SDL, and statutory tax filing with 100% accuracy.",
        card_emp_title: "Employee Self-Service Portal",
        card_emp_desc: "Mobile app for employees to view itemized payslips and submit claims.",
        
        card_wc_title: "Instant Working Capital",
        card_wc_desc: "Collateral-free credit lines up to S$500,000 with approval in 2 hours.",
        card_line_title: "Corporate Credit Line",
        card_line_desc: "Flexible revolving credit line with daily automatic revenue-based repayments.",
        
        case_tag: "Direct-to-Consumer & Retail",
        case_quote: '"Razorepay helped us increase checkout success rates to 94.2% across the region."',
        case_desc: "With dynamic PayNow QR and intelligent route switches, thousands of growing brands deliver seamless checkout experiences.",
        case_btn: "Read Case Study",
        
        dev_tag: "RELIABILITY • SCALE • SECURITY • DEVELOPER-FIRST",
        dev_title_1: "Razorepay is built",
        dev_title_2: "for developers, by developers",
        dev_desc: "Explore predictable REST APIs, webhooks with auto-retries, and battle-tested SDKs designed to get you live in minutes.",
        dev_bullet_1: "Standardized RESTful JSON endpoints with Idempotency",
        dev_bullet_2: "Pre-built SDKs for PHP, Node.js, Python, Java, Go, React, Flutter",
        dev_bullet_3: "Postman collections and live webhook testing sandbox",
        dev_btn_keys: "Get API Keys",
        dev_btn_docs: "Explore API Docs",
        
        nocode_sub: "<what html?>",
        nocode_title_1: "Not a developer?",
        nocode_title_2: "Our No-Code products have you covered",
        nocode_1_title: "Payment Links",
        nocode_1_desc: "Accept payments instantly: Share links via email, text, or social.",
        nocode_2_title: "Payment Pages",
        nocode_2_desc: "Accept payments without coding on a custom-branded storefront.",
        nocode_3_title: "Payment Buttons",
        nocode_3_desc: "Effortlessly add a Pay Now button without any coding knowledge.",
        
        stat_scale_tag: "Enterprise Scale",
        stat_scale_title: "Powering billions in monthly transaction volume with 99.99% system availability.",
        stat_scale_desc: "Multi-region distributed architecture handles peak flash sales of 1,000+ txns/sec.",
        stat_scale_link: "Explore Infrastructure →",
        testi_tag: "Verified Testimonial",
        testi_quote: '"Razorepay APIs and automated vendor payouts allowed our engineering team to focus entirely on our core product."',
        
        founders_title_1: "Powering growth with",
        founders_title_2: "real stories",
        founders_count: "10,000,000+ businesses powered",
        
        faq_title: "Frequently asked questions",
        faq_desc: "Find answers to common questions regarding onboarding, pricing, payment methods, and security.",
        faq_support: "Have more questions? Contact support →",
        faq_q1: "What is Razorepay and how does it work?",
        faq_a1: "Razorepay is a full-stack payments platform that enables businesses to accept, process, and disburse payments across cards, PayNow, e-wallets, and bank transfers.",
        faq_q2: "What are the transaction charges and pricing?",
        faq_a2: "Razorepay charges competitive flat fees per successful transaction with no hidden setup or recurring maintenance charges.",
        faq_q3: "What payment modes are supported?",
        faq_a3: "We support 100+ payment methods including PayNow, Visa, Mastercard, Amex, GrabPay, and international currencies.",
        faq_q4: "How fast is onboarding and activation?",
        faq_a4: "You can sign up in 2 minutes, start testing immediately in Sandbox, and get activated within 24 hours via digital KYC.",
        faq_q5: "Is Razorepay secure and regulated?",
        faq_a5: "Yes. Razorepay is certified PCI-DSS Level 1, ISO/IEC 27001 compliant, and protected by bank-grade 256-bit encryption.",
        
        cta_title: "Supercharge your business with Razorepay",
        cta_desc: "Sign up in under 2 minutes and start accepting payments instantly with high-converting checkout.",
        cta_signup: "Get Started Now",
        cta_contact: "Contact Sales",
        
        footer_brand_desc: "Razorepay is the leading financial services platform enabling businesses to accept payments, automate banking, and scale globally.",
        footer_col_accept: "Accept Payments",
        footer_col_banking: "Banking & Payouts",
        footer_col_devs: "Developers",
        footer_col_company: "Company & Legal",
        contact_modal_title: "Talk to our Solutions Team",
        contact_modal_desc: "Discover custom enterprise pricing and tailored integration options."
    },

    // ==================== UNITED STATES (🇺🇸 English - USD) ====================
    us: {
        meta_label: "United States",
        flag: "🇺🇸",
        currency: "$",
        
        announcement_badge: "NEW",
        announcement_text: "Razorepay Global & Agentic Payments are live! Experience 5X faster autonomous checkouts.",
        announcement_link: "Explore now →",
        nav_payments: "Payments",
        nav_banking: "Banking+",
        nav_ai: "Build AI Native",
        nav_nocode: "No-Code Suite",
        nav_devs: "Developers",
        nav_pricing: "Pricing",
        nav_contact: "Contact Sales",
        nav_pg: "Payment Gateway",
        nav_pg_desc: "Credit/Debit Cards, Apple Pay, Google Pay & ACH",
        nav_qr: "Instant Checkout & QR",
        nav_qr_desc: "High-conversion checkout with real-time webhooks",
        nav_links: "Payment Links & Pages",
        nav_links_desc: "Zero-code branded checkout links",
        nav_ca: "Business Account",
        nav_ca_desc: "Automated business banking & cash management",
        nav_payouts: "Instant Global Payouts",
        nav_payouts_desc: "24x7 domestic ACH, Wire & Real-Time Payments",
        nav_payroll: "Automated Payroll",
        nav_payroll_desc: "Direct deposit & automated tax withholdings",
        
        hero_badge: "Global Enterprise Payment Solutions",
        hero_title_1: "Advanced Payment Solutions",
        hero_title_2: "for Modern Global Businesses",
        hero_desc: "Accept payments from customers worldwide, automate vendor payouts, manage treasury seamlessly, and accelerate growth with programmable financial infrastructure.",
        hero_signup: "Get Started Now",
        hero_trust_modes: "100+ Payment Modes",
        hero_trust_settle: "Instant Settlements",
        hero_trust_fees: "Zero Setup Fees",
        hero_card_received: "Payment Received",
        hero_card_sub: "via Apple Pay • Instant",
        hero_uptime: "99.99% Uptime",
        trust_partners: "Powering payments for market leaders, high-growth startups & enterprises",
        
        startup26_badge: "ANNUAL FINTECH SUMMIT",
        startup26_title_1: "STARTUP 26",
        startup26_title_2: "The Future of Autonomous Commerce",
        startup26_desc: "Join 10,000+ founders, developers & tech leaders to explore AI-native payments, smart routing, and programmable financial rails.",
        startup26_cta: "Register for Keynote",
        
        main_sec_title_1: "The all in one",
        main_sec_title_2: "finance platform you’ve been looking for",
        tab_ai: "Build AI Native",
        tab_accept: "Accept Payments",
        tab_payouts: "Make Payouts",
        tab_banking: "Business Banking",
        tab_payroll: "Automate Payroll",
        tab_credit: "Credit & Lending",
        tab_cta: "Get Started Now",
        
        card_ai_agentic_title: "Agentic Payments",
        card_ai_agentic_desc: "Turn every AI chat or agent trigger into an autonomous checkout flow.",
        card_ai_studio_title: "Agent Studio",
        card_ai_studio_desc: "Delegate financial workflows to autonomous AI agents that get things done.",
        card_ai_builders_title: "Payments for AI Builders",
        card_ai_builders_desc: "One-click payment nodes for n8n, Replit, and Vercel workflows.",
        card_ai_routing_title: "AI Routing Engine",
        card_ai_routing_desc: "Autonomous route optimization for 99.9% gateway success rates.",
        
        card_pg_title: "Payment Gateway",
        card_pg_desc: "Accept Visa, Mastercard, Amex, Apple Pay, Google Pay and ACH.",
        card_pl_title: "Payment Links",
        card_pl_desc: "Share links via SMS, WhatsApp, Email and get paid instantly.",
        card_pp_title: "Payment Pages",
        card_pp_desc: "Launch custom-branded payment pages without writing any code.",
        card_sub_title: "Subscriptions",
        card_sub_desc: "Automate recurring billing with smart retry logic and card updater.",
        
        card_bulk_title: "API & Bulk Payouts",
        card_bulk_desc: "24x7 instant payouts via FedNow, RTP, ACH, and wire transfers.",
        card_s2p_title: "Source to Pay",
        card_s2p_desc: "Automate vendor invoice management, OCR matching, and payout approvals.",
        card_plinks_title: "Payout Links",
        card_plinks_desc: "Disburse refunds, cashbacks & rewards directly with an instant link.",
        card_multibank_title: "Multi-Bank Routing",
        card_multibank_desc: "Dynamic uptime routing across leading bank partners and card networks.",
        
        card_ca_title: "Current Account",
        card_ca_desc: "Automated business banking hub built for startups & scaling enterprises.",
        card_cards_title: "Corporate Cards",
        card_cards_desc: "High-limit corporate cards with zero personal guarantee and SaaS rewards.",
        card_tax_title: "Tax Payments",
        card_tax_desc: "Automated corporate tax calculations, filing, and estimated quarterly taxes.",
        card_acc_title: "Accounting Integrations",
        card_acc_desc: "Real-time 2-way sync with QuickBooks, NetSuite, and Xero.",
        
        card_pay_title: "Automated Payroll",
        card_pay_desc: "Disburse salaries in 3 clicks with automated payroll tax calculations.",
        card_comp_title: "Compliance & Tax Filings",
        card_comp_desc: "Automated federal, state, and local payroll tax filings with 100% guarantee.",
        card_emp_title: "Self-Service Portal",
        card_emp_desc: "Mobile app for employees to view paystubs, W-2 forms, and benefits.",
        
        card_wc_title: "Instant Working Capital",
        card_wc_desc: "Collateral-free credit lines up to $500,000 with approval in 2 hours.",
        card_line_title: "Corporate Credit Line",
        card_line_desc: "Flexible revolving credit line with automatic revenue-based repayments.",
        
        case_tag: "Direct-to-Consumer & Retail",
        case_quote: '"Razorepay helped us increase checkout success rates to 94.2% globally."',
        case_desc: "With smart card routing and one-click checkouts, thousands of retail brands deliver seamless, zero-friction payment experiences.",
        case_btn: "Read Case Study",
        
        dev_tag: "RELIABILITY • SCALE • SECURITY • DEVELOPER-FIRST",
        dev_title_1: "Razorepay is built",
        dev_title_2: "for developers, by developers",
        dev_desc: "Explore predictable REST APIs, webhooks with auto-retries, and battle-tested SDKs designed to get you live in minutes.",
        dev_bullet_1: "Standardized RESTful JSON endpoints with Idempotency",
        dev_bullet_2: "Pre-built SDKs for PHP, Node.js, Python, Java, Go, React, Flutter",
        dev_bullet_3: "Postman collections and live webhook simulators",
        dev_btn_keys: "Get API Keys",
        dev_btn_docs: "Explore API Docs",
        
        nocode_sub: "<what html?>",
        nocode_title_1: "Not a developer?",
        nocode_title_2: "Our No-Code products have you covered",
        nocode_1_title: "Payment Links",
        nocode_1_desc: "Accept payments instantly: Share links via email, text, or social.",
        nocode_2_title: "Payment Pages",
        nocode_2_desc: "Accept payments without coding on a custom-branded storefront.",
        nocode_3_title: "Payment Buttons",
        nocode_3_desc: "Effortlessly add a Pay Now button without any coding knowledge.",
        
        stat_scale_tag: "Enterprise Scale",
        stat_scale_title: "Powering over $10B+ in monthly transactions with 99.99% system availability.",
        stat_scale_desc: "Distributed multi-region architecture handles peak flash sales of 1,000+ txns/sec.",
        stat_scale_link: "Explore Infrastructure →",
        testi_tag: "Verified Testimonial",
        testi_quote: '"Razorepay APIs and automated vendor payouts allowed our engineering team to focus entirely on our core product without building banking rails."',
        
        founders_title_1: "Powering growth with",
        founders_title_2: "real stories",
        founders_count: "10,000,000+ happy businesses",
        
        faq_title: "Frequently asked questions",
        faq_desc: "Find answers to common questions regarding account activation, pricing, payment methods, and security.",
        faq_support: "Have more questions? Contact support →",
        faq_q1: "What is Razorepay and how does it work?",
        faq_a1: "Razorepay is a full-stack payments platform that enables businesses to accept, process, and disburse payments across cards, ACH, digital wallets, and wires.",
        faq_q2: "What are the transaction charges and pricing?",
        faq_a2: "Razorepay offers transparent flat-rate pricing per successful transaction with no setup fees and no annual maintenance fees.",
        faq_q3: "What payment modes are supported?",
        faq_a3: "We support Visa, MasterCard, Amex, Discover, Apple Pay, Google Pay, ACH Direct Debit, and 100+ global payment modes.",
        faq_q4: "How fast is onboarding and activation?",
        faq_a4: "You can sign up in under 2 minutes, begin sandbox testing immediately, and get verified within 24 hours.",
        faq_q5: "Is Razorepay secure and compliant?",
        faq_a5: "Yes. Razorepay is certified PCI-DSS Level 1, SOC 2 / ISO 27001 compliant, and protected by bank-grade 256-bit encryption.",
        
        cta_title: "Supercharge your business with Razorepay",
        cta_desc: "Sign up in under 2 minutes and start accepting payments instantly with high-converting checkout.",
        cta_signup: "Get Started Now",
        cta_contact: "Contact Sales",
        
        footer_brand_desc: "Razorepay is the leading financial services platform enabling businesses to accept payments, automate banking, and scale globally.",
        footer_col_accept: "Accept Payments",
        footer_col_banking: "Banking & Payouts",
        footer_col_devs: "Developers",
        footer_col_company: "Company & Legal",
        contact_modal_title: "Talk to our Solutions Team",
        contact_modal_desc: "Discover custom enterprise pricing and tailored integration options."
    },

    // ==================== INDIA (🇮🇳 English - INR) ====================
    in: {
        meta_label: "India",
        flag: "🇮🇳",
        currency: "₹",
        
        announcement_badge: "NEW",
        announcement_text: "Razorepay Turbo UPI & Agentic Payments are live! Experience 5X faster checkouts.",
        announcement_link: "Explore now →",
        nav_payments: "Payments",
        nav_banking: "Banking+",
        nav_ai: "Build AI Native",
        nav_nocode: "No-Code Suite",
        nav_devs: "Developers",
        nav_pricing: "Pricing",
        nav_contact: "Contact Sales",
        nav_pg: "Payment Gateway",
        nav_pg_desc: "100+ payment modes, UPI AutoPay & Cards",
        nav_qr: "QR Codes & Smart Collect",
        nav_qr_desc: "Instant UPI QR with real-time webhooks",
        nav_links: "Payment Links & Pages",
        nav_links_desc: "Zero-code branded checkout links",
        nav_ca: "RazorepayX Current Account",
        nav_ca_desc: "Automated business banking built for startups",
        nav_payouts: "Instant Vendor Payouts",
        nav_payouts_desc: "24x7 IMPS, NEFT & UPI disbursements",
        nav_payroll: "Automated Payroll",
        nav_payroll_desc: "Salary transfers & automated tax filing",
        
        hero_badge: "India's Most Trusted Payment Gateway",
        hero_title_1: "Advanced Payment Solutions",
        hero_title_2: "for India's Boldest Businesses",
        hero_desc: "Accept payments from customers anywhere in the world, automate vendor payouts, seamlessly manage capital, and supercharge growth with built-in banking.",
        hero_signup: "Sign Up Now",
        hero_trust_modes: "100+ Payment Modes",
        hero_trust_settle: "Instant Settlements",
        hero_trust_fees: "Zero Setup Fees",
        hero_card_received: "Payment Received",
        hero_card_sub: "via UPI AutoPay • Instant",
        hero_uptime: "99.99% Uptime",
        trust_partners: "Powering payments for India's boldest startups & unicorns",
        
        startup26_badge: "ANNUAL FINTECH SUMMIT",
        startup26_title_1: "STARTUP 26",
        startup26_title_2: "The Future of Autonomous Commerce",
        startup26_desc: "Join 10,000+ founders, developers & industry leaders to explore AI-native payments, smart routing, and next-gen financial architecture.",
        startup26_cta: "Register for Keynote",
        
        main_sec_title_1: "The all in one",
        main_sec_title_2: "finance platform you’ve been looking for",
        tab_ai: "Build AI Native",
        tab_accept: "Accept Payments",
        tab_payouts: "Make Payouts",
        tab_banking: "Start Business Banking",
        tab_payroll: "Automate Payroll",
        tab_credit: "Get Credit & Loans",
        tab_cta: "Get Started Now",
        
        card_ai_agentic_title: "Agentic Payments",
        card_ai_agentic_desc: "Turn every chat into a checkout with AI-native payment flows.",
        card_ai_studio_title: "Agent Studio",
        card_ai_studio_desc: "Delegate operational work to agents that get things done.",
        card_ai_builders_title: "Payments for AI Builders",
        card_ai_builders_desc: "One-click payment nodes for n8n, Replit, and Vercel workflows.",
        card_ai_routing_title: "AI Routing Engine",
        card_ai_routing_desc: "Autonomous route optimization for 99.9% gateway success rates.",
        
        card_pg_title: "Payment Gateway",
        card_pg_desc: "Accept 100+ payment methods with highest conversion checkout.",
        card_pl_title: "Payment Links",
        card_pl_desc: "Share links via SMS, WhatsApp, Email and get paid instantly.",
        card_pp_title: "Payment Pages",
        card_pp_desc: "Launch custom-branded payment pages without writing any code.",
        card_sub_title: "Subscriptions",
        card_sub_desc: "Automate recurring billing with UPI AutoPay & Cards.",
        
        card_bulk_title: "API & Bulk Payouts",
        card_bulk_desc: "24x7 instant payouts via IMPS, NEFT & UPI even on holidays.",
        card_s2p_title: "Source to Pay",
        card_s2p_desc: "Automate vendor invoice management and payout approvals.",
        card_plinks_title: "Payout Links",
        card_plinks_desc: "Disburse refunds, cashbacks & rewards directly with a link.",
        card_multibank_title: "Multi-Bank Routing",
        card_multibank_desc: "Dynamic uptime routing across leading bank partners.",
        
        card_ca_title: "Current Account",
        card_ca_desc: "Automated business banking hub built for startups & scale-ups.",
        card_cards_title: "Corporate Cards",
        card_cards_desc: "Collateral-free cards with high limits & cashbacks on SaaS spend.",
        card_tax_title: "Tax Payments",
        card_tax_desc: "1-click automated TDS, GST and advance tax payments.",
        card_acc_title: "Accounting Integrations",
        card_acc_desc: "Real-time sync with Tally, Zoho Books, and QuickBooks.",
        
        card_pay_title: "Automated Payroll",
        card_pay_desc: "Disburse salaries in 3 clicks with automated calculations.",
        card_comp_title: "Compliance (PF/PT/TDS)",
        card_comp_desc: "Automated statutory compliance with 100% filing guarantee.",
        card_emp_title: "Self-Service Portal",
        card_emp_desc: "Mobile app for employees to view payslips and file tax declarations.",
        
        card_wc_title: "Instant Working Capital",
        card_wc_desc: "Collateral-free credit lines up to ₹50 Lakhs with approval in 2 hours.",
        card_line_title: "Corporate Credit Line",
        card_line_desc: "Flexible revolving credit line with daily automatic micro-repayments.",
        
        case_tag: "Direct-to-Consumer & Retail",
        case_quote: '"Razorepay helped us increase checkout success rates to 94.2% across India."',
        case_desc: "With custom UPI dynamic QR and intelligent route switches, thousands of growing retail brands deliver seamless, zero-friction payment experiences to millions of shoppers.",
        case_btn: "Read Case Study",
        
        dev_tag: "RELIABILITY • SCALE • SECURITY • DEVELOPER-FIRST",
        dev_title_1: "Razorepay is built",
        dev_title_2: "for developers, by developers",
        dev_desc: "Explore predictable REST APIs, webhooks with auto-retries, and battle-tested SDKs designed to get you from sandbox to production in minutes.",
        dev_bullet_1: "Standardized RESTful JSON endpoints with Idempotency",
        dev_bullet_2: "Pre-built SDKs for PHP, Node.js, Python, Java, Go, React, Flutter",
        dev_bullet_3: "Postman collections and live webhook simulators",
        dev_btn_keys: "Get API Keys",
        dev_btn_docs: "Explore API Docs",
        
        nocode_sub: "<what html?>",
        nocode_title_1: "Not a developer?",
        nocode_title_2: "Our No-Code products have you covered",
        nocode_1_title: "Payment Links",
        nocode_1_desc: "Accept payments instantly: Share links via email, text, or social.",
        nocode_2_title: "Payment Pages",
        nocode_2_desc: "Accept payments without coding on a custom-branded store.",
        nocode_3_title: "Payment Buttons",
        nocode_3_desc: "Effortlessly add a Pay Now button without any coding knowledge.",
        
        stat_scale_tag: "Enterprise Scale",
        stat_scale_title: "Powering over ₹10,000+ Crore in monthly transactions with 99.99% system availability.",
        stat_scale_desc: "Our distributed multi-region banking architecture handles peak flash sales of 1,000+ transactions per second without dropping a single packet.",
        stat_scale_link: "Explore Infrastructure →",
        testi_tag: "Verified Testimonial",
        testi_quote: '"Razorepay\'s APIs and automated vendor payouts allowed our engineering team to focus entirely on our core product without building banking rails from scratch."',
        
        founders_title_1: "Powering growth with",
        founders_title_2: "real stories",
        founders_count: "10,000,000+ happy businesses",
        
        faq_title: "Frequently asked questions",
        faq_desc: "Find answers to common questions regarding account activation, pricing, payment methods, and security.",
        faq_support: "Have more questions? Contact support →",
        faq_q1: "What is Razorepay and how does it work?",
        faq_a1: "Razorepay is a full-stack payments solution allowing businesses in India to accept, process, and disburse payments. With a single integration, you get access to UPI, Credit/Debit cards, Netbanking, Wallets, and international cards.",
        faq_q2: "What are the transaction charges and pricing?",
        faq_a2: "Razorepay charges a standard flat 2% fee per successful domestic transaction. There are no setup fees, no annual maintenance charges (AMC), and custom enterprise pricing is available for high-volume merchants.",
        faq_q3: "What payment modes are supported?",
        faq_a3: "We support 100+ payment methods including UPI (Google Pay, PhonePe, Paytm, BHIM), all major Credit & Debit cards (Visa, MasterCard, RuPay, Amex), 50+ Netbanking options, popular Wallets, Cardless EMI, and PayLater.",
        faq_q4: "How fast is the onboarding and activation?",
        faq_a4: "You can sign up in under 2 minutes and start testing in sandbox immediately. Account activation requires 100% paperless digital KYC verification and typically completes within 24 hours.",
        faq_q5: "Is Razorepay secure and RBI authorized?",
        faq_a5: "Yes. Razorepay is an authorized Payment Aggregator (PA) by the Reserve Bank of India (RBI), certified PCI-DSS Level 1, ISO 27001 compliant, and protected by bank-grade 256-bit SSL encryption.",
        
        cta_title: "Supercharge your business with Razorepay",
        cta_desc: "Sign up in under 2 minutes and start accepting payments instantly with India's highest-converting checkout.",
        cta_signup: "Sign Up Now",
        cta_contact: "Contact Sales",
        
        footer_brand_desc: "Razorepay is India's leading full-stack financial services platform that enables businesses to accept payments, automate banking operations, manage payroll, and access business credit.",
        footer_col_accept: "Accept Payments",
        footer_col_banking: "Banking & Payouts",
        footer_col_devs: "Developers",
        footer_col_company: "Company & Legal",
        contact_modal_title: "Talk to our Solutions Team",
        contact_modal_desc: "Discover custom pricing, enterprise architecture and tailored integration options."
    }
};

/**
 * Switch & Apply Language
 */
function setLanguage(locale) {
    if (!RAZORPAY_TRANSLATIONS[locale]) {
        locale = 'vi'; // Default to Vietnamese
    }

    const dict = RAZORPAY_TRANSLATIONS[locale];
    localStorage.setItem('razorepay_locale', locale);

    // 1. Update Header Button Flag & Label
    const currentFlag = document.getElementById('current-lang-flag');
    const currentLabel = document.getElementById('current-lang-label');
    if (currentFlag) currentFlag.innerText = dict.flag;
    if (currentLabel) currentLabel.innerText = dict.meta_label;

    // 2. Update Dropdown Checkmarks
    document.querySelectorAll('.lang-option-btn').forEach(btn => {
        const bLang = btn.getAttribute('data-lang');
        const check = btn.querySelector('.lang-check');
        if (bLang === locale) {
            btn.classList.add('bg-blue-50', 'text-rzp-blue', 'font-bold');
            if (check) check.classList.remove('hidden');
        } else {
            btn.classList.remove('bg-blue-50', 'text-rzp-blue', 'font-bold');
            if (check) check.classList.add('hidden');
        }
    });

    // 3. Update Mobile Drawer Lang Buttons
    document.querySelectorAll('.mobile-lang-btn').forEach(btn => {
        const bLang = btn.getAttribute('data-lang');
        const check = btn.querySelector('.mobile-lang-check');
        if (bLang === locale) {
            btn.classList.add('bg-blue-50', 'text-rzp-blue', 'font-bold');
            if (check) check.classList.remove('hidden');
        } else {
            btn.classList.remove('bg-blue-50', 'text-rzp-blue', 'font-bold');
            if (check) check.classList.add('hidden');
        }
    });

    // 4. Translate all text elements with data-i18n
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (dict[key] !== undefined) {
            el.textContent = dict[key];
        }
    });

    // 5. Translate all HTML elements with data-i18n-html
    document.querySelectorAll('[data-i18n-html]').forEach(el => {
        const key = el.getAttribute('data-i18n-html');
        if (dict[key] !== undefined) {
            el.innerHTML = dict[key];
        }
    });

    // 6. Translate all Placeholders
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (dict[key] !== undefined) {
            el.placeholder = dict[key];
        }
    });

    // 7. Close dropdown
    closeLangDropdown();
}

/**
 * Dropdown Toggle Helper
 */
function toggleLangDropdown(e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('lang-dropdown-menu');
    const arrow = document.getElementById('lang-arrow');
    if (!menu) return;

    const isVisible = !menu.classList.contains('invisible');
    if (isVisible) {
        closeLangDropdown();
    } else {
        menu.classList.remove('opacity-0', 'invisible');
        if (arrow) arrow.classList.add('rotate-180');
    }
}

function closeLangDropdown() {
    const menu = document.getElementById('lang-dropdown-menu');
    const arrow = document.getElementById('lang-arrow');
    if (menu) {
        menu.classList.add('opacity-0', 'invisible');
    }
    if (arrow) {
        arrow.classList.remove('rotate-180');
    }
}

// Global click outside to close dropdown
document.addEventListener('click', (e) => {
    const container = document.getElementById('lang-dropdown-container');
    if (container && !container.contains(e.target)) {
        closeLangDropdown();
    }
});

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const urlLang = urlParams.get('lang') || urlParams.get('locale');
    const savedLang = localStorage.getItem('razorepay_locale');
    
    // Default to 'vi' if Vietnamese preferred, or saved
    const activeLocale = urlLang || savedLang || 'vi';
    setLanguage(activeLocale);

    const langBtn = document.getElementById('lang-menu-btn');
    if (langBtn) {
        langBtn.addEventListener('click', toggleLangDropdown);
    }
});
