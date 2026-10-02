<script setup>
/**
 * AppLayout.vue - Inertia Version
 * Menggunakan usePage().props untuk akses data (auth, siteSetting),
 * Link dari inertiajs menggantikan RouterLink,
 * dan useForm/router untuk logout.
 */
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
})

const page = usePage()
const auth = computed(() => page.props.auth)
const user = computed(() => auth.value.user)
const siteSetting = computed(() => page.props.siteSetting || {})
const currentPath = computed(() => page.url)

const pendingCounts = computed(() => page.props.pendingCounts || {})
const totalPending = computed(() => {
  return (pendingCounts.value.sertifikasi || 0) +
         (pendingCounts.value.peminjaman_lab || 0) +
         (pendingCounts.value.izin_penelitian || 0) +
         (pendingCounts.value.surat_bebas_lab || 0)
})

const isNotificationOpen = ref(false)

function toggleNotification() {
  isNotificationOpen.value = !isNotificationOpen.value
}

function closeNotificationDropdown(e) {
  if (!e.target.closest('.notification-wrapper')) {
    isNotificationOpen.value = false
  }
}

const isSidebarOpen = ref(false)

const isMahasiswa = computed(() => user.value?.role === 'mahasiswa')
const isDosen = computed(() => user.value?.role === 'dosen')
const isAdmin = computed(() => user.value?.role === 'admin')
const isKepalaLab = computed(() => user.value?.role === 'kepala_lab')

const isNotificationEnabled = computed(() => isAdmin.value || isKepalaLab.value || isMahasiswa.value || isDosen.value)

const getNotificationSubtitle = (count) => {
    if (isAdmin.value || isKepalaLab.value) {
        return `${count} pengajuan menunggu`
    }
    return `${count} pembaruan status`
}

const getNotificationUrl = (type) => {
    if (isAdmin.value) return `/admin/${type}`
    if (isKepalaLab.value) return `/kepala_lab/${type}`
    if (isDosen.value) return `/dosen/${type}`
    return `/mahasiswa/${type}`
}

onMounted(() => {
  if (siteSetting.value.tema_warna === 'merah') {
    document.body.classList.add('theme-merah')
  } else {
    document.body.classList.remove('theme-merah')
  }
  document.addEventListener('click', closeNotificationDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeNotificationDropdown)
})

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}

function closeSidebar() {
  isSidebarOpen.value = false
}

function handleLogout() {
  router.post('/logout')
}

// Helpers for active links
const isActive = (path) => currentPath.value.startsWith(path)
const isExactActive = (path) => currentPath.value === path
</script>

<template>
  <div class="overlay" :class="{ show: isSidebarOpen }" @click="closeSidebar"></div>

  <!-- SIDEBAR -->
  <aside class="sidebar" :class="{ open: isSidebarOpen }">
      <div class="sidebar-brand">
          <Link href="/" class="sidebar-logo">
              <img v-if="siteSetting.logo" :src="`/storage-public/${siteSetting.logo}`" alt="Logo" style="width:40px;height:40px;border-radius:10px;object-fit:contain;background:#fff;padding:3px;flex-shrink:0">
              <div v-else class="sidebar-logo-icon">
                SL
              </div>
              <div class="sidebar-logo-text">
                  <strong>{{ siteSetting.nama_website || 'SIMLAB STMIK' }}</strong>
                  <span>{{ siteSetting.nama_kampus || 'Samarinda' }}</span>
              </div>
          </Link>
      </div>

      <nav class="sidebar-nav">
          <div class="sidebar-section">
              <div class="sidebar-section-title">Menu Utama</div>
              
              <template v-if="isMahasiswa">
                <Link href="/mahasiswa/dashboard" class="sidebar-link" :class="{ active: isActive('/mahasiswa/dashboard') }">
                    <i class="fa fa-th-large"></i> Dashboard
                </Link>
                <Link href="/mahasiswa/sertifikasi" class="sidebar-link" :class="{ active: isActive('/mahasiswa/sertifikasi') }">
                    <i class="fa fa-certificate"></i> Sertifikasi
                </Link>
                <Link href="/mahasiswa/izin-penelitian" class="sidebar-link" :class="{ active: isActive('/mahasiswa/izin-penelitian') }">
                    <i class="fa fa-flask"></i> Izin Penelitian
                </Link>
                <Link href="/mahasiswa/peminjaman-lab" class="sidebar-link" :class="{ active: isActive('/mahasiswa/peminjaman-lab') }">
                    <i class="fa fa-door-open"></i> Peminjaman Lab
                </Link>
                <Link href="/mahasiswa/surat-bebas-lab" class="sidebar-link" :class="{ active: isActive('/mahasiswa/surat-bebas-lab') }">
                    <i class="fa fa-file-alt"></i> Surat Bebas Lab
                </Link>
              </template>

              <template v-else-if="isDosen">
                <Link href="/dosen/dashboard" class="sidebar-link" :class="{ active: isActive('/dosen/dashboard') }">
                    <i class="fa fa-th-large"></i> Dashboard
                </Link>
                <Link href="/dosen/peminjaman-lab" class="sidebar-link" :class="{ active: isActive('/dosen/peminjaman-lab') }">
                    <i class="fa fa-door-open"></i> Peminjaman Lab
                </Link>
                <Link href="/dosen/modul" class="sidebar-link" :class="{ active: isActive('/dosen/modul') }">
                    <i class="fa fa-book"></i> Modul Praktikum
                </Link>
              </template>

              <template v-else-if="isAdmin">
                <Link href="/admin/dashboard" class="sidebar-link" :class="{ active: isActive('/admin/dashboard') }">
                    <i class="fa fa-th-large"></i> Dashboard
                </Link>
                <Link href="/admin/sertifikasi" class="sidebar-link" :class="{ active: isActive('/admin/sertifikasi') }">
                    <i class="fa fa-certificate"></i> Sertifikasi
                </Link>
                <Link href="/admin/skema" class="sidebar-link" :class="{ active: isActive('/admin/skema') }">
                    <i class="fa fa-list-alt"></i> Skema Sertifikasi
                </Link>
                <Link href="/admin/jadwal" class="sidebar-link" :class="{ active: isActive('/admin/jadwal') }">
                    <i class="fa fa-calendar-alt"></i> Jadwal Sertifikasi
                </Link>
                <Link href="/admin/peminjaman-lab" class="sidebar-link" :class="{ active: isActive('/admin/peminjaman-lab') }">
                    <i class="fa fa-door-open"></i> Peminjaman Lab
                </Link>
                <Link href="/admin/izin-penelitian" class="sidebar-link" :class="{ active: isActive('/admin/izin-penelitian') }">
                    <i class="fa fa-flask"></i> Izin Penelitian
                </Link>
                <Link href="/admin/surat-bebas-lab" class="sidebar-link" :class="{ active: isActive('/admin/surat-bebas-lab') }">
                    <i class="fa fa-file-alt"></i> Surat Bebas Lab
                </Link>
                <Link href="/admin/ruangan" class="sidebar-link" :class="{ active: isActive('/admin/ruangan') }">
                    <i class="fa fa-door-open"></i> Manajemen Ruangan
                </Link>
                <Link href="/admin/program-studi" class="sidebar-link" :class="{ active: isActive('/admin/program-studi') }">
                    <i class="fa fa-graduation-cap"></i> Program Studi
                </Link>
                <Link href="/admin/users" class="sidebar-link" :class="{ active: isActive('/admin/users') }">
                    <i class="fa fa-users"></i> Manajemen User
                </Link>
                <Link href="/admin/berita" class="sidebar-link" :class="{ active: isActive('/admin/berita') }">
                    <i class="fa fa-newspaper"></i> Berita &amp; Pengumuman
                </Link>
                <Link href="/kepala_lab/struktur" class="sidebar-link" :class="{ active: isActive('/kepala_lab/struktur') }">
                    <i class="fa fa-sitemap"></i> Struktur Organisasi
                </Link>
                <Link href="/admin/site-setting" class="sidebar-link" :class="{ active: isActive('/admin/site-setting') }">
                    <i class="fa fa-globe"></i> Profil Website
                </Link>
              </template>

              <template v-else-if="isKepalaLab">
                <Link href="/kepala_lab/dashboard" class="sidebar-link" :class="{ active: isActive('/kepala_lab/dashboard') }">
                    <i class="fa fa-th-large"></i> Dashboard
                </Link>
                <Link href="/kepala_lab/sertifikasi" class="sidebar-link" :class="{ active: isActive('/kepala_lab/sertifikasi') }">
                    <i class="fa fa-clipboard-check"></i> Validasi Sertifikasi
                </Link>
                <Link href="/kepala_lab/izin-penelitian" class="sidebar-link" :class="{ active: isActive('/kepala_lab/izin-penelitian') }">
                    <i class="fa fa-flask"></i> Izin Penelitian
                </Link>
                <Link href="/kepala_lab/peminjaman-lab" class="sidebar-link" :class="{ active: isActive('/kepala_lab/peminjaman-lab') }">
                    <i class="fa fa-door-open"></i> Peminjaman Lab
                </Link>
                <Link href="/kepala_lab/surat-bebas-lab" class="sidebar-link" :class="{ active: isActive('/kepala_lab/surat-bebas-lab') }">
                    <i class="fa fa-file-alt"></i> Surat Bebas Lab
                </Link>
                <Link href="/admin/berita" class="sidebar-link" :class="{ active: isActive('/admin/berita') }">
                    <i class="fa fa-newspaper"></i> Berita &amp; Pengumuman
                </Link>
                <Link href="/kepala_lab/struktur" class="sidebar-link" :class="{ active: isActive('/kepala_lab/struktur') }">
                    <i class="fa fa-sitemap"></i> Struktur Organisasi
                </Link>
              </template>
          </div>

          <div class="sidebar-section">
              <div class="sidebar-section-title">Akun</div>
              <Link href="/profile" class="sidebar-link" :class="{ active: isActive('/profile') }">
                  <i class="fa fa-user-cog"></i> Profil Saya
              </Link>
          </div>
      </nav>

      <div class="sidebar-footer">
          <div class="sidebar-user" v-if="user">
              <img v-if="user.foto_profil" :src="`/files/${user.foto_profil}`" alt="Foto" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid rgba(255,255,255,.3)">
              <div v-else class="sidebar-avatar">{{ user.name.substring(0, 1).toUpperCase() }}</div>
              <div class="sidebar-user-info">
                  <strong>{{ user.name }}</strong>
                  <span>{{ user.role }}</span>
              </div>
          </div>
      </div>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="main-wrap">
      <header class="topbar">
          <div style="display:flex;align-items:center;gap:14px">
              <button class="hamburger" @click="toggleSidebar"><i class="fa fa-bars"></i></button>
              <span class="topbar-title">{{ title }}</span>
          </div>
          <div class="topbar-right">
              <!-- NOTIFICATION -->
              <div class="notification-wrapper" v-if="isNotificationEnabled">
                  <button @click="toggleNotification" class="btn-notification">
                      <i class="fa fa-bell"></i>
                      <span class="notification-badge" v-if="totalPending > 0">{{ totalPending }}</span>
                  </button>
                  <div class="notification-dropdown" :class="{ show: isNotificationOpen }">
                      <div class="notification-header">
                          Notifikasi
                      </div>
                      <div class="notification-list" v-if="totalPending > 0">
                          <Link :href="getNotificationUrl('sertifikasi')" class="notification-item" v-if="pendingCounts.sertifikasi > 0">
                              <div class="notification-icon" style="background:#eff6ff;color:#3b82f6"><i class="fa fa-certificate"></i></div>
                              <div class="notification-text">
                                  <strong>Sertifikasi</strong>
                                  <span>{{ getNotificationSubtitle(pendingCounts.sertifikasi) }}</span>
                              </div>
                          </Link>
                          <Link :href="getNotificationUrl('peminjaman-lab')" class="notification-item" v-if="pendingCounts.peminjaman_lab > 0">
                              <div class="notification-icon" style="background:#fef3c7;color:#d97706"><i class="fa fa-door-open"></i></div>
                              <div class="notification-text">
                                  <strong>Peminjaman Lab</strong>
                                  <span>{{ getNotificationSubtitle(pendingCounts.peminjaman_lab) }}</span>
                              </div>
                          </Link>
                          <Link :href="getNotificationUrl('izin-penelitian')" class="notification-item" v-if="pendingCounts.izin_penelitian > 0">
                              <div class="notification-icon" style="background:#dcfce7;color:#16a34a"><i class="fa fa-flask"></i></div>
                              <div class="notification-text">
                                  <strong>Izin Penelitian</strong>
                                  <span>{{ getNotificationSubtitle(pendingCounts.izin_penelitian) }}</span>
                              </div>
                          </Link>
                          <Link :href="getNotificationUrl('surat-bebas-lab')" class="notification-item" v-if="pendingCounts.surat_bebas_lab > 0">
                              <div class="notification-icon" style="background:#f3e8ff;color:#9333ea"><i class="fa fa-file-alt"></i></div>
                              <div class="notification-text">
                                  <strong>Surat Bebas Lab</strong>
                                  <span>{{ getNotificationSubtitle(pendingCounts.surat_bebas_lab) }}</span>
                              </div>
                          </Link>
                      </div>
                      <div class="notification-empty" v-else>
                          Belum ada notifikasi baru
                      </div>
                  </div>
              </div>

              <button @click="handleLogout" class="topbar-btn btn-logout">
                  <i class="fa fa-sign-out-alt"></i> Keluar
              </button>
          </div>
      </header>

      <main class="content">
          <slot></slot>
      </main>
  </div>
</template>
