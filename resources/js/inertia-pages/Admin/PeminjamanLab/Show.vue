<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjaman: Object
})

const actionForm = useForm({})

function action(type) {
  const actions = {
    'verifikasi': { msg: 'Verifikasi permohonan ini?', url: `/admin/peminjaman-lab/${props.peminjaman.id}/verifikasi` },
    'tolak': { msg: 'Tolak permohonan ini?', url: `/admin/peminjaman-lab/${props.peminjaman.id}/tolak` },
    'kembali': { msg: 'Verifikasi pengembalian ruangan?', url: `/admin/peminjaman-lab/${props.peminjaman.id}/verifikasi-kembali` }
  }

  const act = actions[type]
  if (!confirm(act.msg)) return

  actionForm.post(act.url, {
    preserveScroll: true
  })
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
  })
}

function formatTime(timeStr) {
  if (!timeStr) return '-'
  return timeStr.substring(0, 5)
}

function getStatusConfig(status) {
  const cfg = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak', dot: '#ef4444' },
    'menunggu_kembali': { bg: '#fdf4ff', border: '#e9d5ff', color: '#6b21a8', icon: 'fa-undo', label: 'Menunggu Verifikasi Kembali', dot: '#a855f7' },
    'dikembalikan': { bg: '#f8fafc', border: '#cbd5e1', color: '#334155', icon: 'fa-check-double', label: 'Sudah Dikembalikan', dot: '#64748b' }
  }
  return cfg[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}
</script>

<template>
  <AppLayout title="Detail Peminjaman">
    <div style="margin-bottom:20px">
      <Link href="/admin/peminjaman-lab" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" @mouseover="$event.currentTarget.style.color='#4f46e5'" @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
      <div>
        <!-- Detail Card -->
        <div class="card" style="margin-bottom:20px">
          <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <h3><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Detail Peminjaman</h3>
            <div :style="`background:${getStatusConfig(peminjaman.status).bg};border:1px solid ${getStatusConfig(peminjaman.status).border};color:${getStatusConfig(peminjaman.status).color};border-radius:8px;padding:5px 14px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px`">
              <i :class="['fa', getStatusConfig(peminjaman.status).icon]"></i> {{ getStatusConfig(peminjaman.status).label }}
            </div>
          </div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:12px 16px;font-size:13px;color:#64748b;width:160px"><i class="fa fa-user" style="color:#4f46e5;margin-right:8px"></i>Nama</td>
                <td style="padding:12px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ peminjaman.user?.name || '-' }}</td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:12px 16px;font-size:13px;color:#64748b"><i class="fa fa-id-card" style="color:#4f46e5;margin-right:8px"></i>NIM / NIP</td>
                <td style="padding:12px 16px;font-size:14px;color:#0f172a">{{ peminjaman.user?.nim_nip || '-' }}</td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:12px 16px;font-size:13px;color:#64748b"><i class="fa fa-book" style="color:#4f46e5;margin-right:8px"></i>Jurusan</td>
                <td style="padding:12px 16px;font-size:14px;color:#0f172a">{{ peminjaman.user?.jurusan || '-' }}</td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:12px 16px;font-size:13px;color:#64748b"><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Ruangan</td>
                <td style="padding:12px 16px;font-size:14px;color:#0f172a;font-weight:700">{{ peminjaman.ruangan }}</td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:12px 16px;font-size:13px;color:#64748b"><i class="fa fa-calendar" style="color:#3b82f6;margin-right:8px"></i>Tanggal</td>
                <td style="padding:12px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ formatDate(peminjaman.tanggal_pinjam) }}</td>
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
              <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:10px"><i class="fa fa-paperclip" style="color:#f59e0b;margin-right:6px"></i>Surat Keterangan</p>
              <a :href="`/files/${peminjaman.surat_keterangan}`" target="_blank"
                style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
                <i class="fa fa-eye"></i> Buka Dokumen
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Aksi -->
      <div>
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-tasks" style="color:#4f46e5;margin-right:8px"></i>Tindakan</h3>
          </div>
          <div class="card-body">
            <div v-if="peminjaman.status === 'menunggu'">
              <p style="font-size:13px;color:#64748b;margin-bottom:16px">Permohonan ini menunggu verifikasi dari admin.</p>
              <button @click="action('verifikasi')" :disabled="actionForm.processing"
                style="width:100%;margin-bottom:10px;padding:12px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;font-size:14px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                :style="actionForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="actionForm.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-check"></i> Verifikasi Permohonan
              </button>
              <button @click="action('tolak')" :disabled="actionForm.processing"
                style="width:100%;padding:12px;border-radius:10px;background:#fff1f2;border:1.5px solid #fda4af;color:#be123c;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                :style="actionForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="actionForm.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-times"></i> Tolak
              </button>
            </div>

            <div v-else-if="peminjaman.status === 'diverifikasi'" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;text-align:center">
              <i class="fa fa-check-circle" style="font-size:28px;color:#3b82f6;display:block;margin-bottom:8px"></i>
              <p style="font-size:14px;font-weight:700;color:#1d4ed8">Sudah Diverifikasi</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px">Menunggu persetujuan Kepala Lab.</p>
            </div>

            <div v-else-if="peminjaman.status === 'disetujui'" style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px;text-align:center">
              <i class="fa fa-check-double" style="font-size:28px;color:#16a34a;display:block;margin-bottom:8px"></i>
              <p style="font-size:14px;font-weight:700;color:#15803d">Disetujui</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px">Peminjaman telah disetujui Kepala Lab. Menunggu pengembalian dari mahasiswa.</p>
            </div>

            <div v-else-if="peminjaman.status === 'menunggu_kembali'">
              <p style="font-size:13px;color:#6b21a8;font-weight:600;margin-bottom:12px">
                <i class="fa fa-undo" style="margin-right:6px"></i>Mahasiswa melaporkan telah mengembalikan ruangan. Verifikasi pengembalian di bawah.
              </p>
              <button @click="action('kembali')" :disabled="actionForm.processing"
                style="width:100%;padding:12px;border-radius:10px;background:linear-gradient(135deg,#a855f7,#7c3aed);color:#fff;font-size:14px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(168,85,247,.3)"
                :style="actionForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="actionForm.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-check-double"></i> Verifikasi Pengembalian
              </button>
            </div>

            <div v-else-if="peminjaman.status === 'dikembalikan'" style="background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;padding:14px;text-align:center">
              <i class="fa fa-check-double" style="font-size:28px;color:#64748b;display:block;margin-bottom:8px"></i>
              <p style="font-size:14px;font-weight:700;color:#334155">Pengembalian Selesai</p>
              <p style="font-size:12px;color:#64748b;margin-top:4px">Ruangan telah dikembalikan dan diverifikasi.</p>
            </div>

            <div v-else style="background:#fff1f2;border:1px solid #fda4af;border-radius:10px;padding:14px;text-align:center">
              <i class="fa fa-times-circle" style="font-size:28px;color:#ef4444;display:block;margin-bottom:8px"></i>
              <p style="font-size:14px;font-weight:700;color:#be123c">Ditolak</p>
            </div>
          </div>
        </div>

        <div class="card" style="margin-top:16px">
          <div class="card-body" style="padding:14px 16px">
            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px">
              <i class="fa fa-clock"></i> Diajukan {{ formatDate(peminjaman.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
