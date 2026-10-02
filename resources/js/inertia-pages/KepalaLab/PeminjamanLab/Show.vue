<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjaman: Object
})

const formAction = useForm({})

function setujui() {
  if (!confirm('Yakin ingin menyetujui peminjaman ini?')) return
  formAction.post(`/kepala_lab/peminjaman-lab/${props.peminjaman.id}/setujui`, {
    preserveScroll: true
  })
}

function tolak() {
  if (!confirm('Yakin ingin menolak permohonan ini?')) return
  formAction.post(`/kepala_lab/peminjaman-lab/${props.peminjaman.id}/tolak`, {
    preserveScroll: true
  })
}

function formatDateFull(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

function formatTime(timeString) {
  if (!timeString) return ''
  return timeString.substring(0, 5)
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi — Menunggu Persetujuan Anda' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak' },
    'menunggu_kembali': { bg: '#fdf4ff', border: '#e9d5ff', color: '#6b21a8', icon: 'fa-undo', label: 'Menunggu Verifikasi Kembali' },
    'dikembalikan': { bg: '#f8fafc', border: '#cbd5e1', color: '#334155', icon: 'fa-check-double', label: 'Dikembalikan' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status }
}
</script>

<template>
  <AppLayout title="Detail Peminjaman">
    <div style="margin-bottom:20px">
      <Link href="/kepala_lab/peminjaman-lab" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#4f46e5'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
      <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
          <h3><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Detail Peminjaman</h3>
          <div :style="`background:${getStatusConfig(peminjaman.status).bg};border:1px solid ${getStatusConfig(peminjaman.status).border};color:${getStatusConfig(peminjaman.status).color};border-radius:8px;padding:5px 14px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px`">
            <i :class="`fa ${getStatusConfig(peminjaman.status).icon}`"></i> {{ getStatusConfig(peminjaman.status).label }}
          </div>
        </div>
        <div class="card-body">
          <div style="display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid #f1f5f9;margin-bottom:4px">
            <img v-if="peminjaman.user?.foto_profil" :src="`/files/${peminjaman.user.foto_profil}`" alt="foto" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0">
            <div v-else style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <span style="color:#fff;font-weight:700;font-size:18px">{{ peminjaman.user?.name ? peminjaman.user.name.substring(0,1).toUpperCase() : 'U' }}</span>
            </div>
            <div>
              <div style="font-size:16px;font-weight:700;color:#0f172a">{{ peminjaman.user?.name }}</div>
              <div style="font-size:13px;color:#64748b">{{ peminjaman.user?.nim_nip || '-' }} · {{ peminjaman.user?.jurusan || 'Mahasiswa' }}</div>
            </div>
          </div>

          <table style="width:100%;border-collapse:collapse;margin-top:8px">
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:12px 16px;font-size:13px;color:#64748b;width:160px"><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Ruangan</td>
              <td style="padding:12px 16px;font-size:14px;color:#0f172a;font-weight:700">{{ peminjaman.ruangan }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:12px 16px;font-size:13px;color:#64748b"><i class="fa fa-calendar" style="color:#3b82f6;margin-right:8px"></i>Tanggal</td>
              <td style="padding:12px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ formatDateFull(peminjaman.tanggal_pinjam) }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:12px 16px;font-size:13px;color:#64748b"><i class="fa fa-clock" style="color:#8b5cf6;margin-right:8px"></i>Waktu</td>
              <td style="padding:12px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ formatTime(peminjaman.jam_mulai) }} – {{ formatTime(peminjaman.jam_selesai) }} WIB</td>
            </tr>
            <tr>
              <td style="padding:12px 16px;font-size:13px;color:#64748b;vertical-align:top"><i class="fa fa-align-left" style="color:#10b981;margin-right:8px"></i>Keperluan</td>
              <td style="padding:12px 16px;font-size:14px;color:#0f172a;line-height:1.7">{{ peminjaman.keperluan }}</td>
            </tr>
          </table>

          <div v-if="peminjaman.surat_keterangan" style="margin:16px 16px 0;padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
            <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:10px"><i class="fa fa-paperclip" style="color:#f59e0b;margin-right:6px"></i>Surat Keterangan Terlampir</p>
            <a :href="`/files/${peminjaman.surat_keterangan}`" target="_blank"
              style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
              <i class="fa fa-eye"></i> Buka Dokumen
            </a>
          </div>
        </div>
      </div>

      <!-- Aksi -->
      <div>
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-gavel" style="color:#4f46e5;margin-right:8px"></i>Keputusan</h3>
          </div>
          <div class="card-body">
            <div v-if="peminjaman.status === 'diverifikasi'">
              <p style="font-size:13px;color:#64748b;margin-bottom:16px;line-height:1.6">Permohonan ini telah diverifikasi admin dan menunggu keputusan Anda.</p>
              
              <button @click="setujui" :disabled="formAction.processing"
                style="width:100%;margin-bottom:10px;padding:12px;border-radius:10px;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:14px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(22,163,74,.3)"
                :style="formAction.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="formAction.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-check"></i> 
                Setujui Peminjaman
              </button>
              
              <button @click="tolak" :disabled="formAction.processing"
                style="width:100%;padding:12px;border-radius:10px;background:#fff1f2;border:1.5px solid #fda4af;color:#be123c;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                :style="formAction.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="formAction.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-times"></i> 
                Tolak Peminjaman
              </button>
            </div>

            <div v-else-if="peminjaman.status === 'disetujui'" style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:20px;text-align:center">
              <i class="fa fa-check-double" style="font-size:32px;color:#16a34a;display:block;margin-bottom:10px"></i>
              <p style="font-size:15px;font-weight:700;color:#15803d">Sudah Disetujui</p>
              <p style="font-size:12px;color:#64748b;margin-top:6px">Peminjaman ruangan telah disetujui.</p>
            </div>

            <div v-else-if="peminjaman.status === 'ditolak'" style="background:#fff1f2;border:1px solid #fda4af;border-radius:10px;padding:20px;text-align:center">
              <i class="fa fa-times-circle" style="font-size:32px;color:#ef4444;display:block;margin-bottom:10px"></i>
              <p style="font-size:15px;font-weight:700;color:#be123c">Ditolak</p>
            </div>

            <div v-else style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;text-align:center">
              <p style="font-size:13px;color:#92400e">Masih menunggu verifikasi admin atau aksi selanjutnya.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
