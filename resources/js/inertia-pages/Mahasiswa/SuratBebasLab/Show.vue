<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  surat: Object
})

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

function formatDateTime(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'masih_tanggungan': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-exclamation-triangle', label: 'Masih Tanggungan', dot: '#ef4444' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi Admin', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

const steps = [
  { key: 'menunggu', label: 'Diajukan', icon: 'fa-paper-plane', color: '#0ea5e9' },
  { key: 'diverifikasi', label: 'Diverifikasi Admin', icon: 'fa-user-check', color: '#3b82f6' },
  { key: 'disetujui', label: 'Disetujui Kepala Lab', icon: 'fa-check-circle', color: '#16a34a' }
]
const orderMap = { 'menunggu': 0, 'masih_tanggungan': 0, 'diverifikasi': 1, 'disetujui': 2 }
</script>

<template>
  <AppLayout title="Detail Surat Bebas Lab">
    <div style="margin-bottom:20px">
      <Link href="/mahasiswa/surat-bebas-lab" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#0ea5e9'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
      <!-- DETAIL CARD -->
      <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
          <h3><i class="fa fa-file-alt" style="color:#0ea5e9;margin-right:8px"></i>Surat Bebas Lab #{{ surat.id }}</h3>
          <div :style="`background:${getStatusConfig(surat.status).bg};border:1px solid ${getStatusConfig(surat.status).border};color:${getStatusConfig(surat.status).color};`" style="border-radius:8px;padding:5px 14px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px">
            <i :class="`fa ${getStatusConfig(surat.status).icon}`"></i> {{ getStatusConfig(surat.status).label }}
          </div>
        </div>
        <div class="card-body">
          <table style="width:100%;border-collapse:collapse">
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:13px 16px;font-size:13px;color:#64748b;width:180px">
                <i class="fa fa-calendar" style="color:#0ea5e9;margin-right:8px"></i>Tanggal Pengajuan
              </td>
              <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">
                {{ formatDate(surat.tanggal_pengajuan) }}
              </td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:13px 16px;font-size:13px;color:#64748b">
                <i class="fa fa-info-circle" style="color:#0ea5e9;margin-right:8px"></i>Status
              </td>
              <td style="padding:13px 16px">
                <span :style="`background:${getStatusConfig(surat.status).bg};border:1px solid ${getStatusConfig(surat.status).border};color:${getStatusConfig(surat.status).color};`" style="border-radius:8px;padding:4px 12px;font-size:13px;font-weight:700">
                  <i :class="`fa ${getStatusConfig(surat.status).icon}`"></i> {{ getStatusConfig(surat.status).label }}
                </span>
              </td>
            </tr>
            <tr v-if="surat.tanggal_disetujui" style="border-bottom:1px solid #f1f5f9">
              <td style="padding:13px 16px;font-size:13px;color:#64748b">
                <i class="fa fa-calendar-check" style="color:#16a34a;margin-right:8px"></i>Tanggal Disetujui
              </td>
              <td style="padding:13px 16px;font-size:14px;color:#16a34a;font-weight:600">
                {{ formatDate(surat.tanggal_disetujui) }}
              </td>
            </tr>
            <tr v-if="surat.catatan">
              <td style="padding:13px 16px;font-size:13px;color:#64748b;vertical-align:top">
                <i class="fa fa-comment-alt" style="color:#ef4444;margin-right:8px"></i>Catatan
              </td>
              <td style="padding:13px 16px;font-size:14px;color:#dc2626;line-height:1.6">{{ surat.catatan }}</td>
            </tr>
          </table>

          <div v-if="surat.status === 'masih_tanggungan'" style="margin:16px;padding:16px;background:#fff1f2;border:1px solid #fda4af;border-radius:10px">
            <p style="font-size:13px;font-weight:700;color:#be123c;margin-bottom:6px"><i class="fa fa-exclamation-triangle"></i> Masih Ada Tanggungan</p>
            <p style="font-size:13px;color:#9f1239;line-height:1.6">Anda masih memiliki tanggungan peminjaman lab yang belum diselesaikan. Selesaikan terlebih dahulu sebelum mengajukan surat bebas lab kembali.</p>
            <Link href="/mahasiswa/peminjaman-lab"
              style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:8px 16px;background:#fff1f2;border:1.5px solid #fda4af;color:#be123c;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
              <i class="fa fa-door-open"></i> Lihat Peminjaman
            </Link>
          </div>
        </div>
      </div>

      <!-- STATUS & AKSI -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-timeline" style="color:#0ea5e9;margin-right:8px"></i>Status Pengajuan</h3>
          </div>
          <div class="card-body">
            <div v-if="surat.status === 'masih_tanggungan'" style="background:#fff1f2;border:1px solid #fda4af;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:28px;margin-bottom:8px">⚠️</div>
              <p style="font-size:13px;font-weight:700;color:#be123c">Masih Tanggungan</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px">Selesaikan tanggungan lab terlebih dahulu.</p>
            </div>
            
            <div v-else style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:20px;bottom:20px;width:2px;background:#e2e8f0"></div>
              
              <div v-for="(s, i) in steps" :key="i" :style="`position:relative;margin-bottom:${i === steps.length - 1 ? '0' : '24px'}`">
                <div :style="`position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;background:${i <= (orderMap[surat.status] || 0) ? s.color : '#e2e8f0'};display:flex;align-items:center;justify-content:center`">
                  <i :class="`fa ${i <= (orderMap[surat.status] || 0) ? s.icon : 'fa-circle'}`" :style="`color:${i <= (orderMap[surat.status] || 0) ? '#fff' : '#94a3b8'};font-size:10px`"></i>
                </div>
                <div :style="`font-size:13px;font-weight:${i <= (orderMap[surat.status] || 0) ? '700' : '500'};color:${i <= (orderMap[surat.status] || 0) ? '#0f172a' : '#94a3b8'};margin-bottom:2px`">{{ s.label }}</div>
                
                <div v-if="i === (orderMap[surat.status] || 0) && surat.status !== 'disetujui'" style="font-size:11px;color:#0ea5e9;font-weight:600">● Saat ini</div>
                <div v-else-if="i <= (orderMap[surat.status] || 0)" style="font-size:11px;color:#16a34a">✓ Selesai</div>
                <div v-else style="font-size:11px;color:#94a3b8">Menunggu</div>
              </div>
            </div>

            <div v-if="surat.status === 'disetujui'" style="margin-top:16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:28px;margin-bottom:6px">🎉</div>
              <p style="font-size:13px;font-weight:700;color:#15803d">Surat Bebas Lab Disetujui!</p>
              <a :href="`/mahasiswa/surat-bebas-lab/${surat.id}/cetak`" target="_blank"
                style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:9px 20px;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(14,165,233,.3)">
                <i class="fa fa-print"></i> Cetak Surat
              </a>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body" style="padding:14px 16px">
            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px">
              <i class="fa fa-clock"></i> Diajukan {{ formatDateTime(surat.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
