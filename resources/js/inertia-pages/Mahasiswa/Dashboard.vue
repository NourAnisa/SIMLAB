<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

const props = defineProps({
  pendingSertifikasi: Number,
  pendingIzin: Number,
  pendingPeminjaman: Number,
  pendingSurat: Number,
  logAktivitas: Array,
  profileCheck: Object,
})

const page = usePage()
const auth = computed(() => page.props.auth)

const cardsLocked = computed(() => !props.profileCheck?.can_access)
</script>

<template>
  <AppLayout title="Dashboard Mahasiswa">
    <p class="page-title">Halo, {{ auth.user?.name }}!</p>
    <p class="page-sub">Selamat datang di SILAB - akses semua layanan laboratorium di bawah ini.</p>

    <div
      v-if="cardsLocked"
      class="dashboard-warning"
      style="background:#fef3c7;border:1.5px solid #fcd34d;border-radius:12px;padding:16px;margin-bottom:24px;display:flex;align-items:flex-start;gap:12px"
    >
      <div style="flex-shrink:0">
        <i class="fa fa-exclamation-circle" style="font-size:24px;color:#f59e0b"></i>
      </div>
      <div style="flex:1">
        <h4 style="margin:0 0 6px;color:#92400e;font-weight:700">Profil Belum Lengkap</h4>
        <p style="margin:0 0 12px;font-size:13px;color:#b45309;line-height:1.5">
          Untuk mengakses Sertifikasi Kompetensi, Izin Penelitian, Peminjaman Lab, dan Surat Bebas Lab,
          Anda harus melengkapi profil terlebih dahulu. Data yang masih kosong:
          <br><strong>{{ profileCheck.missing_fields.join(', ') }}</strong>
        </p>
        <Link
          href="/profile"
          style="display:inline-block;padding:8px 16px;background:#f59e0b;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;cursor:pointer"
        >
          <i class="fa fa-arrow-right" style="margin-right:5px"></i>Lengkapi Profil Sekarang
        </Link>
      </div>
    </div>

    <div
      class="stats-row"
      style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:28px"
    >
      <div class="stat-card" style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div class="stat-card-icon" style="background:rgba(79,70,229,.1);color:#4f46e5;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-award"></i>
        </div>
        <div class="stat-card-body">
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ pendingSertifikasi }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Sertifikasi Menunggu</span>
        </div>
      </div>

      <div class="stat-card" style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div class="stat-card-icon" style="background:rgba(16,185,129,.1);color:#10b981;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-flask"></i>
        </div>
        <div class="stat-card-body">
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ pendingIzin }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Izin Menunggu</span>
        </div>
      </div>

      <div class="stat-card" style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div class="stat-card-icon" style="background:rgba(245,158,11,.1);color:#d97706;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-building"></i>
        </div>
        <div class="stat-card-body">
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ pendingPeminjaman }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Peminjaman Menunggu</span>
        </div>
      </div>

      <div class="stat-card" style="background:#fff;border-radius:16px;padding:22px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:16px">
        <div class="stat-card-icon" style="background:rgba(239,68,68,.1);color:#ef4444;width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px">
          <i class="fa fa-file-lines"></i>
        </div>
        <div class="stat-card-body">
          <strong style="display:block;font-size:22px;font-weight:800;color:#0f172a">{{ pendingSurat }}</strong>
          <span style="font-size:12px;color:#64748b;font-weight:500">Surat Bebas Menunggu</span>
        </div>
      </div>
    </div>

    <div class="menu-grid mahasiswa-menu-grid">
      <component
        :is="cardsLocked ? 'div' : Link"
        :href="cardsLocked ? undefined : '/mahasiswa/sertifikasi'"
        class="menu-card"
        :class="{ 'menu-card-locked': cardsLocked }"
        :aria-disabled="cardsLocked ? 'true' : 'false'"
        style="--mc-color:linear-gradient(90deg,#4f46e5,#7c3aed);--mc-bg:rgba(79,70,229,.1);--mc-text:#4f46e5"
      >
        <div v-if="cardsLocked" class="menu-card-status"><i class="fa fa-lock"></i> Menu Terkunci</div>
        <div class="menu-card-icon"><i class="fa fa-award"></i></div>
        <h4>Sertifikasi Kompetensi</h4>
        <p>Daftar ujian sertifikasi lab, pantau status pendaftaran dan hasil ujian.</p>
        <div v-if="cardsLocked" class="menu-card-lock-note">
          <i class="fa fa-lock"></i> Lengkapi profil untuk akses
        </div>
        <div v-else class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Lihat Layanan</div>
      </component>

      <component
        :is="cardsLocked ? 'div' : Link"
        :href="cardsLocked ? undefined : '/mahasiswa/izin-penelitian'"
        class="menu-card"
        :class="{ 'menu-card-locked': cardsLocked }"
        :aria-disabled="cardsLocked ? 'true' : 'false'"
        style="--mc-color:linear-gradient(90deg,#10b981,#059669);--mc-bg:rgba(16,185,129,.1);--mc-text:#10b981"
      >
        <div v-if="cardsLocked" class="menu-card-status"><i class="fa fa-lock"></i> Menu Terkunci</div>
        <div class="menu-card-icon"><i class="fa fa-flask"></i></div>
        <h4>Izin Penelitian</h4>
        <p>Ajukan izin penggunaan lab untuk penelitian skripsi atau tugas akhir.</p>
        <div v-if="cardsLocked" class="menu-card-lock-note">
          <i class="fa fa-lock"></i> Lengkapi profil untuk akses
        </div>
        <div v-else class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Lihat Layanan</div>
      </component>

      <component
        :is="cardsLocked ? 'div' : Link"
        :href="cardsLocked ? undefined : '/mahasiswa/peminjaman-lab'"
        class="menu-card"
        :class="{ 'menu-card-locked': cardsLocked }"
        :aria-disabled="cardsLocked ? 'true' : 'false'"
        style="--mc-color:linear-gradient(90deg,#f59e0b,#d97706);--mc-bg:rgba(245,158,11,.1);--mc-text:#d97706"
      >
        <div v-if="cardsLocked" class="menu-card-status"><i class="fa fa-lock"></i> Menu Terkunci</div>
        <div class="menu-card-icon"><i class="fa fa-building"></i></div>
        <h4>Peminjaman Lab</h4>
        <p>Booking ruangan lab untuk kegiatan kelompok atau praktikum mandiri.</p>
        <div v-if="cardsLocked" class="menu-card-lock-note">
          <i class="fa fa-lock"></i> Lengkapi profil untuk akses
        </div>
        <div v-else class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Lihat Layanan</div>
      </component>

      <component
        :is="cardsLocked ? 'div' : Link"
        :href="cardsLocked ? undefined : '/mahasiswa/surat-bebas-lab'"
        class="menu-card"
        :class="{ 'menu-card-locked': cardsLocked }"
        :aria-disabled="cardsLocked ? 'true' : 'false'"
        style="--mc-color:linear-gradient(90deg,#ef4444,#dc2626);--mc-bg:rgba(239,68,68,.1);--mc-text:#ef4444"
      >
        <div v-if="cardsLocked" class="menu-card-status"><i class="fa fa-lock"></i> Menu Terkunci</div>
        <div class="menu-card-icon"><i class="fa fa-file-lines"></i></div>
        <h4>Surat Bebas Lab</h4>
        <p>Ajukan surat keterangan bebas lab untuk keperluan wisuda atau yudisium.</p>
        <div v-if="cardsLocked" class="menu-card-lock-note">
          <i class="fa fa-lock"></i> Lengkapi profil untuk akses
        </div>
        <div v-else class="menu-card-arrow"><i class="fa fa-arrow-right"></i> Lihat Layanan</div>
      </component>
    </div>
  </AppLayout>
</template>

<style scoped>
.mahasiswa-menu-grid .menu-card.menu-card-locked {
  position: relative;
  opacity: .72;
  cursor: not-allowed;
  pointer-events: none;
  filter: saturate(.82) grayscale(.08);
  border: 1px solid #fcd34d;
  box-shadow: 0 10px 28px rgba(245, 158, 11, .12);
}

.mahasiswa-menu-grid .menu-card.menu-card-locked .menu-card-icon,
.mahasiswa-menu-grid .menu-card.menu-card-locked h4,
.mahasiswa-menu-grid .menu-card.menu-card-locked p {
  opacity: .9;
}

.menu-card-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 12px;
  padding: 6px 10px;
  border-radius: 999px;
  background: #fff7ed;
  color: #c2410c;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .02em;
  text-transform: uppercase;
}

.menu-card-lock-note {
  margin-top: 10px;
  padding: 10px 12px;
  background: #fffbeb;
  border: 1px dashed #fcd34d;
  border-radius: 8px;
  font-size: 12px;
  color: #92400e;
  font-weight: 600;
}
</style>
