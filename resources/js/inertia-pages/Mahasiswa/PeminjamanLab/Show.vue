<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjaman: Object
})

const returning = ref(false)

function kembalikan() {
  if (!confirm('Konfirmasi bahwa Anda sudah mengembalikan ruangan?')) return
  
  router.post(`/mahasiswa/peminjaman-lab/${props.peminjaman.id}/kembalikan`, {}, {
    preserveScroll: true,
    onStart: () => { returning.value = true },
    onFinish: () => { returning.value = false }
  })
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

function formatDateTime(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function formatTime(timeString) {
  if (!timeString) return ''
  return timeString.substring(0, 5)
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi Admin', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak', dot: '#ef4444' },
    'menunggu_kembali': { bg: '#fdf4ff', border: '#e9d5ff', color: '#6b21a8', icon: 'fa-undo', label: 'Menunggu Verifikasi Pengembalian', dot: '#a855f7' },
    'dikembalikan': { bg: '#f8fafc', border: '#cbd5e1', color: '#334155', icon: 'fa-check-double', label: 'Sudah Dikembalikan', dot: '#64748b' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

const steps = [
  { key: 'menunggu', label: 'Diajukan', icon: 'fa-paper-plane', color: '#f59e0b' },
  { key: 'diverifikasi', label: 'Diverifikasi Admin', icon: 'fa-user-check', color: '#3b82f6' },
  { key: 'disetujui', label: 'Disetujui Kepala Lab', icon: 'fa-check-circle', color: '#16a34a' },
  { key: 'menunggu_kembali', label: 'Lapor Pengembalian', icon: 'fa-undo', color: '#a855f7' },
  { key: 'dikembalikan', label: 'Verifikasi Pengembalian', icon: 'fa-check-double', color: '#64748b' }
]
const orderMap = { 'menunggu': 0, 'diverifikasi': 1, 'disetujui': 2, 'ditolak': 2, 'menunggu_kembali': 3, 'dikembalikan': 4 }
</script>

<template>
  <AppLayout title="Detail Peminjaman Lab">
    <div style="margin-bottom:20px">
      <Link href="/mahasiswa/peminjaman-lab" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#f59e0b'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
      <!-- DETAIL CARD -->
      <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
          <h3><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Detail Peminjaman Lab</h3>
          <div :style="`background:${getStatusConfig(peminjaman.status).bg};border:1px solid ${getStatusConfig(peminjaman.status).border};color:${getStatusConfig(peminjaman.status).color};`" style="border-radius:8px;padding:5px 14px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px">
            <i :class="`fa ${getStatusConfig(peminjaman.status).icon}`"></i> {{ getStatusConfig(peminjaman.status).label }}
          </div>
        </div>
        <div class="card-body">
          <table style="width:100%;border-collapse:collapse">
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:14px 16px;font-size:13px;color:#64748b;white-space:nowrap;width:180px">
                <i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px;width:14px"></i>Ruangan
              </td>
              <td style="padding:14px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ peminjaman.ruangan }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:14px 16px;font-size:13px;color:#64748b;white-space:nowrap;width:180px">
                <i class="fa fa-calendar" style="color:#3b82f6;margin-right:8px;width:14px"></i>Tanggal
              </td>
              <td style="padding:14px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ formatDate(peminjaman.tanggal_pinjam) }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:14px 16px;font-size:13px;color:#64748b;white-space:nowrap;width:180px">
                <i class="fa fa-clock" style="color:#8b5cf6;margin-right:8px;width:14px"></i>Waktu
              </td>
              <td style="padding:14px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ formatTime(peminjaman.jam_mulai) }} – {{ formatTime(peminjaman.jam_selesai) }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:14px 16px;font-size:13px;color:#64748b;white-space:nowrap;width:180px">
                <i class="fa fa-history" style="color:#64748b;margin-right:8px;width:14px"></i>Tanggal Pengajuan
              </td>
              <td style="padding:14px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ formatDateTime(peminjaman.created_at) }}</td>
            </tr>
            <tr>
              <td style="padding:14px 16px;font-size:13px;color:#64748b;vertical-align:top">
                <i class="fa fa-align-left" style="color:#10b981;margin-right:8px;width:14px"></i>Keperluan
              </td>
              <td style="padding:14px 16px;font-size:14px;color:#0f172a;line-height:1.7">{{ peminjaman.keperluan }}</td>
            </tr>
          </table>

          <div v-if="peminjaman.surat_keterangan" style="margin-top:20px;padding:16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
            <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:10px"><i class="fa fa-paperclip" style="color:#f59e0b;margin-right:6px"></i>Surat Keterangan Terlampir</p>
            <a :href="`/storage-public/${peminjaman.surat_keterangan}`" target="_blank"
              style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
              <i class="fa fa-eye"></i> Lihat Dokumen
            </a>
          </div>
        </div>
      </div>

      <!-- STATUS TIMELINE -->
      <div>
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-timeline" style="color:#f59e0b;margin-right:8px"></i>Status Pengajuan</h3>
          </div>
          <div class="card-body">
            <div v-if="peminjaman.status === 'ditolak'" style="background:#fff1f2;border:1px solid #fda4af;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:32px;margin-bottom:8px">❌</div>
              <p style="font-size:14px;font-weight:700;color:#be123c">Peminjaman Ditolak</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px">Silakan ajukan permohonan baru jika diperlukan.</p>
            </div>
            
            <div v-else style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:20px;bottom:20px;width:2px;background:#e2e8f0"></div>
              
              <div v-for="(s, i) in steps" :key="i" :style="`position:relative;margin-bottom:${i === steps.length - 1 ? '0' : '20px'}`">
                <div :style="`position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;background:${i <= (orderMap[peminjaman.status] || 0) ? s.color : '#e2e8f0'};display:flex;align-items:center;justify-content:center;transition:all .3s`">
                  <i :class="`fa ${i <= (orderMap[peminjaman.status] || 0) ? s.icon : 'fa-circle'}`" :style="`color:${i <= (orderMap[peminjaman.status] || 0) ? '#fff' : '#94a3b8'};font-size:10px`"></i>
                </div>
                <div :style="`font-size:13px;font-weight:${i <= (orderMap[peminjaman.status] || 0) ? '700' : '500'};color:${i <= (orderMap[peminjaman.status] || 0) ? '#0f172a' : '#94a3b8'};margin-bottom:2px`">{{ s.label }}</div>
                
                <div v-if="i === (orderMap[peminjaman.status] || 0) && !['disetujui','dikembalikan'].includes(peminjaman.status)" style="font-size:11px;color:#f59e0b;font-weight:600">● Saat ini</div>
                <div v-else-if="i <= (orderMap[peminjaman.status] || 0)" style="font-size:11px;color:#16a34a">✓ Selesai</div>
                <div v-else style="font-size:11px;color:#94a3b8">Menunggu</div>
              </div>
            </div>

            <div v-if="peminjaman.status === 'disetujui'" style="margin-top:16px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:26px;margin-bottom:6px">🎉</div>
              <p style="font-size:13px;font-weight:700;color:#15803d;margin-bottom:4px">Peminjaman Disetujui!</p>
              <p style="font-size:12px;color:#64748b;margin-bottom:12px">Ruangan siap digunakan. Setelah selesai, laporkan pengembalian.</p>
              <div style="display:flex;gap:8px;flex-direction:column;">
                <a :href="`/mahasiswa/peminjaman-lab/${peminjaman.id}/cetak-bukti-pinjam`" target="_blank"
                  style="width:100%;padding:10px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:13px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;text-decoration:none;box-shadow:0 4px 12px rgba(245,158,11,.3)">
                  <i class="fa fa-print"></i> Cetak Bukti Pinjam
                </a>
                <button @click="kembalikan" :disabled="returning"
                  style="width:100%;padding:10px;border-radius:8px;background:linear-gradient(135deg,#a855f7,#7c3aed);color:#fff;font-size:13px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;box-shadow:0 4px 12px rgba(168,85,247,.3)"
                  :style="returning ? 'opacity:0.7;cursor:not-allowed' : ''">
                  <i v-if="returning" class="fa fa-spinner fa-spin"></i>
                  <i v-else class="fa fa-undo"></i> 
                  {{ returning ? 'Melaporkan...' : 'Laporkan Pengembalian' }}
                </button>
              </div>
            </div>

            <div v-if="peminjaman.status === 'menunggu_kembali'" style="margin-top:16px;background:#fdf4ff;border:1px solid #e9d5ff;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:26px;margin-bottom:6px">⏳</div>
              <p style="font-size:13px;font-weight:700;color:#6b21a8">Menunggu Verifikasi Pengembalian</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px;margin-bottom:12px">Admin akan memverifikasi pengembalian ruangan.</p>
              <a :href="`/mahasiswa/peminjaman-lab/${peminjaman.id}/cetak-bukti-pinjam`" target="_blank"
                style="width:100%;padding:10px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:13px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;text-decoration:none;box-shadow:0 4px 12px rgba(245,158,11,.3)">
                <i class="fa fa-print"></i> Cetak Bukti Pinjam
              </a>
            </div>

            <div v-if="peminjaman.status === 'dikembalikan'" style="margin-top:16px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:26px;margin-bottom:6px">✅</div>
              <p style="font-size:13px;font-weight:700;color:#334155">Pengembalian Selesai</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px;margin-bottom:12px">Ruangan telah dikembalikan dan diverifikasi admin.</p>
              <div style="display:flex;gap:8px;flex-direction:column;">
                <a :href="`/mahasiswa/peminjaman-lab/${peminjaman.id}/cetak-bukti-pinjam`" target="_blank"
                  style="width:100%;padding:10px;border-radius:8px;background:#fff;border:1px solid #cbd5e1;color:#475569;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;text-decoration:none">
                  <i class="fa fa-print"></i> Bukti Pinjam
                </a>
                <a :href="`/mahasiswa/peminjaman-lab/${peminjaman.id}/cetak-bukti-kembali`" target="_blank"
                  style="width:100%;padding:10px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:13px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;text-decoration:none;box-shadow:0 4px 12px rgba(16,185,129,.3)">
                  <i class="fa fa-print"></i> Bukti Kembali
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
