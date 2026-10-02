<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  izins: Object
})

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak', dot: '#ef4444' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function truncate(str, n) {
  if (!str) return ''
  return (str.length > n) ? str.slice(0, n - 1) + '...' : str
}
</script>

<template>
  <AppLayout title="Izin Penelitian">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
      <p class="page-title" style="margin-bottom:0">Izin Penelitian</p>
      <Link href="/mahasiswa/izin-penelitian/buat" 
        style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(79,70,229,.3);transition:all .2s"
        @mouseover="$event.currentTarget.style.transform='translateY(-1px)'" 
        @mouseout="$event.currentTarget.style.transform='translateY(0)'">
        <i class="fa fa-plus"></i> Ajukan Izin Baru
      </Link>
    </div>
    <p class="page-sub">Kelola semua permohonan izin penelitian di laboratorium.</p>

    <div>
      <div v-if="izins.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
        <div style="font-size:56px;margin-bottom:16px">🔬</div>
        <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Belum Ada Pengajuan</h3>
        <p style="color:#64748b;font-size:14px;margin-bottom:24px">Anda belum pernah mengajukan izin penelitian. Ajukan sekarang untuk mendapatkan akses lab.</p>
        <Link href="/mahasiswa/izin-penelitian/buat"
          style="display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px">
          <i class="fa fa-flask"></i> Ajukan Izin Penelitian
        </Link>
      </div>
      
      <div v-else style="display:flex;flex-direction:column;gap:14px">
        <Link v-for="izin in izins.data" :key="izin.id" :href="`/mahasiswa/izin-penelitian/${izin.id}`"
          style="display:block;text-decoration:none"
          @mouseover="$event.currentTarget.querySelector('.izin-card').style.transform='translateY(-2px)'; $event.currentTarget.querySelector('.izin-card').style.boxShadow='0 8px 24px rgba(0,0,0,.08)'"
          @mouseout="$event.currentTarget.querySelector('.izin-card').style.transform='translateY(0)'; $event.currentTarget.querySelector('.izin-card').style.boxShadow='none'">
          
          <div class="card izin-card" :style="`border-left:4px solid ${getStatusConfig(izin.status).dot};transition:all .25s`">
            <div class="card-body" style="padding:18px 24px">
              <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
                <div style="flex:1">
                  <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(5,150,105,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <i class="fa fa-flask" style="color:#10b981;font-size:15px"></i>
                    </div>
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a;line-height:1.3">{{ izin.judul_penelitian }}</h4>
                  </div>
                  <p style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:8px;margin-left:44px">{{ truncate(izin.tujuan, 120) }}</p>
                  <div style="display:flex;flex-wrap:wrap;gap:8px;margin-left:44px">
                    <span style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:4px">
                      <i class="fa fa-clock"></i> {{ formatDate(izin.created_at) }}
                    </span>
                    <span v-if="izin.tanggal_disetujui" style="font-size:12px;color:#16a34a;display:flex;align-items:center;gap:4px">
                      <i class="fa fa-calendar-check"></i> Disetujui {{ formatDate(izin.tanggal_disetujui) }}
                    </span>
                  </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
                  <div :style="`background:${getStatusConfig(izin.status).bg};border:1px solid ${getStatusConfig(izin.status).border};color:${getStatusConfig(izin.status).color};`" style="border-radius:8px;padding:5px 12px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:5px;white-space:nowrap">
                    <i :class="`fa ${getStatusConfig(izin.status).icon}`"></i> {{ getStatusConfig(izin.status).label }}
                  </div>
                  <span style="font-size:11px;color:#94a3b8">Lihat Detail →</span>
                </div>
              </div>
            </div>
          </div>
        </Link>

        <!-- Pagination -->
        <div v-if="izins.last_page > 1" style="display:flex;justify-content:center;gap:8px;margin-top:16px">
          <Link v-for="(link, index) in izins.links" :key="index"
            :href="link.url || ''"
            v-html="link.label"
            preserve-scroll
            :style="`padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
