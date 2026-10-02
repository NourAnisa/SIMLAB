<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

defineProps({
  totalPeminjaman: Number,
  totalDisetujui: Number,
  totalDitolak: Number,
  totalMenunggu: Number,
  riwayatPeminjaman: Array,
})
</script>

<template>
  <AppLayout title="Dashboard Dosen">
    <p class="page-title">Panel Dosen SILAB</p>
    <p class="page-sub">Pantau peminjaman laboratorium dan kelola modul praktikum Anda.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:28px">
      <div style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div style="background:rgba(79,70,229,.1);color:#4f46e5;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-door-open"></i>
        </div>
        <div>
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ totalPeminjaman }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Total Peminjaman</span>
        </div>
      </div>

      <div style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div style="background:rgba(16,185,129,.1);color:#10b981;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-check-circle"></i>
        </div>
        <div>
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ totalDisetujui }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Peminjaman Disetujui</span>
        </div>
      </div>

      <div style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div style="background:rgba(239,68,68,.1);color:#ef4444;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-times-circle"></i>
        </div>
        <div>
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ totalDitolak }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Peminjaman Ditolak</span>
        </div>
      </div>

      <div style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div style="background:rgba(245,158,11,.1);color:#d97706;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-clock"></i>
        </div>
        <div>
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ totalMenunggu }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Menunggu Verifikasi</span>
        </div>
      </div>
    </div>

    <div class="menu-grid">
      <Link href="/dosen/peminjaman-lab" class="menu-card" style="--mc-color:linear-gradient(90deg,#f59e0b,#d97706);--mc-bg:rgba(245,158,11,.1);--mc-text:#d97706">
        <div class="menu-card-icon">🏫</div>
        <h4>Peminjaman Lab</h4>
        <p>Ajukan dan pantau status permohonan peminjaman ruangan laboratorium.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      
      <Link href="/dosen/modul" class="menu-card" style="--mc-color:linear-gradient(90deg,#06b6d4,#0891b2);--mc-bg:rgba(6,182,212,.1);--mc-text:#0891b2">
        <div class="menu-card-icon">📚</div>
        <h4>Modul Praktikum</h4>
        <p>Unggah dan kelola modul praktikum yang dapat diakses mahasiswa.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>

      <Link href="/dosen/izin-penelitian" class="menu-card" style="--mc-color:linear-gradient(90deg,#10b981,#059669);--mc-bg:rgba(16,185,129,.1);--mc-text:#10b981">
        <div class="menu-card-icon">🔬</div>
        <h4>Izin Penelitian</h4>
        <p>Ajukan izin penelitian laboratorium untuk penelitian Anda.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Segera Hadir</div>
      </Link>
    </div>
  </AppLayout>
</template>
