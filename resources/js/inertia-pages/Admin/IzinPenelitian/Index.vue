<script setup>
import { ref, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  izins: Object,
  counts: Object,
  filters: Object
})

const filterStatus = ref(props.filters?.status || '')

function loadData() {
  const params = {}
  if (filterStatus.value) params.status = filterStatus.value

  router.get('/admin/izin-penelitian', params, {
    preserveState: true,
    preserveScroll: true
  })
}

watch(filterStatus, () => {
  loadData()
})

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric'
  })
}

function limitStr(str, max) {
  if (!str) return ''
  return str.length > max ? str.substring(0, max) + '...' : str
}

function verifikasi(id) {
  if (!confirm('Verifikasi izin penelitian ini?')) return
  router.post(`/admin/izin-penelitian/${id}/verifikasi`, {}, { preserveScroll: true })
}

function getStatusConfig(status) {
  const cfg = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', label: 'Menunggu' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', label: 'Diverifikasi' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', label: 'Disetujui' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', label: 'Ditolak' }
  }
  return cfg[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: status }
}
</script>

<template>
  <AppLayout title="Izin Penelitian">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
      <p class="page-title" style="margin-bottom:0">Izin Penelitian</p>
    </div>
    <p class="page-sub">Kelola dan verifikasi semua permohonan izin penelitian dari mahasiswa dan dosen.</p>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <!-- STAT CARDS -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:24px">
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,rgba(79,70,229,.1),rgba(124,58,237,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="font-size:18px">📋</span>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a">{{ counts.all }}</div>
            <div style="font-size:11px;color:#64748b">Total Pengajuan</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,rgba(245,158,11,.1),rgba(217,119,6,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="font-size:18px">⏳</span>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#d97706">{{ counts.menunggu }}</div>
            <div style="font-size:11px;color:#64748b">Menunggu</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,rgba(59,130,246,.1),rgba(29,78,216,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="font-size:18px">🔍</span>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#1d4ed8">{{ counts.diverifikasi }}</div>
            <div style="font-size:11px;color:#64748b">Diverifikasi</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(5,150,105,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="font-size:18px">✅</span>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#16a34a">{{ counts.disetujui }}</div>
            <div style="font-size:11px;color:#64748b">Disetujui</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,rgba(239,68,68,.1),rgba(220,38,38,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <span style="font-size:18px">❌</span>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#dc2626">{{ counts.ditolak }}</div>
            <div style="font-size:11px;color:#64748b">Ditolak</div>
          </div>
        </div>
      </div>
    </div>

    <!-- FILTER TABS -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
      <button @click="filterStatus = ''" :style="filterStatus === '' ? 'background:#4f46e5;color:#fff;border-color:#4f46e5' : 'background:#fff;color:#64748b;border-color:#e2e8f0'" style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;border:1.5px solid;cursor:pointer;transition:all .2s">
        Semua
      </button>
      <button @click="filterStatus = 'menunggu'" :style="filterStatus === 'menunggu' ? 'background:#4f46e5;color:#fff;border-color:#4f46e5' : 'background:#fff;color:#64748b;border-color:#e2e8f0'" style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;border:1.5px solid;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:6px">
        Menunggu <span v-if="counts.menunggu > 0" :style="filterStatus === 'menunggu' ? 'background:rgba(255,255,255,.25);color:#fff' : 'background:#d97706;color:#fff'" style="border-radius:50px;padding:1px 7px;font-size:11px">{{ counts.menunggu }}</span>
      </button>
      <button @click="filterStatus = 'diverifikasi'" :style="filterStatus === 'diverifikasi' ? 'background:#4f46e5;color:#fff;border-color:#4f46e5' : 'background:#fff;color:#64748b;border-color:#e2e8f0'" style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;border:1.5px solid;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:6px">
        Diverifikasi <span v-if="counts.diverifikasi > 0" :style="filterStatus === 'diverifikasi' ? 'background:rgba(255,255,255,.25);color:#fff' : 'background:#1d4ed8;color:#fff'" style="border-radius:50px;padding:1px 7px;font-size:11px">{{ counts.diverifikasi }}</span>
      </button>
      <button @click="filterStatus = 'disetujui'" :style="filterStatus === 'disetujui' ? 'background:#4f46e5;color:#fff;border-color:#4f46e5' : 'background:#fff;color:#64748b;border-color:#e2e8f0'" style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;border:1.5px solid;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:6px">
        Disetujui <span v-if="counts.disetujui > 0" :style="filterStatus === 'disetujui' ? 'background:rgba(255,255,255,.25);color:#fff' : 'background:#16a34a;color:#fff'" style="border-radius:50px;padding:1px 7px;font-size:11px">{{ counts.disetujui }}</span>
      </button>
      <button @click="filterStatus = 'ditolak'" :style="filterStatus === 'ditolak' ? 'background:#4f46e5;color:#fff;border-color:#4f46e5' : 'background:#fff;color:#64748b;border-color:#e2e8f0'" style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;border:1.5px solid;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:6px">
        Ditolak <span v-if="counts.ditolak > 0" :style="filterStatus === 'ditolak' ? 'background:rgba(255,255,255,.25);color:#fff' : 'background:#dc2626;color:#fff'" style="border-radius:50px;padding:1px 7px;font-size:11px">{{ counts.ditolak }}</span>
      </button>
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <h3><i class="fa fa-flask" style="color:#10b981;margin-right:8px"></i>Daftar Permohonan</h3>
        <span style="font-size:13px;color:#64748b">{{ izins.total }} data</span>
      </div>
      
      <div v-if="izins.data.length === 0" style="text-align:center;padding:50px 24px">
        <div style="font-size:48px;margin-bottom:12px">🔬</div>
        <p style="color:#64748b;font-size:14px">Tidak ada permohonan izin penelitian{{ filterStatus ? ' dengan status ini' : '' }}.</p>
      </div>
      
      <div v-else style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0">
              <th style="padding:12px 20px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">#</th>
              <th style="padding:12px 20px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Pemohon</th>
              <th style="padding:12px 20px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Judul Penelitian</th>
              <th style="padding:12px 20px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Tanggal</th>
              <th style="padding:12px 20px;text-align:left;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Status</th>
              <th style="padding:12px 20px;text-align:center;font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(izin, index) in izins.data" :key="izin.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s" @mouseover="$event.currentTarget.style.background='#fafbfc'" @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:14px 20px;font-size:13px;color:#94a3b8;font-weight:500">{{ (izins.current_page - 1) * 15 + index + 1 }}</td>
              <td style="padding:14px 20px">
                <div style="display:flex;align-items:center;gap:10px">
                  <img v-if="izin.user?.foto_profil" :src="`/files/${izin.user.foto_profil}`" alt="foto" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0">
                  <div v-else style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0">
                    {{ izin.user?.name?.substring(0, 1)?.toUpperCase() || '?' }}
                  </div>
                  <div>
                    <div style="font-size:13px;font-weight:600;color:#0f172a">{{ izin.user?.name || '-' }}</div>
                    <div style="font-size:11px;color:#94a3b8">{{ izin.user?.email || '-' }}</div>
                  </div>
                </div>
              </td>
              <td style="padding:14px 20px">
                <div style="font-size:13px;font-weight:600;color:#0f172a;max-width:260px" :title="izin.judul_penelitian">{{ limitStr(izin.judul_penelitian, 55) }}</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px" :title="izin.tujuan">{{ limitStr(izin.tujuan, 60) }}</div>
              </td>
              <td style="padding:14px 20px;font-size:13px;color:#64748b;white-space:nowrap">{{ formatDate(izin.created_at) }}</td>
              <td style="padding:14px 20px">
                <span :style="`background:${getStatusConfig(izin.status).bg};border:1px solid ${getStatusConfig(izin.status).border};color:${getStatusConfig(izin.status).color};border-radius:6px;padding:4px 10px;font-size:12px;font-weight:700`">
                  {{ getStatusConfig(izin.status).label }}
                </span>
              </td>
              <td style="padding:14px 20px;text-align:center">
                <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                  <Link :href="`/admin/izin-penelitian/${izin.id}`"
                    style="padding:6px 12px;background:#f1f5f9;color:#475569;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px;transition:all .2s"
                    @mouseover="$event.currentTarget.style.background='#e2e8f0'" @mouseout="$event.currentTarget.style.background='#f1f5f9'">
                    <i class="fa fa-eye"></i> Detail
                  </Link>
                  <button v-if="izin.status === 'menunggu'" @click="verifikasi(izin.id)"
                    style="padding:6px 12px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:4px">
                    <i class="fa fa-check"></i> Verifikasi
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="izins.last_page > 1" style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
        <Link v-for="(link, index) in izins.links" :key="index"
          :href="link.url || ''"
          v-html="link.label"
          preserve-scroll
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
