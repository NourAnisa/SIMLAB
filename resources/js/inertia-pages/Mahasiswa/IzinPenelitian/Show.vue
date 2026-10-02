<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  izin: Object
})

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Sedang Diverifikasi', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak', dot: '#ef4444' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

const timeline = [
  { key: 'menunggu', label: 'Permohonan Dikirim', desc: 'Menunggu verifikasi admin', icon: 'fa-paper-plane', color: '#f59e0b' },
  { key: 'diverifikasi', label: 'Terverifikasi Admin', desc: 'Menunggu persetujuan kepala lab', icon: 'fa-user-check', color: '#3b82f6' },
  { key: 'disetujui', label: 'Disetujui', desc: 'Izin penelitian telah disetujui', icon: 'fa-check-circle', color: '#10b981' }
]
const order = { 'menunggu': 0, 'diverifikasi': 1, 'disetujui': 2, 'ditolak': 1 }
</script>

<template>
  <AppLayout title="Detail Izin Penelitian">
    <div style="margin-bottom:20px">
      <Link href="/mahasiswa/izin-penelitian" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#4f46e5'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
      <!-- MAIN DETAIL -->
      <div>
        <div class="card" :style="`margin-bottom:16px;border-left:4px solid ${getStatusConfig(izin.status).dot}`">
          <div class="card-header" style="background:linear-gradient(135deg,rgba(16,185,129,.05),rgba(5,150,105,.05))">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                <i class="fa fa-flask" style="color:#fff;font-size:16px"></i>
              </div>
              <div>
                <h3 style="font-size:16px;font-weight:700;color:#0f172a">{{ izin.judul_penelitian }}</h3>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Diajukan {{ formatDate(izin.created_at) }}</div>
              </div>
            </div>
            <div :style="`background:${getStatusConfig(izin.status).bg};border:1px solid ${getStatusConfig(izin.status).border};color:${getStatusConfig(izin.status).color};`" style="border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px">
              <i :class="`fa ${getStatusConfig(izin.status).icon}`"></i> {{ getStatusConfig(izin.status).label }}
            </div>
          </div>
          <div class="card-body">
            <div style="margin-bottom:20px">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Tujuan Penelitian</div>
              <p style="font-size:14px;color:#475569;line-height:1.8;white-space:pre-line">{{ izin.tujuan }}</p>
            </div>
            
            <div v-if="izin.data_dibutuhkan && izin.data_dibutuhkan.length > 0" style="margin-bottom:20px">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Data yang Dibutuhkan</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px">
                <div v-for="(item, idx) in izin.data_dibutuhkan" :key="idx" style="background:#f8fafc;border:1px solid #e2e8f0;padding:6px 12px;border-radius:6px;font-size:12px;color:#475569">
                  <i class="fa fa-check" style="color:#10b981;margin-right:4px"></i> {{ item }}
                </div>
              </div>
            </div>

            <div v-if="izin.dokumen_berkas" style="margin-bottom:20px;padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Dokumen Lampiran</div>
              <a :href="`/storage-public/${izin.dokumen_berkas}`" target="_blank"
                style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;background:#fff;border:1.5px solid #4f46e5;color:#4f46e5;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:all .2s"
                @mouseover="$event.currentTarget.style.background='#4f46e5'; $event.currentTarget.style.color='#fff'" 
                @mouseout="$event.currentTarget.style.background='#fff'; $event.currentTarget.style.color='#4f46e5'">
                <i class="fa fa-file-download"></i> Lihat Dokumen
              </a>
            </div>

            <div v-if="izin.catatan_verifikasi" :style="`padding:16px;background:${izin.status === 'ditolak' ? '#fff1f2' : '#f8fafc'};border-radius:10px;border-left:3px solid ${izin.status === 'ditolak' ? '#ef4444' : '#4f46e5'}`">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
                <i class="fa fa-comment" style="margin-right:4px"></i>Catatan Verifikasi
              </div>
              <p style="font-size:14px;color:#475569;line-height:1.7">{{ izin.catatan_verifikasi }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- SIDEBAR STATUS TIMELINE -->
      <div>
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-timeline" style="color:#4f46e5;margin-right:8px"></i>Status Permohonan</h3>
          </div>
          <div class="card-body">
            <div v-if="izin.status === 'ditolak'" style="text-align:center;padding:20px 0">
              <div style="font-size:40px;margin-bottom:10px">❌</div>
              <div style="font-size:14px;font-weight:700;color:#be123c">Permohonan Ditolak</div>
              <div style="font-size:12px;color:#64748b;margin-top:4px">{{ formatDate(izin.updated_at) }}</div>
            </div>
            <div v-else style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:20px;bottom:20px;width:2px;background:#e2e8f0"></div>
              
              <div v-for="(step, i) in timeline" :key="i" :style="`position:relative;margin-bottom:${i === timeline.length - 1 ? '0' : '22px'}`">
                <div :style="`position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;background:${i <= (order[izin.status] || 0) ? step.color : '#e2e8f0'};display:flex;align-items:center;justify-content:center;transition:all .3s;${i === (order[izin.status] || 0) ? 'box-shadow:0 0 0 4px '+step.color+'33' : ''}`">
                  <i :class="`fa ${step.icon}`" :style="`color:${i <= (order[izin.status] || 0) ? '#fff' : '#94a3b8'};font-size:10px`"></i>
                </div>
                <div :style="`font-size:13px;font-weight:${i === (order[izin.status] || 0) ? '700' : '600'};color:${i <= (order[izin.status] || 0) ? '#0f172a' : '#94a3b8'};margin-bottom:2px`">
                  {{ step.label }}
                  <span v-if="i === (order[izin.status] || 0)" :style="`font-size:10px;background:${step.color};color:#fff;border-radius:50px;padding:1px 7px;margin-left:4px;font-weight:700`">Saat ini</span>
                </div>
                <div :style="`font-size:11px;color:${i <= (order[izin.status] || 0) ? '#64748b' : '#c9d5e0'}`">{{ step.desc }}</div>
              </div>
            </div>

            <template v-if="izin.tanggal_disetujui">
              <hr style="border:none;border-top:1px solid #f1f5f9;margin:16px 0">
              <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#16a34a;font-weight:600">
                <i class="fa fa-calendar-check"></i>
                Tanggal Disetujui: {{ formatDate(izin.tanggal_disetujui) }}
              </div>
            </template>

            <template v-if="izin.status === 'disetujui'">
              <hr style="border:none;border-top:1px solid #f1f5f9;margin:16px 0">
              <a :href="`/mahasiswa/izin-penelitian/${izin.id}/cetak`" target="_blank"
                style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border-radius:10px;text-decoration:none;font-size:14px;font-weight:700;box-shadow:0 4px 12px rgba(16,185,129,.3)">
                <i class="fa fa-file-pdf"></i> Cetak / Unduh Surat Izin
              </a>
            </template>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
