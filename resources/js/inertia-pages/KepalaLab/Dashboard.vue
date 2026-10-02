<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

defineProps({
  totalSertifikasi: Number,
  totalIzin: Number,
  totalPeminjaman: Number,
  totalSurat: Number,
  pendingSertifikasi: Array,
  pendingIzin: Array,
  pendingPeminjaman: Array,
  pendingSurat: Array,
})
</script>

<template>
  <AppLayout title="Dashboard Kepala Lab">
    <p class="page-title">Panel Kepala Laboratorium</p>
    <p class="page-sub">Kelola dan validasi semua permohonan layanan laboratorium.</p>

    <div class="menu-grid">
        <Link href="/kepala_lab/sertifikasi" class="menu-card" style="--mc-color:linear-gradient(90deg,#4f46e5,#7c3aed);--mc-bg:rgba(79,70,229,.1);--mc-text:#4f46e5">
            <div class="menu-card-icon">🏆</div>
            <h4>Validasi Sertifikasi</h4>
            <p>Validasi dan setujui pendaftaran sertifikasi kompetensi mahasiswa.</p>
            <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Validasi</div>
        </Link>
        <Link href="/kepala_lab/izin-penelitian" class="menu-card" style="--mc-color:linear-gradient(90deg,#10b981,#059669);--mc-bg:rgba(16,185,129,.1);--mc-text:#059669">
            <div class="menu-card-icon">🔬</div>
            <h4>Izin Penelitian</h4>
            <p>Setujui atau tolak permohonan izin penelitian yang telah diverifikasi admin.</p>
            <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Validasi</div>
        </Link>
        <Link href="/kepala_lab/peminjaman-lab" class="menu-card" style="--mc-color:linear-gradient(90deg,#f59e0b,#d97706);--mc-bg:rgba(245,158,11,.1);--mc-text:#d97706">
            <div class="menu-card-icon">🏫</div>
            <h4>Peminjaman Lab</h4>
            <p>Setujui atau tolak permohonan peminjaman ruangan laboratorium.</p>
            <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Validasi</div>
        </Link>
        <Link href="/kepala_lab/surat-bebas-lab" class="menu-card" style="--mc-color:linear-gradient(90deg,#ef4444,#dc2626);--mc-bg:rgba(239,68,68,.1);--mc-text:#ef4444">
            <div class="menu-card-icon">📄</div>
            <h4>Surat Bebas Lab</h4>
            <p>Tandatangani dan setujui surat keterangan bebas lab mahasiswa.</p>
            <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Validasi</div>
        </Link>
        <Link href="/admin/berita" class="menu-card" style="--mc-color:linear-gradient(90deg,#f59e0b,#d97706);--mc-bg:rgba(245,158,11,.1);--mc-text:#d97706">
            <div class="menu-card-icon">📰</div>
            <h4>Berita &amp; Pengumuman</h4>
            <p>Buat dan kelola berita serta pengumuman yang tampil di halaman publik.</p>
            <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
        </Link>
        <Link href="/kepala_lab/struktur" class="menu-card" style="--mc-color:linear-gradient(90deg,#06b6d4,#0891b2);--mc-bg:rgba(6,182,212,.1);--mc-text:#0891b2">
            <div class="menu-card-icon">👥</div>
            <h4>Struktur Organisasi</h4>
            <p>Kelola susunan organisasi laboratorium yang tampil di halaman publik.</p>
            <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
        </Link>
    </div>
  </AppLayout>
</template>
