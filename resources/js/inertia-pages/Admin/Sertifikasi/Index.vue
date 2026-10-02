<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  pendaftarans: Object,
  stats: Object,
  filters: Object
})

const filterStatus = ref(props.filters?.status || '')

function loadData() {
  const params = {}
  if (filterStatus.value) params.status = filterStatus.value

  router.get('/admin/sertifikasi', params, {
    preserveState: true,
    preserveScroll: true
  })
}

function applyFilter() {
  loadData()
}

function resetFilter() {
  filterStatus.value = ''
  loadData()
}

function verifikasi(id) {
  if (!confirm('Verifikasi pendaftaran ini dan generate token ujian?')) return
  router.post(`/admin/sertifikasi/${id}/verifikasi`, {}, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', label: 'Menunggu' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', label: 'Diverifikasi' },
    'lulus': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', label: 'Lulus' },
    'tidak_lulus': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', label: 'Tidak Lulus' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: status }
}
</script>

<template>
  <AppLayout title="Manajemen Sertifikasi">
    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <!-- STATS -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
      <div class="card">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:16px">
          <div style="width:44px;height:44px;border-radius:12px;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-users" style="color:#4f46e5;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ stats.total }}</div>
            <div style="font-size:12px;color:#64748b">Total Pendaftar</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:16px">
          <div style="width:44px;height:44px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-clock" style="color:#d97706;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ stats.menunggu }}</div>
            <div style="font-size:12px;color:#64748b">Menunggu</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:16px">
          <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-search" style="color:#1d4ed8;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ stats.diverifikasi }}</div>
            <div style="font-size:12px;color:#64748b">Diverifikasi</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:16px 20px;display:flex;align-items:center;gap:16px">
          <div style="width:44px;height:44px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-trophy" style="color:#15803d;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ stats.lulus }}</div>
            <div style="font-size:12px;color:#64748b">Lulus</div>
          </div>
        </div>
      </div>
    </div>

    <!-- FILTER -->
    <div class="card" style="margin-bottom:20px">
      <div class="card-body" style="padding:14px 20px">
        <form @submit.prevent="applyFilter" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
          <select v-model="filterStatus" style="padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff">
            <option value="">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="lulus">Lulus</option>
            <option value="tidak_lulus">Tidak Lulus</option>
          </select>
          <button type="submit"
            style="padding:9px 20px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
            <i class="fa fa-filter"></i> Filter
          </button>
          <button v-if="filterStatus" type="button" @click="resetFilter"
            style="padding:9px 16px;background:#f1f5f9;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
            <i class="fa fa-times"></i> Reset
          </button>
        </form>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-trophy" style="color:#f59e0b;margin-right:8px"></i>Daftar Pendaftaran Sertifikasi</h3>
        <span v-if="pendaftarans.total" style="font-size:12px;color:#94a3b8">{{ pendaftarans.total }} pendaftar</span>
      </div>
      
      <div style="overflow-x:auto">
        <div v-if="pendaftarans.data.length === 0" style="padding:60px;text-align:center">
          <div style="font-size:48px;margin-bottom:12px">📋</div>
          <p style="font-size:15px;color:#64748b;font-weight:500">Belum ada pendaftar sertifikasi.</p>
        </div>
        
        <table v-else style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Mahasiswa</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Skema</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Jadwal</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Status</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Token Ujian</th>
              <th style="padding:14px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in pendaftarans.data" :key="p.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s"
              @mouseover="$event.currentTarget.style.background='#fafbff'" 
              @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:14px 16px">
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ p.user?.name }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ p.user?.nim_nip || p.user?.email }}</div>
              </td>
              <td style="padding:14px 16px;font-size:13px;color:#475569;font-weight:500">
                {{ p.jadwal?.skema?.nama_skema || '-' }}
              </td>
              <td style="padding:14px 16px;font-size:13px;color:#475569">
                {{ formatDate(p.jadwal?.tanggal) }}
              </td>
              <td style="padding:14px 16px">
                <span :style="`background:${getStatusConfig(p.status).bg};border:1px solid ${getStatusConfig(p.status).border};color:${getStatusConfig(p.status).color};border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700`">
                  {{ getStatusConfig(p.status).label }}
                </span>
              </td>
              <td style="padding:14px 16px">
                <code v-if="p.token_ujian" style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:13px;font-weight:700;color:#4f46e5">{{ p.token_ujian }}</code>
                <span v-else style="color:#94a3b8;font-size:13px">—</span>
              </td>
              <td style="padding:14px 16px;text-align:center">
                <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                  <Link :href="`/admin/sertifikasi/${p.id}`"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700">
                    <i class="fa fa-eye"></i> Detail
                  </Link>
                  
                  <button v-if="p.status === 'menunggu'" @click="verifikasi(p.id)"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit">
                    <i class="fa fa-check"></i> 
                    Verifikasi
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pendaftarans.last_page > 1" style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
        <Link v-for="(link, index) in pendaftarans.links" :key="index"
          :href="link.url || ''"
          v-html="link.label"
          preserve-scroll
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
