<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  pendaftarans: Object
})

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', text: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', text: '#1d4ed8', icon: 'fa-spinner', label: 'Sudah Diverifikasi' },
    'lulus': { bg: '#f0fdf4', border: '#86efac', text: '#15803d', icon: 'fa-check-circle', label: 'Lulus' },
    'tidak_lulus': { bg: '#fff1f2', border: '#fda4af', text: '#be123c', icon: 'fa-times-circle', label: 'Tidak Lulus' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', text: '#64748b', icon: 'fa-question', label: status }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function fromNow(dateString) {
  const diff = Math.floor((new Date() - new Date(dateString)) / 1000)
  if (diff < 60) return 'baru saja'
  if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu'
  if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu'
  if (diff < 2592000) return Math.floor(diff / 86400) + ' hari yang lalu'
  if (diff < 31536000) return Math.floor(diff / 2592000) + ' bulan yang lalu'
  return Math.floor(diff / 31536000) + ' tahun yang lalu'
}
</script>

<template>
  <AppLayout title="Riwayat Sertifikasi">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
      <p class="page-title" style="margin-bottom:0">Riwayat Pendaftaran</p>
      <Link href="/mahasiswa/sertifikasi" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
        <i class="fa fa-plus"></i> Daftar Baru
      </Link>
    </div>
    <p class="page-sub">Pantau status semua pendaftaran sertifikasi Anda.</p>

    <div>
      <div v-if="pendaftarans.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
        <div style="font-size:52px;margin-bottom:16px">📋</div>
        <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Belum Ada Riwayat</h3>
        <p style="color:#64748b;font-size:14px;margin-bottom:20px">Anda belum pernah mendaftar sertifikasi. Daftar sekarang!</p>
        <Link href="/mahasiswa/sertifikasi" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px">
          <i class="fa fa-paper-plane"></i> Lihat Jadwal Tersedia
        </Link>
      </div>
      
      <div v-else style="display:flex;flex-direction:column;gap:16px">
        <div v-for="p in pendaftarans.data" :key="p.id" class="card" style="overflow:visible">
          <div class="card-body" style="padding:20px 24px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                  <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden">
                    <img v-if="p.jadwal.skema.logo" :src="`/storage-public/${p.jadwal.skema.logo}`" :alt="p.jadwal.skema.nama_skema" style="width:42px;height:42px;object-fit:contain;background:#fff;padding:4px;border-radius:12px">
                    <i v-else class="fa fa-certificate" style="color:#fff;font-size:16px"></i>
                  </div>
                  <div>
                    <h4 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:2px">{{ p.jadwal.skema.nama_skema }}</h4>
                    <div style="font-size:12px;color:#94a3b8"><i class="fa fa-calendar" style="margin-right:4px"></i>{{ formatDate(p.jadwal.tanggal) }}</div>
                  </div>
                </div>
                
                <div style="display:flex;flex-wrap:wrap;gap:10px">
                  <div style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px">
                    <i class="fa fa-clock" style="color:#94a3b8"></i>
                    Didaftarkan {{ fromNow(p.created_at) }}
                  </div>
                  <div v-if="p.token_ujian" style="font-size:12px;background:#eff6ff;color:#1d4ed8;padding:3px 10px;border-radius:50px;font-weight:700;display:flex;align-items:center;gap:4px">
                    <i class="fa fa-key"></i> Token: {{ p.token_ujian }}
                  </div>
                </div>
                
                <div v-if="p.hasil_validasi" style="margin-top:10px;padding:10px 14px;background:#f8fafc;border-radius:8px;border-left:3px solid #4f46e5">
                  <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:3px">CATATAN KEPALA LAB:</div>
                  <p style="font-size:13px;color:#475569">{{ p.hasil_validasi }}</p>
                </div>
              </div>
              
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
                <div :style="`background:${getStatusConfig(p.status).bg};border:1px solid ${getStatusConfig(p.status).border};color:${getStatusConfig(p.status).text};`" style="border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;white-space:nowrap;display:flex;align-items:center;gap:6px">
                  <i :class="`fa ${getStatusConfig(p.status).icon}`"></i>
                  {{ getStatusConfig(p.status).label }}
                </div>
                <a v-if="['lulus', 'tidak_lulus'].includes(p.status)" :href="`/mahasiswa/sertifikasi/${p.id}/cetak`" target="_blank"
                    :style="`background:${p.status === 'lulus' ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#64748b,#475569)'}`"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;box-shadow:0 3px 8px rgba(0,0,0,.15);border:none;cursor:pointer">
                    <i class="fa fa-print"></i> Cetak Sertifikat
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <div v-if="pendaftarans.last_page > 1" style="display:flex;justify-content:center;gap:8px;margin-top:16px">
          <Link v-for="(link, index) in pendaftarans.links" :key="index"
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
