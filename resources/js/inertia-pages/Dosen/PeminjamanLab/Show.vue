<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjaman: Object,
})

const formKembali = useForm({})

function kembalikan() {
  if (!confirm('Laporkan pengembalian ruangan ini?')) return
  formKembali.patch(`/dosen/peminjaman-lab/${props.peminjaman.id}/kembalikan`, {
    preserveScroll: true
  })
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function formatDateTime(d) {
  if (!d) return '-'
  return new Date(d).toLocaleString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function getStatusConfig(status) {
  const c = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi' },
    'menunggu_kembali': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-undo', label: 'Menunggu Verifikasi Kembali' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak' },
    'selesai': { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-archive', label: 'Selesai' },
  }
  return c[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status }
}
</script>

<template>
  <AppLayout title="Detail Peminjaman Lab">
    <div style="margin-bottom:20px">
      <Link href="/dosen/peminjaman-lab"
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500"
        @mouseover="$event.currentTarget.style.color='#f59e0b'"
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start">
      <!-- MAIN DETAIL -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                <i class="fa fa-door-open" style="color:#fff;font-size:18px"></i>
              </div>
              <div>
                <h3 style="font-size:17px;font-weight:700;color:#0f172a;margin:0">{{ peminjaman.ruangan }}</h3>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Diajukan {{ formatDateTime(peminjaman.created_at) }}</div>
              </div>
            </div>
            <div :style="`background:${getStatusConfig(peminjaman.status).bg};border:1px solid ${getStatusConfig(peminjaman.status).border};color:${getStatusConfig(peminjaman.status).color};border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px`">
              <i :class="`fa ${getStatusConfig(peminjaman.status).icon}`"></i>
              {{ getStatusConfig(peminjaman.status).label }}
            </div>
          </div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
              <div>
                <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Tanggal Pinjam</div>
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ formatDate(peminjaman.tanggal_pinjam) }}</div>
              </div>
              <div>
                <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Waktu</div>
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ peminjaman.jam_mulai }} – {{ peminjaman.jam_selesai }}</div>
              </div>
            </div>
            <div style="margin-bottom:16px">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Keperluan</div>
              <p style="font-size:14px;color:#475569;line-height:1.7;white-space:pre-line">{{ peminjaman.keperluan }}</p>
            </div>
            <div v-if="peminjaman.catatan" style="padding:14px;background:#fff1f2;border-radius:10px;border-left:3px solid #ef4444">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Catatan Admin</div>
              <p style="font-size:14px;color:#475569">{{ peminjaman.catatan }}</p>
            </div>
          </div>
        </div>

        <!-- KEMBALIKAN ACTION -->
        <div v-if="peminjaman.status === 'disetujui'" class="card" style="border-left:4px solid #f59e0b">
          <div class="card-body">
            <h4 style="font-size:14px;font-weight:700;color:#92400e;margin:0 0 8px"><i class="fa fa-undo" style="margin-right:6px"></i>Selesai Menggunakan Ruangan?</h4>
            <p style="font-size:13px;color:#64748b;margin:0 0 14px">Klik tombol berikut untuk melaporkan pengembalian ruangan kepada admin.</p>
            <button @click="kembalikan" :disabled="formKembali.processing"
              style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
              <i v-if="formKembali.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-undo"></i>
              {{ formKembali.processing ? 'Memproses...' : 'Laporkan Pengembalian' }}
            </button>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-info-circle" style="color:#f59e0b;margin-right:8px"></i>Informasi</h3>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
          <div>
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Status</div>
            <span :style="`background:${getStatusConfig(peminjaman.status).bg};border:1px solid ${getStatusConfig(peminjaman.status).border};color:${getStatusConfig(peminjaman.status).color};border-radius:6px;padding:4px 12px;font-size:12px;font-weight:700`">
              {{ getStatusConfig(peminjaman.status).label }}
            </span>
          </div>
          <div v-if="peminjaman.surat_keterangan">
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Surat Keterangan</div>
            <a :href="`/storage-public/${peminjaman.surat_keterangan}`" target="_blank"
              style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#fff;border:1.5px solid #f59e0b;color:#d97706;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
              <i class="fa fa-file-download"></i> Lihat Surat
            </a>
          </div>
          <div v-if="peminjaman.waktu_dikembalikan">
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Dikembalikan Pada</div>
            <div style="font-size:13px;color:#475569">{{ formatDateTime(peminjaman.waktu_dikembalikan) }}</div>
          </div>

          <!-- Cetak -->
          <template v-if="['disetujui', 'selesai', 'menunggu_kembali'].includes(peminjaman.status)">
            <hr style="border:none;border-top:1px solid #f1f5f9;margin:4px 0">
            <a :href="`/dosen/peminjaman-lab/${peminjaman.id}/cetak-bukti-pinjam`" target="_blank"
              style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700">
              <i class="fa fa-print"></i> Cetak Bukti Pinjam
            </a>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
