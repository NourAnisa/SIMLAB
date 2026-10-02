<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

const props = defineProps({
  totalSertifikasi: Number,
  totalIzin: Number,
  totalPeminjaman: Number,
  totalSurat: Number,
  jadwalHariIni: Array
})
</script>

<template>
  <AppLayout title="Dashboard Admin">
    <p class="page-title">Panel Admin SILAB</p>
    <p class="page-sub">Kelola semua permohonan dan data layanan laboratorium.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:24px">
      <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.05);border-bottom:4px solid #4f46e5;display:flex;align-items:center;gap:16px">
        <div style="width:50px;height:50px;border-radius:12px;background:rgba(79,70,229,.1);color:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:24px">
          <i class="fa fa-certificate"></i>
        </div>
        <div>
          <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1">{{ totalSertifikasi }}</div>
          <div style="font-size:13px;color:#64748b;margin-top:4px;font-weight:600">Sertifikasi Baru</div>
        </div>
      </div>
      
      <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.05);border-bottom:4px solid #06b6d4;display:flex;align-items:center;gap:16px">
        <div style="width:50px;height:50px;border-radius:12px;background:rgba(6,182,212,.1);color:#06b6d4;display:flex;align-items:center;justify-content:center;font-size:24px">
          <i class="fa fa-microscope"></i>
        </div>
        <div>
          <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1">{{ totalIzin }}</div>
          <div style="font-size:13px;color:#64748b;margin-top:4px;font-weight:600">Izin Penelitian Baru</div>
        </div>
      </div>

      <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.05);border-bottom:4px solid #f59e0b;display:flex;align-items:center;gap:16px">
        <div style="width:50px;height:50px;border-radius:12px;background:rgba(245,158,11,.1);color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:24px">
          <i class="fa fa-school"></i>
        </div>
        <div>
          <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1">{{ totalPeminjaman }}</div>
          <div style="font-size:13px;color:#64748b;margin-top:4px;font-weight:600">Peminjaman Lab Baru</div>
        </div>
      </div>

      <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.05);border-bottom:4px solid #ef4444;display:flex;align-items:center;gap:16px">
        <div style="width:50px;height:50px;border-radius:12px;background:rgba(239,68,68,.1);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:24px">
          <i class="fa fa-file-alt"></i>
        </div>
        <div>
          <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1">{{ totalSurat }}</div>
          <div style="font-size:13px;color:#64748b;margin-top:4px;font-weight:600">Surat Bebas Lab Baru</div>
        </div>
      </div>
    </div>

    <div class="menu-grid">
      <Link href="/admin/sertifikasi" class="menu-card" style="--mc-color:linear-gradient(90deg,#4f46e5,#7c3aed);--mc-bg:rgba(79,70,229,.1);--mc-text:#4f46e5">
        <div class="menu-card-icon">🏆</div>
        <h4>Manajemen Sertifikasi</h4>
        <p>Kelola pendaftaran dan proses sertifikasi kompetensi mahasiswa.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/skema" class="menu-card" style="--mc-color:linear-gradient(90deg,#7c3aed,#6d28d9);--mc-bg:rgba(124,58,237,.1);--mc-text:#7c3aed">
        <div class="menu-card-icon">📋</div>
        <h4>Skema Sertifikasi</h4>
        <p>Atur skema dan kategori ujian sertifikasi yang tersedia.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/jadwal" class="menu-card" style="--mc-color:linear-gradient(90deg,#10b981,#059669);--mc-bg:rgba(16,185,129,.1);--mc-text:#10b981">
        <div class="menu-card-icon">📅</div>
        <h4>Jadwal Sertifikasi</h4>
        <p>Buat dan kelola jadwal pelaksanaan ujian sertifikasi.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/peminjaman-lab" class="menu-card" style="--mc-color:linear-gradient(90deg,#f59e0b,#d97706);--mc-bg:rgba(245,158,11,.1);--mc-text:#d97706">
        <div class="menu-card-icon">🏫</div>
        <h4>Peminjaman Lab</h4>
        <p>Proses dan kelola permohonan peminjaman ruangan laboratorium.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/izin-penelitian" class="menu-card" style="--mc-color:linear-gradient(90deg,#06b6d4,#0891b2);--mc-bg:rgba(6,182,212,.1);--mc-text:#0891b2">
        <div class="menu-card-icon">🔬</div>
        <h4>Izin Penelitian</h4>
        <p>Proses permohonan izin penelitian mahasiswa dan dosen.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/surat-bebas-lab" class="menu-card" style="--mc-color:linear-gradient(90deg,#ef4444,#dc2626);--mc-bg:rgba(239,68,68,.1);--mc-text:#ef4444">
        <div class="menu-card-icon">📄</div>
        <h4>Surat Bebas Lab</h4>
        <p>Verifikasi dan proses permohonan surat keterangan bebas lab.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/users" class="menu-card" style="--mc-color:linear-gradient(90deg,#64748b,#475569);--mc-bg:rgba(100,116,139,.1);--mc-text:#475569">
        <div class="menu-card-icon">👥</div>
        <h4>Manajemen User</h4>
        <p>Kelola akun pengguna dan atur hak akses masing-masing role.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Kelola</div>
      </Link>
      <Link href="/admin/site-setting" class="menu-card" style="--mc-color:linear-gradient(90deg,#0ea5e9,#0284c7);--mc-bg:rgba(14,165,233,.1);--mc-text:#0284c7">
        <div class="menu-card-icon">🌐</div>
        <h4>Profil Website</h4>
        <p>Atur nama website, nama kampus, dan logo yang tampil di sistem.</p>
        <div class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Atur</div>
      </Link>
      <Link href="/admin/berita" class="menu-card" style="--mc-color:linear-gradient(90deg,#f59e0b,#d97706);--mc-bg:rgba(245,158,11,.1);--mc-text:#d97706">
        <div class="menu-card-icon">📰</div>
        <h4>Berita &amp; Pengumuman</h4>
        <p>Kelola berita dan pengumuman yang tampil di halaman publik.</p>
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
