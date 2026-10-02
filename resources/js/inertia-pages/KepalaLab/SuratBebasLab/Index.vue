<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  surats: Object
})

function setujui(id, name) {
  if (!confirm(`Setujui surat bebas lab untuk ${name}?`)) return
  router.post(`/kepala_lab/surat-bebas-lab/${id}/setujui`, {}, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<template>
  <AppLayout title="Surat Bebas Lab">
    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div class="card">
      <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <h3><i class="fa fa-file-alt" style="color:#0ea5e9;margin-right:8px"></i>Permohonan Surat Bebas Lab — Menunggu Persetujuan</h3>
        <span v-if="surats.total" style="font-size:12px;color:#94a3b8">{{ surats.total }} permohonan</span>
      </div>
      
      <div style="overflow-x:auto">
        <div v-if="surats.data.length === 0" style="padding:60px;text-align:center">
          <div style="font-size:48px;margin-bottom:12px">✅</div>
          <p style="font-size:15px;color:#64748b;font-weight:500">Tidak ada permohonan yang menunggu persetujuan.</p>
        </div>
        
        <table v-else style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Mahasiswa</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">NIM</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Tgl Pengajuan</th>
              <th style="padding:14px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="s in surats.data" :key="s.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s"
              @mouseover="$event.currentTarget.style.background='#fafbff'" 
              @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:14px 16px">
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ s.user?.name }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ s.user?.email }}</div>
              </td>
              <td style="padding:14px 16px;font-size:13px;color:#475569">
                {{ s.user?.nim_nip || '-' }}
              </td>
              <td style="padding:14px 16px;font-size:13px;color:#475569">
                {{ formatDate(s.tanggal_pengajuan) }}
              </td>
              <td style="padding:14px 16px;text-align:center">
                <div style="display:flex;align-items:center;justify-content:center;gap:8px">
                  <Link :href="`/kepala_lab/surat-bebas-lab/${s.id}`"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700">
                    <i class="fa fa-eye"></i> Detail
                  </Link>
                  
                  <button @click="setujui(s.id, s.user?.name)"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit">
                    <i class="fa fa-check"></i> 
                    Setujui
                  </button>
                </div>
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
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#0ea5e9' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
