<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  surats: Object,
  filters: Object
})

const filterSearch = ref(props.filters?.search || '')
const filterStatus = ref(props.filters?.status || '')

function loadData() {
  const params = {}
  if (filterSearch.value) params.search = filterSearch.value
  if (filterStatus.value) params.status = filterStatus.value

  router.get('/admin/surat-bebas-lab', params, {
    preserveState: true,
    preserveScroll: true
  })
}

let filterTimeout = null
watch([filterSearch, filterStatus], () => {
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => loadData(), 300)
})

function clearFilters() {
  filterSearch.value = ''
  filterStatus.value = ''
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric'
  })
}

function getStatusConfig(status) {
  const cfg = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', label: 'Menunggu' },
    'masih_tanggungan': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', label: 'Masih Tanggungan' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', label: 'Diverifikasi' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', label: 'Disetujui' }
  }
  return cfg[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: status }
}
</script>

<template>
  <AppLayout title="Surat Bebas Lab">
    <!-- FILTER -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-body" style="padding:14px 20px">
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
          <input v-model="filterSearch" placeholder="Cari nama mahasiswa..."
            style="flex:1;min-width:200px;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none"
            @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
          <select v-model="filterStatus" style="padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff"
            @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
            <option value="">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="masih_tanggungan">Masih Tanggungan</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="disetujui">Disetujui</option>
          </select>
          <button v-if="filterSearch || filterStatus" @click="clearFilters"
            style="padding:9px 16px;background:#f1f5f9;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
            <i class="fa fa-times"></i> Reset
          </button>
        </div>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <h3><i class="fa fa-file-alt" style="color:#0ea5e9;margin-right:8px"></i>Daftar Permohonan Surat Bebas Lab</h3>
        <span style="font-size:12px;color:#94a3b8" v-if="surats.total">{{ surats.total }} permohonan</span>
      </div>
      
      <div v-if="surats.data.length === 0" style="padding:60px;text-align:center">
        <div style="font-size:48px;margin-bottom:12px">📭</div>
        <p style="font-size:15px;color:#64748b;font-weight:500">Belum ada permohonan surat bebas lab yang sesuai.</p>
      </div>
      
      <div v-else style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Mahasiswa</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">NIM</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Tgl Pengajuan</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Status</th>
              <th style="padding:14px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="s in surats.data" :key="s.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s" @mouseover="$event.currentTarget.style.background='#fafbff'" @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:14px 16px">
                <div style="display:flex;align-items:center;gap:10px">
                  <img v-if="s.user?.foto_profil" :src="`/files/${s.user.foto_profil}`" alt="foto" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0">
                  <div v-else style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#0284c7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0">
                    {{ s.user?.name?.substring(0, 1)?.toUpperCase() || '?' }}
                  </div>
                  <div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a">{{ s.user?.name || '-' }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ s.user?.email || '-' }}</div>
                  </div>
                </div>
              </td>
              <td style="padding:14px 16px;font-size:13px;color:#475569">
                {{ s.user?.nim_nip || '-' }}
              </td>
              <td style="padding:14px 16px;font-size:13px;color:#475569">
                {{ formatDate(s.tanggal_pengajuan) }}
              </td>
              <td style="padding:14px 16px">
                <span :style="`background:${getStatusConfig(s.status).bg};border:1px solid ${getStatusConfig(s.status).border};color:${getStatusConfig(s.status).color};border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700`">
                  {{ getStatusConfig(s.status).label }}
                </span>
              </td>
              <td style="padding:14px 16px;text-align:center">
                <Link :href="`/admin/surat-bebas-lab/${s.id}`"
                  style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700">
                  <i class="fa fa-eye"></i> Detail
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="surats.last_page > 1" style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
        <Link v-for="(link, index) in surats.links" :key="index"
          :href="link.url || ''"
          v-html="link.label"
          preserve-scroll
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
