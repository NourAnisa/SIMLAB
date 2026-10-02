<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjamans: Object,
  filters: Object
})

const filterTanggal = ref(props.filters?.tanggal || '')
const filterRuangan = ref(props.filters?.ruangan || '')
const filterStatus = ref(props.filters?.status || '')

function loadData() {
  const params = {}
  if (filterTanggal.value) params.tanggal = filterTanggal.value
  if (filterRuangan.value) params.ruangan = filterRuangan.value
  if (filterStatus.value) params.status = filterStatus.value

  router.get('/admin/peminjaman-lab', params, {
    preserveState: true,
    preserveScroll: true
  })
}

let filterTimeout = null
watch([filterTanggal, filterRuangan, filterStatus], () => {
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => loadData(), 300)
})

function clearFilters() {
  filterTanggal.value = ''
  filterRuangan.value = ''
  filterStatus.value = ''
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
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', label: 'Menunggu' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', label: 'Diverifikasi' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', label: 'Disetujui' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', label: 'Ditolak' },
    'menunggu_kembali': { bg: '#fdf4ff', border: '#e9d5ff', color: '#6b21a8', label: 'Menunggu Kembali' },
    'dikembalikan': { bg: '#f8fafc', border: '#cbd5e1', color: '#334155', label: 'Dikembalikan' }
  }
  return cfg[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: status }
}
</script>

<template>
  <AppLayout title="Peminjaman Lab">
    <p class="page-title">Manajemen Peminjaman Lab</p>
    <p class="page-sub">Verifikasi dan kelola semua permohonan peminjaman ruangan laboratorium.</p>

    <!-- Filter -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-body" style="padding:16px 20px">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px">Tanggal</label>
            <input type="date" v-model="filterTanggal"
              style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px">Ruangan</label>
            <input type="text" v-model="filterRuangan" placeholder="Cari ruangan..."
              style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;width:180px">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:4px">Status</label>
            <select v-model="filterStatus" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none">
              <option value="">Semua Status</option>
              <option value="menunggu">Menunggu</option>
              <option value="diverifikasi">Diverifikasi</option>
              <option value="disetujui">Disetujui</option>
              <option value="ditolak">Ditolak</option>
              <option value="menunggu_kembali">Menunggu Kembali</option>
              <option value="dikembalikan">Dikembalikan</option>
            </select>
          </div>
          <button v-if="filterTanggal || filterRuangan || filterStatus" @click="clearFilters"
            style="padding:8px 14px;background:#f1f5f9;color:#64748b;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer">
            <i class="fa fa-times"></i> Reset
          </button>
        </div>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div v-if="peminjamans.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
      <div style="font-size:48px;margin-bottom:16px">🏫</div>
      <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Belum Ada Data Peminjaman</h3>
      <p style="color:#64748b;font-size:14px">Belum ada permohonan peminjaman lab yang sesuai dengan filter.</p>
    </div>

    <div v-else class="card">
      <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <h3><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Daftar Peminjaman</h3>
        <span style="font-size:13px;color:#64748b">{{ peminjamans.total }} permohonan</span>
      </div>
      <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0">
              <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Mahasiswa</th>
              <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Ruangan</th>
              <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Tanggal & Waktu</th>
              <th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Status</th>
              <th style="padding:12px 16px;text-align:center;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in peminjamans.data" :key="p.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s" @mouseover="$event.currentTarget.style.background='#fafbff'" @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:14px 16px">
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ p.user?.name || '-' }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ p.user?.nim_nip || p.user?.email || '-' }}</div>
              </td>
              <td style="padding:14px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ p.ruangan }}</td>
              <td style="padding:14px 16px">
                <div style="font-size:13px;color:#0f172a;font-weight:600">{{ formatDate(p.tanggal_pinjam) }}</div>
                <div style="font-size:12px;color:#64748b">{{ formatTime(p.jam_mulai) }} – {{ formatTime(p.jam_selesai) }}</div>
              </td>
              <td style="padding:14px 16px">
                <span :style="`background:${getStatusConfig(p.status).bg};border:1px solid ${getStatusConfig(p.status).border};color:${getStatusConfig(p.status).color};padding:4px 10px;border-radius:6px;font-size:12px;font-weight:700`">
                  {{ getStatusConfig(p.status).label }}
                </span>
              </td>
              <td style="padding:14px 16px;text-align:center">
                <Link :href="`/admin/peminjaman-lab/${p.id}`"
                  style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:7px;text-decoration:none;font-size:12px;font-weight:600">
                  <i class="fa fa-eye"></i> Detail
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="peminjamans.last_page > 1" style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
        <Link v-for="(link, index) in peminjamans.links" :key="index"
          :href="link.url || ''"
          v-html="link.label"
          preserve-scroll
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
