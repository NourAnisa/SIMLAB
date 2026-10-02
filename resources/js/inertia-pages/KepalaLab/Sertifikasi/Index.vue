<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  pendaftarans: Object
})

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<template>
  <AppLayout title="Validasi Sertifikasi">
    <p class="page-title">Validasi Sertifikasi</p>
    <p class="page-sub">Validasi dan setujui pendaftaran sertifikasi kompetensi mahasiswa.</p>
    
    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div>
      <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
          <h3><i class="fa fa-trophy" style="color:#f59e0b;margin-right:8px"></i>Pendaftaran Menunggu Penilaian</h3>
          <span style="font-size:12px;color:#94a3b8;font-weight:600">{{ pendaftarans.total }} peserta</span>
        </div>
        <div style="overflow-x:auto">
          <div v-if="pendaftarans.data.length === 0" style="padding:60px;text-align:center">
            <div style="font-size:48px;margin-bottom:12px">✅</div>
            <p style="font-size:15px;color:#64748b;font-weight:500">Tidak ada pendaftaran yang menunggu penilaian.</p>
          </div>
          <table v-else style="width:100%;border-collapse:collapse">
            <thead>
              <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
                <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Peserta</th>
                <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Skema</th>
                <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Jadwal Ujian</th>
                <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Token</th>
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
                  {{ p.jadwal?.tanggal ? formatDate(p.jadwal.tanggal) : '-' }}
                </td>
                <td style="padding:14px 16px">
                  <code style="background:#eef2ff;padding:4px 10px;border-radius:6px;font-size:13px;font-weight:700;color:#4f46e5;letter-spacing:1px">{{ p.token_ujian }}</code>
                </td>
                <td style="padding:14px 16px;text-align:center">
                  <div style="display:flex;align-items:center;justify-content:center;gap:8px">
                    <Link :href="`/kepala_lab/sertifikasi/${p.id}`"
                      style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700">
                      <i class="fa fa-eye"></i> Detail & Nilai
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="pendaftarans.last_page > 1" style="display:flex;justify-content:center;gap:8px;padding:16px 20px;border-top:1px solid #f1f5f9">
          <Link v-for="(link, index) in pendaftarans.links" :key="index"
            :href="link.url || ''"
            v-html="link.label"
            preserve-scroll
            :style="`padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
