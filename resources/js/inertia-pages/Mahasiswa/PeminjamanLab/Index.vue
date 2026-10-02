<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  peminjamans: Object
})

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak', dot: '#ef4444' },
    'menunggu_kembali': { bg: '#fdf4ff', border: '#e9d5ff', color: '#6b21a8', icon: 'fa-undo', label: 'Menunggu Verifikasi Pengembalian', dot: '#a855f7' },
    'dikembalikan': { bg: '#f8fafc', border: '#cbd5e1', color: '#334155', icon: 'fa-check-double', label: 'Dikembalikan', dot: '#64748b' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatTime(timeString) {
  if (!timeString) return ''
  return timeString.substring(0, 5)
}

function truncate(str, n) {
  if (!str) return ''
  return (str.length > n) ? str.slice(0, n - 1) + '...' : str
}
</script>

<template>
  <AppLayout title="Peminjaman Lab">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
      <p class="page-title" style="margin-bottom:0">Peminjaman Lab</p>
      <Link href="/mahasiswa/peminjaman-lab/buat"
        style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(245,158,11,.3);transition:all .2s"
        @mouseover="$event.currentTarget.style.transform='translateY(-1px)'" 
        @mouseout="$event.currentTarget.style.transform='translateY(0)'">
        <i class="fa fa-plus"></i> Ajukan Peminjaman
      </Link>
    </div>
    <p class="page-sub">Kelola semua permohonan peminjaman ruangan laboratorium.</p>

    <div>
      <div v-if="peminjamans.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
        <div style="font-size:56px;margin-bottom:16px">🏫</div>
        <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Belum Ada Peminjaman</h3>
        <p style="color:#64748b;font-size:14px;margin-bottom:24px">Anda belum pernah mengajukan peminjaman lab. Ajukan sekarang untuk booking ruangan.</p>
        <Link href="/mahasiswa/peminjaman-lab/buat"
          style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px">
          <i class="fa fa-door-open"></i> Ajukan Peminjaman Lab
        </Link>
      </div>
      
      <div v-else style="display:flex;flex-direction:column;gap:14px">
        <Link v-for="p in peminjamans.data" :key="p.id" :href="`/mahasiswa/peminjaman-lab/${p.id}`"
          style="display:block;text-decoration:none"
          @mouseover="$event.currentTarget.querySelector('.card').style.transform='translateY(-2px)'; $event.currentTarget.querySelector('.card').style.boxShadow='0 8px 24px rgba(0,0,0,.08)'"
          @mouseout="$event.currentTarget.querySelector('.card').style.transform='translateY(0)'; $event.currentTarget.querySelector('.card').style.boxShadow='none'">
          
          <div class="card" :style="`border-left:4px solid ${getStatusConfig(p.status).dot};transition:all .25s`">
            <div class="card-body" style="padding:18px 24px">
              <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
                <div style="flex:1">
                  <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(245,158,11,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <i class="fa fa-door-open" style="color:#f59e0b;font-size:15px"></i>
                    </div>
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a">{{ p.ruangan }}</h4>
                  </div>
                  <div style="display:flex;flex-wrap:wrap;gap:14px;margin-left:44px;margin-bottom:6px">
                    <span style="font-size:13px;color:#475569;display:flex;align-items:center;gap:5px">
                      <i class="fa fa-calendar" style="color:#f59e0b"></i>
                      {{ formatDate(p.tanggal_pinjam) }}
                    </span>
                    <span style="font-size:13px;color:#475569;display:flex;align-items:center;gap:5px">
                      <i class="fa fa-clock" style="color:#f59e0b"></i>
                      {{ formatTime(p.jam_mulai) }} – {{ formatTime(p.jam_selesai) }}
                    </span>
                  </div>
                  <p style="font-size:13px;color:#64748b;line-height:1.5;margin-left:44px">{{ truncate(p.keperluan, 100) }}</p>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
                  <div :style="`background:${getStatusConfig(p.status).bg};border:1px solid ${getStatusConfig(p.status).border};color:${getStatusConfig(p.status).color};`" style="border-radius:8px;padding:5px 12px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:5px;white-space:nowrap">
                    <i :class="`fa ${getStatusConfig(p.status).icon}`"></i> {{ getStatusConfig(p.status).label }}
                  </div>
                  <span style="font-size:11px;color:#94a3b8">Lihat Detail →</span>
                </div>
              </div>
            </div>
          </div>
        </Link>

        <!-- Pagination -->
        <div v-if="peminjamans.last_page > 1" style="display:flex;justify-content:center;gap:8px;margin-top:16px">
          <Link v-for="(link, index) in peminjamans.links" :key="index"
            :href="link.url || ''"
            v-html="link.label"
            preserve-scroll
            :style="`padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#f59e0b' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
