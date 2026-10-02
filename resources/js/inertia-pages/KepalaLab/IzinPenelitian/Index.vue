<script setup>
import { ref, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  izins: Object,
  counts: Object,
  filters: Object
})

const currentFilter = ref(props.filters?.status || null)

function loadData() {
  const params = {}
  if (currentFilter.value) params.status = currentFilter.value

  router.get('/kepala_lab/izin-penelitian', params, {
    preserveState: true,
    preserveScroll: true
  })
}

function applyFilter(status) {
  currentFilter.value = status
  loadData()
}

function setujui(id) {
  if (!confirm('Setujui izin penelitian ini?')) return
  router.post(`/kepala_lab/izin-penelitian/${id}/setujui`, {}, {
    preserveScroll: true
  })
}

function tolak(id) {
  if (!confirm('Tolak izin penelitian ini?')) return
  router.post(`/kepala_lab/izin-penelitian/${id}/tolak`, {}, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function getStatusConfig(status) {
  const configs = {
    'diverifikasi': { bg: '#eff6ff', c: '#1d4ed8', label: 'Menunggu Persetujuan' },
    'disetujui': { bg: '#f0fdf4', c: '#15803d', label: 'Disetujui' },
    'ditolak': { bg: '#fff1f2', c: '#be123c', label: 'Ditolak' }
  }
  return configs[status] || { bg: '#f8fafc', c: '#64748b', label: status }
}
</script>

<template>
  <AppLayout title="Izin Penelitian">
    <p class="page-title">Daftar Izin Penelitian</p>
    <p class="page-sub">Setujui atau tolak permohonan izin penelitian yang telah diverifikasi oleh Admin.</p>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>
    
    <div>
      <!-- STATS -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        <div style="background:#fff;border-radius:14px;padding:18px 20px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:14px">
          <div style="width:44px;height:44px;border-radius:12px;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-list" style="color:#4f46e5;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ counts.all }}</div>
            <div style="font-size:12px;color:#94a3b8;font-weight:500">Total</div>
          </div>
        </div>
        <div style="background:#fff;border-radius:14px;padding:18px 20px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:14px">
          <div style="width:44px;height:44px;border-radius:12px;background:rgba(59,130,246,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-search" style="color:#3b82f6;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ counts.diverifikasi }}</div>
            <div style="font-size:12px;color:#94a3b8;font-weight:500">Menunggu Persetujuan</div>
          </div>
        </div>
        <div style="background:#fff;border-radius:14px;padding:18px 20px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:14px">
          <div style="width:44px;height:44px;border-radius:12px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-check-circle" style="color:#10b981;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ counts.disetujui }}</div>
            <div style="font-size:12px;color:#94a3b8;font-weight:500">Disetujui</div>
          </div>
        </div>
        <div style="background:#fff;border-radius:14px;padding:18px 20px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:14px">
          <div style="width:44px;height:44px;border-radius:12px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-times-circle" style="color:#ef4444;font-size:18px"></i>
          </div>
          <div>
            <div style="font-size:22px;font-weight:800;color:#0f172a">{{ counts.ditolak }}</div>
            <div style="font-size:12px;color:#94a3b8;font-weight:500">Ditolak</div>
          </div>
        </div>
      </div>

      <!-- FILTER -->
      <div class="card" style="margin-bottom:20px">
        <div class="card-body" style="padding:14px 20px">
          <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <span style="font-size:13px;color:#64748b;font-weight:600">Filter:</span>
            
            <button @click="applyFilter(null)"
              :style="`padding:6px 14px;border-radius:50px;font-size:13px;font-weight:600;border:1.5px solid ${!currentFilter ? '#10b981' : '#e2e8f0'};background:${!currentFilter ? '#10b981' : '#fff'};color:${!currentFilter ? '#fff' : '#64748b'};cursor:pointer`">
              Semua ({{ counts.all }})
            </button>
            <button @click="applyFilter('diverifikasi')"
              :style="`padding:6px 14px;border-radius:50px;font-size:13px;font-weight:600;border:1.5px solid ${currentFilter === 'diverifikasi' ? '#10b981' : '#e2e8f0'};background:${currentFilter === 'diverifikasi' ? '#10b981' : '#fff'};color:${currentFilter === 'diverifikasi' ? '#fff' : '#64748b'};cursor:pointer`">
              Menunggu ({{ counts.diverifikasi }})
            </button>
            <button @click="applyFilter('disetujui')"
              :style="`padding:6px 14px;border-radius:50px;font-size:13px;font-weight:600;border:1.5px solid ${currentFilter === 'disetujui' ? '#10b981' : '#e2e8f0'};background:${currentFilter === 'disetujui' ? '#10b981' : '#fff'};color:${currentFilter === 'disetujui' ? '#fff' : '#64748b'};cursor:pointer`">
              Disetujui ({{ counts.disetujui }})
            </button>
            <button @click="applyFilter('ditolak')"
              :style="`padding:6px 14px;border-radius:50px;font-size:13px;font-weight:600;border:1.5px solid ${currentFilter === 'ditolak' ? '#10b981' : '#e2e8f0'};background:${currentFilter === 'ditolak' ? '#10b981' : '#fff'};color:${currentFilter === 'ditolak' ? '#fff' : '#64748b'};cursor:pointer`">
              Ditolak ({{ counts.ditolak }})
            </button>
          </div>
        </div>
      </div>

      <!-- TABLE -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-flask" style="color:#10b981;margin-right:8px"></i>Daftar Permohonan</h3>
        </div>
        <div class="card-body" style="padding:0">
          <div v-if="izins.data.length === 0" style="text-align:center;padding:48px 24px;color:#94a3b8">
            <div style="font-size:48px;margin-bottom:12px">🔬</div>
            <p style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">Tidak ada permohonan</p>
            <p style="font-size:13px">Belum ada izin penelitian yang sesuai dengan filter.</p>
          </div>
          <table v-else style="width:100%;border-collapse:collapse">
            <thead>
              <tr style="border-bottom:2px solid #f1f5f9;background:#f8fafc">
                <th style="padding:12px 20px;font-size:12px;font-weight:700;color:#94a3b8;text-align:left;text-transform:uppercase;letter-spacing:.5px">Mahasiswa</th>
                <th style="padding:12px 20px;font-size:12px;font-weight:700;color:#94a3b8;text-align:left;text-transform:uppercase;letter-spacing:.5px">Judul Penelitian</th>
                <th style="padding:12px 20px;font-size:12px;font-weight:700;color:#94a3b8;text-align:left;text-transform:uppercase;letter-spacing:.5px">Tanggal</th>
                <th style="padding:12px 20px;font-size:12px;font-weight:700;color:#94a3b8;text-align:center;text-transform:uppercase;letter-spacing:.5px">Status</th>
                <th style="padding:12px 20px;font-size:12px;font-weight:700;color:#94a3b8;text-align:center;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="izin in izins.data" :key="izin.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s"
                @mouseover="$event.currentTarget.style.background='#f8fafc'" 
                @mouseout="$event.currentTarget.style.background=''">
                <td style="padding:14px 20px">
                  <div style="display:flex;align-items:center;gap:10px">
                    <img v-if="izin.user?.foto_profil" :src="`/files/${izin.user.foto_profil}`" alt="foto" style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0">
                    <div v-else style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0">
                      {{ izin.user?.name ? izin.user.name.substring(0, 1).toUpperCase() : 'M' }}
                    </div>
                    <div>
                      <div style="font-size:14px;font-weight:600;color:#0f172a">{{ izin.user?.name }}</div>
                      <div style="font-size:12px;color:#94a3b8">{{ izin.user?.nim_nip || izin.user?.email }}</div>
                    </div>
                  </div>
                </td>
                <td style="padding:14px 20px">
                  <div style="font-size:13px;color:#475569;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ izin.judul_penelitian }}</div>
                </td>
                <td style="padding:14px 20px;font-size:13px;color:#64748b;white-space:nowrap">
                  {{ formatDate(izin.created_at) }}
                </td>
                <td style="padding:14px 20px;text-align:center">
                  <span :style="`padding:4px 12px;border-radius:50px;font-size:11px;font-weight:700;background:${getStatusConfig(izin.status).bg};color:${getStatusConfig(izin.status).c}`">
                    {{ getStatusConfig(izin.status).label }}
                  </span>
                </td>
                <td style="padding:14px 20px;text-align:center">
                  <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <button v-if="izin.status === 'diverifikasi'" @click="setujui(izin.id)"
                      style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#10b981;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
                      <i class="fa fa-check"></i> Setujui
                    </button>
                    <button v-if="izin.status === 'diverifikasi'" @click="tolak(izin.id)"
                      style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
                      <i class="fa fa-times"></i> Tolak
                    </button>
                    <Link :href="`/kepala_lab/izin-penelitian/${izin.id}`"
                      style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f1f5f9;color:#475569;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600">
                      <i class="fa fa-eye"></i> Detail
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div v-if="izins.last_page > 1" style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
            <Link v-for="(link, index) in izins.links" :key="index"
              :href="link.url || ''"
              v-html="link.label"
              preserve-scroll
              :style="`padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#10b981' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
