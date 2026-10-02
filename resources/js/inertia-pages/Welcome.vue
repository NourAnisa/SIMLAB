<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    auth: Object,
    siteSetting: Object,
    skemas: Array,
    jadwalSertifikasi: Array,
    beritas: Array,
    moduls: Array,
    strukturs: Array
});

const curSlide = ref(0);
let slideInterval = null;
const isScrolled = ref(false);

onMounted(() => {
    startSlideshow();
    window.addEventListener('scroll', handleScroll);
    document.body.classList.add('landing-page');
    if (props.siteSetting?.tema_warna === 'merah') {
        document.body.classList.add('theme-merah');
    } else {
        document.body.classList.remove('theme-merah');
    }
});

onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
    window.removeEventListener('scroll', handleScroll);
    document.body.classList.remove('landing-page');
    document.body.classList.remove('theme-merah');
});

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const startSlideshow = () => {
    slideInterval = setInterval(() => {
        if (props.siteSetting?.gallery_images && props.siteSetting.gallery_images.length > 1) {
            curSlide.value = (curSlide.value + 1) % props.siteSetting.gallery_images.length;
        }
    }, 4000);
};

const setSlide = (index) => {
    curSlide.value = index;
    if (slideInterval) clearInterval(slideInterval);
    startSlideshow();
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};

const formatShortDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const getDashboardRoute = () => {
    if (!props.auth?.user) return '/';
    return `/${props.auth.user.role}/dashboard`;
};

const stripTags = (html) => {
    if (!html) return '';
    const tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
};

const getLimitString = (str, limit) => {
    if(!str) return '';
    if(str.length <= limit) return str;
    return str.substring(0, limit) + '...';
};

const getModulIcon = (path) => {
    if (!path) return { icon: 'fa-file', color: '#64748b', bg: 'linear-gradient(135deg,#f8fafc,#f1f5f9)', border: '#cbd5e1' };
    const ext = path.split('.').pop().toLowerCase();
    switch (ext) {
        case 'pdf': return { icon: 'fa-file-pdf', color: '#ef4444', bg: 'linear-gradient(135deg,#fff1f2,#ffe4e6)', border: '#fda4af' };
        case 'doc': case 'docx': return { icon: 'fa-file-word', color: '#3b82f6', bg: 'linear-gradient(135deg,#eff6ff,#dbeafe)', border: '#93c5fd' };
        case 'ppt': case 'pptx': return { icon: 'fa-file-powerpoint', color: '#f97316', bg: 'linear-gradient(135deg,#fff7ed,#ffedd5)', border: '#fdba74' };
        default: return { icon: 'fa-file', color: '#64748b', bg: 'linear-gradient(135deg,#f8fafc,#f1f5f9)', border: '#cbd5e1' };
    }
};

const getKatGrad = (kat) => {
    switch (kat) {
        case 'Pengumuman': return 'linear-gradient(135deg,#667eea,#764ba2)';
        case 'Kegiatan': return 'linear-gradient(135deg,#11998e,#38ef7d)';
        case 'Info Wisuda': return 'linear-gradient(135deg,#f093fb,#f5576c)';
        case 'Akademik': return 'linear-gradient(135deg,#f59e0b,#d97706)';
        default: return 'linear-gradient(135deg,#64748b,#94a3b8)';
    }
};

const getKatEmoji = (kat) => {
    switch (kat) {
        case 'Pengumuman': return '📢';
        case 'Kegiatan': return '🔬';
        case 'Info Wisuda': return '🎓';
        case 'Akademik': return '📚';
        default: return '📰';
    }
};

const scrollTo = (id) => {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>

<template>
<div class="welcome-page">
    <!-- NAVBAR -->
    <nav class="navbar" :style="{ boxShadow: isScrolled ? '0 4px 24px rgba(0,0,0,0.12)' : '0 1px 20px rgba(0,0,0,0.06)' }">
        <div class="navbar-inner">
            <Link href="/" class="navbar-brand">
                <img v-if="siteSetting?.logo" :src="`/storage-public/${siteSetting.logo}`" alt="Logo" style="width:42px;height:42px;border-radius:10px;object-fit:contain">
                <div v-else class="navbar-logo">SL</div>
                <div class="navbar-title">
                    <strong>{{ siteSetting?.nama_website || 'SIMLAB STMIK' }}</strong>
                    <span>{{ siteSetting?.nama_kampus || 'Samarinda' }}</span>
                </div>
            </Link>
            <ul class="navbar-menu">
                <li><a href="#layanan" @click.prevent="scrollTo('layanan')">Layanan</a></li>
                <li><a href="#tentang" @click.prevent="scrollTo('tentang')">Tentang</a></li>
                <li><a href="#struktur" @click.prevent="scrollTo('struktur')">Struktur</a></li>
                <li><a href="#berita" @click.prevent="scrollTo('berita')">Berita</a></li>
                <li><a href="#kontak" @click.prevent="scrollTo('kontak')">Kontak</a></li>
            </ul>
            <div class="navbar-actions">
                <template v-if="auth.user">
                    <Link :href="getDashboardRoute()" class="btn-primary">
                        <i class="fa fa-th-large" style="margin-right:6px;"></i>Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link href="/login" class="btn-outline">Masuk</Link>
                    <Link href="/register" class="btn-primary">Daftar</Link>
                </template>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-badge fade-in fade-in-1">
                    <span></span> Sistem Informasi Lab Digital {{ siteSetting?.nama_kampus || 'STMIK Samarinda' }}
                </div>
                <h1 class="fade-in fade-in-2">
                    Layanan Lab <span class="accent">Lebih Mudah</span><br>&amp; Lebih Cepat
                </h1>
                <p class="fade-in fade-in-3">
                    {{ siteSetting?.deskripsi || 'Platform digital terintegrasi untuk pengelolaan seluruh layanan Laboratorium STMIK Samarinda — dari sertifikasi, izin penelitian, peminjaman ruangan, hingga surat bebas lab.' }}
                </p>
                <div class="hero-buttons fade-in fade-in-4">
                    <template v-if="auth.user">
                        <Link :href="getDashboardRoute()" class="hero-btn-primary">
                            <i class="fa fa-th-large"></i> Ke Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/register" class="hero-btn-primary">
                            <i class="fa fa-rocket"></i> Mulai Sekarang
                        </Link>
                        <Link href="/login" class="hero-btn-secondary">
                            <i class="fa fa-sign-in-alt"></i> Sudah Punya Akun
                        </Link>
                    </template>
                </div>
            </div>
            <div class="hero-cards">
                <div class="hero-card">
                    <div class="hero-card-icon" style="background:linear-gradient(135deg,rgba(79,70,229,0.3),rgba(124,58,237,0.3));">🏆</div>
                    <div class="hero-card-text">
                        <strong>Sertifikasi Kompetensi</strong>
                        <span>Daftar &amp; kelola ujian sertifikasi lab</span>
                    </div>
                </div>
                <div class="hero-card">
                    <div class="hero-card-icon" style="background:linear-gradient(135deg,rgba(16,185,129,0.3),rgba(5,150,105,0.3));">🔬</div>
                    <div class="hero-card-text">
                        <strong>Izin Penelitian</strong>
                        <span>Ajukan izin penelitian secara online</span>
                    </div>
                </div>
                <div class="hero-card">
                    <div class="hero-card-icon" style="background:linear-gradient(135deg,rgba(245,158,11,0.3),rgba(217,119,6,0.3));">🏫</div>
                    <div class="hero-card-text">
                        <strong>Peminjaman Lab</strong>
                        <span>Pesan ruangan lab dengan mudah</span>
                    </div>
                </div>
                <div class="hero-card">
                    <div class="hero-card-icon" style="background:linear-gradient(135deg,rgba(239,68,68,0.3),rgba(220,38,38,0.3));">📄</div>
                    <div class="hero-card-text">
                        <strong>Surat Bebas Lab</strong>
                        <span>Pengajuan surat keterangan bebas lab</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <div class="stats-bar">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-number">4</div>
                <div class="stat-label">Layanan Digital Terintegrasi</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Akses Online Kapanpun</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">4</div>
                <div class="stat-label">Level Akses Pengguna</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Proses Paperless</div>
            </div>
        </div>
    </div>

    <!-- LAYANAN -->
    <section class="section services-bg" id="layanan">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Platform Digital</div>
                <h2 class="section-title">Semua Kebutuhan Lab dalam Satu Platform</h2>
                <p class="section-desc">Layanan laboratorium dan informasi akademik kini dapat diakses secara digital, kapan saja dan di mana saja.</p>
            </div>

            <!-- GROUP: LAYANAN -->
            <div style="margin:36px 0 16px;display:flex;align-items:center;gap:12px">
                <div style="height:3px;flex:1;background:linear-gradient(90deg,var(--color-primary),transparent)"></div>
                <span style="font-size:13px;font-weight:700;color:var(--color-primary);letter-spacing:1px;text-transform:uppercase">Layanan</span>
                <div style="height:3px;flex:1;background:linear-gradient(90deg,transparent,var(--color-primary))"></div>
            </div>
            <div class="services-grid">
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,var(--color-primary),var(--color-secondary));--icon-bg:linear-gradient(135deg,rgba(var(--color-primary-rgb),0.12),rgba(var(--color-secondary-rgb),0.12));--check-color:var(--color-primary);">
                    <div class="service-icon">🏆</div>
                    <h3>Sertifikasi Kompetensi</h3>
                    <p>Daftarkan diri untuk ujian sertifikasi berdasarkan skema yang tersedia. Pantau status pendaftaran dan hasil ujian secara real-time.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Pilih jadwal &amp; skema sertifikasi</li>
                        <li><i class="fa fa-check-circle"></i> Upload dokumen persyaratan</li>
                        <li><i class="fa fa-check-circle"></i> Hasil validasi kepala lab</li>
                    </ul>
                </div>
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#10b981,#059669);--icon-bg:linear-gradient(135deg,rgba(16,185,129,0.12),rgba(5,150,105,0.12));--check-color:#10b981;">
                    <div class="service-icon">🔬</div>
                    <h3>Izin Penelitian</h3>
                    <p>Ajukan permohonan izin penelitian di laboratorium secara online. Proses persetujuan yang transparan dan cepat.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Formulir pengajuan online</li>
                        <li><i class="fa fa-check-circle"></i> Upload proposal penelitian</li>
                        <li><i class="fa fa-check-circle"></i> Tracking status persetujuan</li>
                    </ul>
                </div>
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#f59e0b,#d97706);--icon-bg:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(217,119,6,0.12));--check-color:#f59e0b;">
                    <div class="service-icon">🏫</div>
                    <h3>Peminjaman Lab</h3>
                    <p>Pesan ruangan laboratorium untuk kegiatan perkuliahan, praktikum, atau kegiatan akademik lainnya dengan sistem booking online.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Cek ketersediaan ruangan</li>
                        <li><i class="fa fa-check-circle"></i> Pilih tanggal &amp; jam</li>
                        <li><i class="fa fa-check-circle"></i> Akses mahasiswa &amp; dosen</li>
                    </ul>
                </div>
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#ef4444,#dc2626);--icon-bg:linear-gradient(135deg,rgba(239,68,68,0.12),rgba(220,38,38,0.12));--check-color:#ef4444;">
                    <div class="service-icon">📄</div>
                    <h3>Surat Bebas Lab</h3>
                    <p>Ajukan permohonan surat keterangan bebas laboratorium untuk keperluan wisuda, yudisium, atau administrasi akademik.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Pengecekan tanggungan otomatis</li>
                        <li><i class="fa fa-check-circle"></i> Proses verifikasi bertahap</li>
                        <li><i class="fa fa-check-circle"></i> Download surat digital (PDF)</li>
                    </ul>
                </div>
            </div>

            <!-- GROUP: INFORMASI -->
            <div style="margin:40px 0 16px;display:flex;align-items:center;gap:12px">
                <div style="height:3px;flex:1;background:linear-gradient(90deg,#06b6d4,transparent)"></div>
                <span style="font-size:13px;font-weight:700;color:#06b6d4;letter-spacing:1px;text-transform:uppercase">Informasi</span>
                <div style="height:3px;flex:1;background:linear-gradient(90deg,transparent,#06b6d4)"></div>
            </div>
            <div class="services-grid">
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#06b6d4,#0891b2);--icon-bg:linear-gradient(135deg,rgba(6,182,212,0.12),rgba(8,145,178,0.12));--check-color:#06b6d4;">
                    <div class="service-icon">🏛️</div>
                    <h3>Tentang Lab</h3>
                    <p>Kenali fasilitas, visi misi, dan profil lengkap laboratorium komputer STMIK Samarinda.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Profil & sejarah laboratorium</li>
                        <li><i class="fa fa-check-circle"></i> Fasilitas yang tersedia</li>
                        <li><i class="fa fa-check-circle"></i> Galeri foto laboratorium</li>
                    </ul>
                    <a href="#tentang" @click.prevent="scrollTo('tentang')" style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:13px;font-weight:600;color:#06b6d4;text-decoration:none">Selengkapnya <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#f59e0b,#d97706);--icon-bg:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(217,119,6,0.12));--check-color:#f59e0b;">
                    <div class="service-icon">📰</div>
                    <h3>Berita &amp; Pengumuman</h3>
                    <p>Ikuti informasi terkini seputar kegiatan laboratorium, pengumuman penting, dan agenda akademik.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Berita terbaru laboratorium</li>
                        <li><i class="fa fa-check-circle"></i> Pengumuman jadwal & kegiatan</li>
                        <li><i class="fa fa-check-circle"></i> Informasi dari kepala lab</li>
                    </ul>
                    <a href="#berita" @click.prevent="scrollTo('berita')" style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:13px;font-weight:600;color:#d97706;text-decoration:none">Lihat Berita <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#8b5cf6,#6d28d9);--icon-bg:linear-gradient(135deg,rgba(139,92,246,0.12),rgba(109,40,217,0.12));--check-color:#8b5cf6;">
                    <div class="service-icon">📚</div>
                    <h3>Modul Praktikum</h3>
                    <p>Akses materi praktikum yang disiapkan dosen pengampu. Unduh modul dan bahan ajar untuk persiapan praktikum.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Modul PDF, PPT, Word</li>
                        <li><i class="fa fa-check-circle"></i> Dikelola langsung oleh dosen</li>
                        <li><i class="fa fa-check-circle"></i> Akses mudah tanpa login</li>
                    </ul>
                    <a href="#modul-praktikum" @click.prevent="scrollTo('modul-praktikum')" style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:13px;font-weight:600;color:#8b5cf6;text-decoration:none">Lihat Modul <i class="fa fa-arrow-right"></i></a>
                </div>
                <div class="service-card" style="--card-gradient:linear-gradient(90deg,#0ea5e9,#0284c7);--icon-bg:linear-gradient(135deg,rgba(14,165,233,0.12),rgba(2,132,199,0.12));--check-color:#0ea5e9;">
                    <div class="service-icon">👥</div>
                    <h3>Struktur Organisasi</h3>
                    <p>Kenali tim pengelola laboratorium yang siap melayani kebutuhan akademik mahasiswa dan dosen.</p>
                    <ul class="service-features">
                        <li><i class="fa fa-check-circle"></i> Kepala & staf laboratorium</li>
                        <li><i class="fa fa-check-circle"></i> Teknisi dan asisten lab</li>
                        <li><i class="fa fa-check-circle"></i> Informasi kontak tim</li>
                    </ul>
                    <a href="#struktur" @click.prevent="scrollTo('struktur')" style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;font-size:13px;font-weight:600;color:#0ea5e9;text-decoration:none">Lihat Struktur <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="section">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Cara Kerja</div>
                <h2 class="section-title">Proses Mudah dalam 4 Langkah</h2>
                <p class="section-desc">Dari pendaftaran hingga persetujuan, semua bisa dilakukan secara digital.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4>Daftar Akun</h4>
                    <p>Buat akun dengan email kampus dan lengkapi profil sesuai role (mahasiswa/dosen).</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4>Pilih Layanan</h4>
                    <p>Pilih layanan yang dibutuhkan dan isi formulir permohonan secara online.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4>Proses Verifikasi</h4>
                    <p>Admin dan Kepala Lab memverifikasi permohonan sesuai alur persetujuan.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h4>Selesai!</h4>
                    <p>Unduh dokumen atau pantau status kapanpun.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TENTANG -->
    <section class="section services-bg" id="tentang">
        <div class="section-inner">
            <div class="about-grid">
                <div class="about-img" style="padding:0;overflow:hidden;border-radius:20px;position:relative;min-height:320px;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary))">
                    <template v-if="siteSetting?.gallery_images && siteSetting.gallery_images.length > 0">
                        <!-- SLIDESHOW -->
                        <div id="gallery-slideshow" style="position:relative;width:100%;height:100%;min-height:320px">
                            <div v-for="(img, i) in siteSetting.gallery_images" :key="i" class="slide" :style="{ position: 'absolute', inset: 0, opacity: i === curSlide ? 1 : 0, transition: 'opacity .8s ease', zIndex: i === curSlide ? 1 : 0 }">
                                <img :src="`/storage-public/${img}`" alt="Galeri Lab" style="width:100%;height:100%;object-fit:cover;display:block">
                                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.45) 0%,transparent 60%)"></div>
                            </div>
                            <div style="position:absolute;bottom:16px;left:50%;transform:translateX(-50%);display:flex;gap:7px;z-index:10">
                                <button v-for="(img, i) in siteSetting.gallery_images" :key="'dot-'+i" @click="setSlide(i)" :style="{ width: i === curSlide ? '24px' : '8px', height: '8px', borderRadius: '8px', background: i === curSlide ? '#fff' : 'rgba(255,255,255,.5)', border: 'none', cursor: 'pointer', transition: 'all .3s', padding: 0 }"></button>
                            </div>
                            <div style="position:absolute;bottom:20px;left:20px;color:#fff;font-size:13px;font-weight:600;text-shadow:0 1px 4px rgba(0,0,0,.5);z-index:10">
                                {{ siteSetting?.nama_kampus || 'STMIK Samarinda' }}
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="about-img-content">
                            <div class="about-img-icon">🖥️</div>
                            <div class="about-img-text">{{ siteSetting?.nama_kampus || 'Laboratorium STMIK Samarinda' }}</div>
                        </div>
                    </template>
                </div>
                <div class="about-text">
                    <div style="margin-bottom:20px;">
                        <span class="badge">STMIK Samarinda</span>
                        <span class="badge">Laboratorium Komputer</span>
                        <span class="badge">Digital Services</span>
                    </div>
                    <h2>Tentang Laboratorium STMIK Samarinda</h2>
                    <p>Laboratorium STMIK Samarinda adalah fasilitas pendukung kegiatan akademik yang menyediakan ruang praktikum, sertifikasi kompetensi, dan berbagai layanan teknis bagi civitas akademika.</p>
                    <p>Dengan sistem informasi ini, kami berkomitmen untuk memberikan layanan yang lebih cepat, transparan, dan efisien melalui digitalisasi seluruh proses administrasi laboratorium.</p>
                    <ul class="about-list">
                        <li><i class="fa fa-check-circle"></i> Sistem persetujuan bertingkat yang transparan</li>
                        <li><i class="fa fa-check-circle"></i> Akses 24 jam dari mana saja via browser</li>
                        <li><i class="fa fa-check-circle"></i> Arsip digital semua dokumen lab</li>
                    </ul>
                    <Link href="/register" class="btn-primary" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;border-radius:10px;">
                        <i class="fa fa-arrow-right"></i> Daftar Sekarang
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- STRUKTUR ORGANISASI -->
    <section class="section services-bg" id="struktur">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Tim Lab</div>
                <h2 class="section-title">Struktur Organisasi</h2>
                <p class="section-desc">Kenali tim pengelola laboratorium yang siap melayani kebutuhan akademik mahasiswa dan dosen.</p>
            </div>
            
            <p v-if="strukturs.length === 0" style="text-align:center;color:#94a3b8;font-size:15px;padding:32px 0">Belum ada data struktur organisasi.</p>
            
            <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px">
                <div v-for="s in strukturs" :key="s.id" style="background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.04);overflow:hidden;border:1px solid #e2e8f0;transition:all .3s" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,.08)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(0,0,0,.04)'">
                    <div style="height:6px;background:linear-gradient(90deg,#0ea5e9,#0284c7)"></div>
                    <div style="padding:24px 20px;text-align:center">
                        <img v-if="s.foto" :src="`/storage-public/${s.foto}`" :alt="s.nama" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e0f2fe;margin-bottom:14px;box-shadow:0 4px 12px rgba(14,165,233,.2)">
                        <div v-else style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#0284c7);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:28px;color:#fff;font-weight:700;box-shadow:0 4px 12px rgba(14,165,233,.2)">
                            {{ s.nama.substring(0, 1).toUpperCase() }}
                        </div>
                        
                        <div style="font-size:12px;font-weight:700;color:#0ea5e9;background:#e0f2fe;padding:4px 12px;border-radius:20px;display:inline-block;margin-bottom:12px">
                            {{ s.jabatan }}
                        </div>
                        <h4 style="margin:0 0 8px;font-size:16px;color:#0f172a;font-weight:800">{{ s.nama }}</h4>
                        <p v-if="s.deskripsi" style="margin:0;font-size:13px;color:#64748b;line-height:1.6">{{ s.deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BERITA -->
    <section class="section" id="berita">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Informasi Terkini</div>
                <h2 class="section-title">Berita &amp; Pengumuman</h2>
                <p class="section-desc">Dapatkan informasi terbaru seputar kegiatan dan layanan laboratorium.</p>
            </div>
            
            <p v-if="beritas.length === 0" style="text-align:center;color:#94a3b8;font-size:15px;padding:32px 0">Belum ada berita yang dipublikasikan.</p>
            
            <div v-else class="news-grid">
                <div v-for="b in beritas" :key="b.id" class="news-card">
                    <div v-if="b.gambar" class="news-img" :style="{'--ng': getKatGrad(b.kategori || 'Lainnya'), padding: 0, overflow: 'hidden'}">
                        <img :src="`/storage-public/${b.gambar}`" :alt="b.judul" style="width:100%;height:100%;object-fit:cover">
                    </div>
                    <div v-else class="news-img" :style="{'--ng': getKatGrad(b.kategori || 'Lainnya')}">{{ getKatEmoji(b.kategori || 'Lainnya') }}</div>
                    <div class="news-body">
                        <span class="news-tag">{{ b.kategori || 'Lainnya' }}</span>
                        <h4>{{ b.judul }}</h4>
                        <p>{{ b.ringkasan || getLimitString(stripTags(b.konten), 120) }}</p>
                        <div class="news-meta">
                            <i class="fa fa-calendar"></i> {{ formatShortDate(b.created_at) }}
                            <template v-if="b.user"> &nbsp;·&nbsp; <i class="fa fa-user"></i> {{ b.user.name }} </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODUL PRAKTIKUM -->
    <section class="section" id="modul-praktikum">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Bahan Ajar</div>
                <h2 class="section-title">Modul Praktikum</h2>
                <p class="section-desc">Unduh modul praktikum dan bahan ajar yang telah diunggah oleh dosen pengampu.</p>
            </div>
            
            <p v-if="moduls.length === 0" style="text-align:center;color:#94a3b8;font-size:15px;padding:32px 0">Belum ada modul praktikum yang tersedia.</p>
            
            <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px">
                <div v-for="m in moduls" :key="m.id" style="background:#fff;border-radius:16px;padding:24px;border:1px solid #e2e8f0;transition:all .3s;display:flex;flex-direction:column;box-shadow:0 2px 8px rgba(0,0,0,.04)" onmouseover="this.style.boxShadow='0 8px 24px rgba(139,92,246,.12)';this.style.borderColor='#c4b5fd'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,.04)';this.style.borderColor='#e2e8f0'">
                    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:16px;flex:1">
                        <div :style="`width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:${getModulIcon(m.file_path).bg};border:1px solid ${getModulIcon(m.file_path).border}`">
                            <i :class="`fa ${getModulIcon(m.file_path).icon}`" :style="`font-size:24px;color:${getModulIcon(m.file_path).color}`"></i>
                        </div>
                        <div>
                            <div style="font-size:11px;font-weight:700;color:#8b5cf6;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px">{{ m.mata_kuliah }}</div>
                            <h4 style="font-size:15px;font-weight:700;color:#0f172a;line-height:1.4;margin:0 0 6px">{{ m.nama_modul }}</h4>
                            <div style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px">
                                <i class="fa fa-user"></i> {{ m.user ? m.user.name : 'Dosen' }}
                            </div>
                        </div>
                    </div>
                    <p v-if="m.deskripsi" style="font-size:13px;color:#64748b;line-height:1.6;margin:0 0 16px;flex:1">{{ m.deskripsi }}</p>
                    <div style="border-top:1px dashed #e2e8f0;padding-top:16px;display:flex;align-items:center;justify-content:space-between">
                        <span style="font-size:11px;color:#94a3b8"><i class="fa fa-calendar"></i> {{ formatShortDate(m.created_at) }}</span>
                        <a :href="`/storage-public/${m.file_path}`" target="_blank" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#8b5cf6;text-decoration:none;background:#f5f3ff;padding:6px 12px;border-radius:6px;transition:all .2s" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#f5f3ff'">
                            <i class="fa fa-download"></i> Unduh
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JADWAL SERTIFIKASI -->
    <section class="section" id="jadwal-sertifikasi" style="background:#fff;border-bottom:1px solid #f1f5f9">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Agenda Mendatang</div>
                <h2 class="section-title">Jadwal Sertifikasi</h2>
                <p class="section-desc">Jadwal pelaksanaan uji kompetensi sertifikasi yang akan datang dalam waktu dekat.</p>
            </div>
            <p v-if="!jadwalSertifikasi || jadwalSertifikasi.length === 0" style="text-align:center;color:#94a3b8;font-size:15px">Belum ada jadwal sertifikasi yang tersedia.</p>
            <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px">
                <div v-for="j in jadwalSertifikasi" :key="j.id" style="background:#f8fafc;border-radius:16px;padding:20px;border:1px solid #e2e8f0;transition:all .3s" onmouseover="this.style.borderColor='#cbd5e1';this.style.boxShadow='0 4px 12px rgba(0,0,0,.05)'" onmouseout="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                    <div style="font-size:11px;font-weight:700;color:var(--color-primary);text-transform:uppercase;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                        <i class="fa fa-calendar-alt"></i> {{ formatShortDate(j.tanggal) }}
                    </div>
                    <h4 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:8px;line-height:1.3">{{ j.skema?.nama_skema || 'Skema Umum' }}</h4>
                    <p style="font-size:13px;color:#64748b;margin-bottom:16px;display:flex;align-items:center;gap:12px">
                        <span v-if="j.jam_mulai && j.jam_selesai"><i class="fa fa-clock" style="margin-right:4px"></i> {{ j.jam_mulai.substring(0,5) }} - {{ j.jam_selesai.substring(0,5) }}</span>
                        <span><i class="fa fa-users" style="margin-right:4px"></i> {{ j.pendaftarans_count || 0 }} / {{ j.kuota }}</span>
                    </p>
                    <Link href="/login" style="display:block;text-align:center;padding:10px 16px;background:white;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;font-weight:600;color:#475569;text-decoration:none;transition:all .2s" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                        Daftar Sertifikasi
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- SKEMA SERTIFIKASI -->
    <section class="section" id="skema-sertifikasi" style="background:#f8fafc">
        <div class="section-inner">
            <div class="section-header">
                <div class="section-tag">Sertifikasi</div>
                <h2 class="section-title">Skema Sertifikasi</h2>
                <p class="section-desc">Pilih skema uji kompetensi yang tersedia di Laboratorium Komputer STMIK Samarinda.</p>
            </div>
            <p v-if="skemas.length === 0" style="text-align:center;color:#94a3b8;font-size:15px">Belum ada skema sertifikasi yang tersedia.</p>
            <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px">
                <div v-for="s in skemas" :key="s.id" style="background:#fff;border-radius:16px;padding:24px;border:1px solid #e2e8f0;transition:all .3s;box-shadow:0 2px 8px rgba(0,0,0,.04)" onmouseover="this.style.boxShadow='0 8px 24px rgba(var(--color-primary-rgb),.12)';this.style.borderColor='#a5b4fc'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,.04)';this.style.borderColor='#e2e8f0'">
                    <img v-if="s.logo" :src="`/storage-public/${s.logo}`" :alt="s.nama_skema" style="width:52px;height:52px;border-radius:14px;object-fit:contain;border:1px solid #e2e8f0;background:#f8fafc;padding:6px;margin-bottom:14px;display:block">
                    <div v-else style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                        <i class="fa fa-certificate" style="color:#fff;font-size:20px"></i>
                    </div>
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:8px;line-height:1.3">{{ s.nama_skema }}</h4>
                    <p style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:14px">{{ getLimitString(s.deskripsi, 90) }}</p>
                    <Link href="/login" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--color-primary);text-decoration:none">
                        Daftar Sekarang <i class="fa fa-arrow-right" style="font-size:11px"></i>
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="cta-inner">
            <h2>Siap Menggunakan SIMLAB?</h2>
            <p>Bergabung dengan seluruh civitas akademika STMIK Samarinda dan nikmati kemudahan layanan laboratorium secara digital.</p>
            <div class="cta-buttons">
                <template v-if="auth.user">
                    <Link :href="getDashboardRoute()" class="hero-btn-primary"><i class="fa fa-th-large"></i> Buka Dashboard</Link>
                </template>
                <template v-else>
                    <Link href="/register" class="hero-btn-primary"><i class="fa fa-user-plus"></i> Daftar Gratis</Link>
                    <Link href="/login" class="hero-btn-secondary"><i class="fa fa-sign-in-alt"></i> Masuk ke Akun</Link>
                </template>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer" id="kontak">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img v-if="siteSetting?.logo" :src="`/storage-public/${siteSetting.logo}`" alt="Logo" style="width:42px;height:42px;border-radius:10px;object-fit:contain">
                    <div v-else class="footer-logo-icon">SL</div>
                    <div class="footer-logo-text">
                        <strong>{{ siteSetting?.nama_website || 'SIMLAB STMIK' }}</strong>
                        <span>{{ siteSetting?.nama_kampus || 'Samarinda' }}</span>
                    </div>
                </div>
                <p>{{ siteSetting?.deskripsi || `Sistem Informasi Layanan Laboratorium ${siteSetting?.nama_kampus || 'STMIK Samarinda'} — mendigitalisasi seluruh layanan lab untuk civitas akademika.` }}</p>
                <div class="footer-social">
                    <a :href="siteSetting?.facebook || '#'" class="social-btn" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a :href="siteSetting?.instagram || '#'" class="social-btn" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a :href="siteSetting?.twitter || '#'" class="social-btn" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a :href="siteSetting?.youtube || '#'" class="social-btn" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h5>Layanan</h5>
                <ul class="footer-links">
                    <li><a href="#">Sertifikasi Kompetensi</a></li>
                    <li><a href="#">Izin Penelitian</a></li>
                    <li><a href="#">Peminjaman Lab</a></li>
                    <li><a href="#">Surat Bebas Lab</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Informasi</h5>
                <ul class="footer-links">
                    <li><a href="#">Tentang Lab</a></li>
                    <li><a href="#">Berita &amp; Pengumuman</a></li>
                    <li><a href="#">Modul Praktikum</a></li>
                    <li><a href="#">Struktur Organisasi</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Kontak</h5>
                <ul class="footer-links">
                    <li><a href="#"><i class="fa fa-map-marker-alt" style="width:16px;"></i> {{ siteSetting?.alamat || 'Samarinda, Kalimantan Timur' }}</a></li>
                    <li><a href="#"><i class="fa fa-phone" style="width:16px;"></i> {{ siteSetting?.telepon || '(0541) xxx-xxxx' }}</a></li>
                    <li><a :href="siteSetting?.email_kontak ? 'mailto:'+siteSetting.email_kontak : '#'"><i class="fa fa-envelope" style="width:16px;"></i> {{ siteSetting?.email_kontak || 'lab@stmik-samarinda.ac.id' }}</a></li>
                    <li><a href="#"><i class="fa fa-clock" style="width:16px;"></i> {{ siteSetting?.jam_operasional || 'Senin–Jumat, 08.00–16.00' }}</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© {{ new Date().getFullYear() }} {{ siteSetting?.nama_website || 'SIMLAB' }} — Sistem Informasi Manajemen Lab {{ siteSetting?.nama_kampus || 'STMIK Samarinda' }}. All rights reserved.</p>
        </div>
    </footer>
</div>
</template>

<style scoped>
/* Copied exactly from blade styles */
* { margin: 0; padding: 0; box-sizing: border-box; }
.welcome-page { font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; background: #fff; }
.navbar { position: fixed; top: 0; width: 100%; z-index: 1000; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(99, 102, 241, 0.1); box-shadow: 0 1px 20px rgba(0, 0, 0, 0.06); transition: box-shadow 0.3s ease; }
.navbar-inner { max-width: 1200px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 68px; }
.navbar-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.navbar-logo { width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; font-weight: 800; }
.navbar-title strong { display: block; font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.2; }
.navbar-title span { font-size: 11px; color: #64748b; }
.navbar-menu { display: flex; align-items: center; gap: 8px; list-style: none; margin: 0; padding: 0; }
.navbar-menu a { padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; color: #475569; transition: all 0.2s; }
.navbar-menu a:hover { background: #f1f5f9; color: var(--color-primary); }
.navbar-actions { display: flex; align-items: center; gap: 10px; }
.btn-outline { padding: 8px 20px; border-radius: 8px; border: 1.5px solid var(--color-primary); color: var(--color-primary); font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-outline:hover { background: var(--color-primary); color: white; }
.btn-primary { padding: 8px 20px; border-radius: 8px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); color: white; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; box-shadow: 0 4px 12px rgba(var(--color-primary-rgb), 0.3); }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(var(--color-primary-rgb), 0.4); }
.hero { min-height: 100vh; background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); display: flex; align-items: center; padding-top: 68px; position: relative; overflow: hidden; }
.hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 30% 50%, rgba(var(--color-primary-rgb), 0.3) 0%, transparent 60%), radial-gradient(ellipse at 80% 20%, rgba(var(--color-secondary-rgb), 0.25) 0%, transparent 50%); }
.hero-inner { max-width: 1200px; margin: 0 auto; padding: 80px 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; position: relative; z-index: 1; }
.hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50px; padding: 6px 16px; margin-bottom: 24px; color: rgba(255, 255, 255, 0.85); font-size: 13px; font-weight: 500; }
.hero-badge span { width: 8px; height: 8px; border-radius: 50%; background: #10b981; animation: pulse2 2s infinite; }
@keyframes pulse2 { 0%, 100% { opacity: 1; transform: scale(1) } 50% { opacity: 0.5; transform: scale(1.3) } }
.hero h1 { font-size: clamp(36px, 5vw, 56px); font-weight: 800; line-height: 1.15; color: white; margin-bottom: 20px; }
.hero h1 .accent { background: linear-gradient(135deg, #a78bfa, #60a5fa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.hero p { font-size: 17px; line-height: 1.75; color: rgba(255, 255, 255, 0.75); margin-bottom: 36px; max-width: 480px; }
.hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }
.hero-btn-primary { padding: 14px 32px; border-radius: 12px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); color: white; font-size: 15px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px rgba(var(--color-primary-rgb), 0.4); transition: all 0.3s; }
.hero-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(var(--color-primary-rgb), 0.5); color: white; }
.hero-btn-secondary { padding: 14px 32px; border-radius: 12px; border: 1.5px solid rgba(255, 255, 255, 0.3); color: white; font-size: 15px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.05); transition: all 0.3s; }
.hero-btn-secondary:hover { background: rgba(255, 255, 255, 0.12); color: white; }
.hero-cards { display: flex; flex-direction: column; gap: 14px; }
.hero-card { background: rgba(255, 255, 255, 0.07); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; padding: 18px 22px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; }
.hero-card:hover { background: rgba(255, 255, 255, 0.12); transform: translateX(4px); }
.hero-card-icon { width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.hero-card-text strong { display: block; color: white; font-size: 14px; font-weight: 600; }
.hero-card-text span { color: rgba(255, 255, 255, 0.55); font-size: 12px; }
.stats-bar { background: white; border-bottom: 1px solid #e2e8f0; padding: 28px 24px; }
.stats-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.stat-item { text-align: center; }
.stat-number { font-size: 32px; font-weight: 800; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.stat-label { font-size: 13px; color: #64748b; font-weight: 500; margin-top: 4px; }
.section { padding: 80px 24px; }
.section-inner { max-width: 1200px; margin: 0 auto; }
.section-header { text-align: center; margin-bottom: 56px; }
.section-tag { display: inline-block; background: linear-gradient(135deg, rgba(var(--color-primary-rgb), 0.1), rgba(var(--color-secondary-rgb), 0.1)); color: var(--color-primary); font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: 6px 16px; border-radius: 50px; border: 1px solid rgba(var(--color-primary-rgb), 0.2); margin-bottom: 16px; }
.section-title { font-size: clamp(28px, 4vw, 40px); font-weight: 800; color: #0f172a; line-height: 1.2; margin-bottom: 16px; }
.section-desc { font-size: 16px; color: #64748b; max-width: 560px; margin: 0 auto; line-height: 1.7; }
.services-bg { background: #f8fafc; }
.services-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.service-card { background: white; border-radius: 20px; padding: 32px; border: 1px solid #e2e8f0; transition: all 0.3s; position: relative; overflow: hidden; }
.service-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--card-gradient); }
.service-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); border-color: transparent; }
.service-icon { width: 64px; height: 64px; border-radius: 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; background: var(--icon-bg); }
.service-card h3 { font-size: 19px; font-weight: 700; color: #0f172a; margin-bottom: 10px; }
.service-card p { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 20px; }
.service-features { list-style: none; display: flex; flex-direction: column; gap: 8px; padding: 0; margin: 0; }
.service-features li { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; font-weight: 500; }
.service-features li i { color: var(--check-color); font-size: 12px; }
.steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; position: relative; }
.steps-grid::before { content: ''; position: absolute; top: 40px; left: 10%; right: 10%; height: 2px; background: linear-gradient(to right, var(--color-primary), var(--color-secondary)); z-index: 0; }
.step-card { text-align: center; position: relative; z-index: 1; }
.step-number { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; font-weight: 800; box-shadow: 0 8px 24px rgba(var(--color-primary-rgb), 0.3); }
.step-card h4 { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.step-card p { font-size: 13px; color: #64748b; line-height: 1.6; }
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
.about-img { border-radius: 24px; overflow: hidden; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); height: 420px; display: flex; align-items: center; justify-content: center; }
.about-img-content { text-align: center; color: white; }
.about-img-icon { font-size: 80px; margin-bottom: 16px; }
.about-img-text { font-size: 16px; font-weight: 600; opacity: 0.8; }
.badge { padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 600; background: #f1f5f9; color: var(--color-primary); border: 1px solid rgba(var(--color-primary-rgb), 0.2); margin: 0 4px 8px 0; display: inline-block; }
.about-text h2 { font-size: 36px; font-weight: 800; color: #0f172a; margin-bottom: 16px; line-height: 1.2; }
.about-text p { font-size: 15px; color: #64748b; line-height: 1.8; margin-bottom: 16px; }
.about-list { list-style: none; margin-bottom: 28px; padding: 0; }
.about-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #475569; margin-bottom: 10px; }
.about-list li i { color: #10b981; margin-top: 2px; flex-shrink: 0; }
.news-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.news-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; transition: all 0.3s; }
.news-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08); }
.news-img { height: 180px; background: var(--ng); display: flex; align-items: center; justify-content: center; font-size: 52px; }
.news-body { padding: 20px; }
.news-tag { display: inline-block; padding: 3px 10px; border-radius: 50px; background: rgba(var(--color-primary-rgb), 0.1); color: var(--color-primary); font-size: 11px; font-weight: 600; margin-bottom: 10px; }
.news-body h4 { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 8px; line-height: 1.4; }
.news-body p { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 12px; }
.news-meta { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 6px; }
.cta-section { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); padding: 80px 24px; text-align: center; position: relative; overflow: hidden; }
.cta-section::before { content: ''; position: absolute; inset: 0; background: radial-gradient(ellipse at 50% 50%, rgba(var(--color-primary-rgb), 0.4) 0%, transparent 70%); }
.cta-inner { max-width: 700px; margin: 0 auto; position: relative; z-index: 1; }
.cta-inner h2 { font-size: clamp(28px, 4vw, 44px); font-weight: 800; color: white; margin-bottom: 16px; }
.cta-inner p { font-size: 16px; color: rgba(255, 255, 255, 0.7); margin-bottom: 36px; line-height: 1.7; }
.cta-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.footer { background: #0f172a; color: rgba(255, 255, 255, 0.7); padding: 60px 24px 0; }
.footer-grid { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; padding-bottom: 48px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
.footer-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
.footer-logo-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-size: 15px; font-weight: 800; }
.footer-logo-text strong { display: block; color: white; font-size: 14px; font-weight: 700; }
.footer-logo-text span { font-size: 11px; color: rgba(255, 255, 255, 0.5); }
.footer-brand p { font-size: 13px; line-height: 1.8; max-width: 280px; margin-bottom: 20px; }
.footer-social { display: flex; gap: 10px; }
.social-btn { width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.7); display: flex; align-items: center; justify-content: center; font-size: 14px; text-decoration: none; transition: all 0.2s; }
.social-btn:hover { background: var(--color-primary); color: white; }
.footer-col h5 { color: white; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
.footer-links { list-style: none; padding: 0; margin: 0; }
.footer-links li { margin-bottom: 8px; }
.footer-links a { color: rgba(255, 255, 255, 0.6); font-size: 13px; text-decoration: none; transition: color 0.2s; }
.footer-links a:hover { color: #a78bfa; }
.footer-bottom { max-width: 1200px; margin: 0 auto; padding: 20px 0; text-align: center; font-size: 13px; color: rgba(255, 255, 255, 0.4); }

@media (max-width: 900px) {
    .hero-inner { grid-template-columns: 1fr; gap: 40px; text-align: center; }
    .hero p { max-width: 100%; }
    .hero-buttons { justify-content: center; }
    .hero-cards { display: none; }
    .services-grid { grid-template-columns: 1fr; }
    .about-grid { grid-template-columns: 1fr; }
    .news-grid { grid-template-columns: 1fr; }
    .steps-grid { grid-template-columns: repeat(2, 1fr); }
    .steps-grid::before { display: none; }
    .stats-inner { grid-template-columns: repeat(2, 1fr); }
    .footer-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 640px) {
    .navbar-menu { display: none; }
    .footer-grid { grid-template-columns: 1fr; }
    .steps-grid { grid-template-columns: 1fr 1fr; }
}

@keyframes fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
.fade-in { animation: fadeInUp 0.6s ease both; }
.fade-in-1 { animation-delay: 0.1s; }
.fade-in-2 { animation-delay: 0.2s; }
.fade-in-3 { animation-delay: 0.3s; }
.fade-in-4 { animation-delay: 0.4s; }
</style>
