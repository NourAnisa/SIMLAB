<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  pendaftaran: Object
})

function verifikasi() {
  if (!confirm(`Verifikasi dan generate token ujian untuk ${props.pendaftaran.user?.name}?`)) return
  router.post(`/admin/sertifikasi/${props.pendaftaran.id}/verifikasi`, {}, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

function formatDateTime(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi — Menunggu Ujian', dot: '#3b82f6' },
    'lulus': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-trophy', label: 'Lulus', dot: '#16a34a' },
    'tidak_lulus': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Tidak Lulus', dot: '#ef4444' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

const steps = [
  { key: 'menunggu', label: 'Mendaftar', icon: 'fa-paper-plane', color: '#f59e0b' },
  { key: 'diverifikasi', label: 'Diverifikasi', icon: 'fa-check', color: '#3b82f6' },
  { key: 'lulus', label: 'Selesai Ujian', icon: 'fa-trophy', color: '#16a34a' },
]

const order = { 'menunggu': 0, 'diverifikasi': 1, 'lulus': 2, 'tidak_lulus': 1 }

function isDone(stepIndex) {
  const current = order[props.pendaftaran.status] ?? 0
  return stepIndex <= current
}

function isActive(stepIndex) {
  const current = order[props.pendaftaran.status] ?? 0
  return stepIndex === current
}
</script>

<template>
  <AppLayout title="Detail Pendaftaran Sertifikasi">
    <div style="margin-bottom:20px">
      <Link href="/admin/sertifikasi" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" @mouseover="$event.currentTarget.style.color='#4f46e5'" @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <!-- DETAIL CARD -->
      <div>
        <div class="card" style="margin-bottom:16px" :style="`border-left:4px solid ${getStatusConfig(pendaftaran.status).dot}`">
          <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa fa-trophy" style="color:#fff;font-size:18px"></i>
              </div>
              <div>
                <h3 style="font-size:17px;font-weight:700;color:#0f172a">{{ pendaftaran.jadwal?.skema?.nama_skema }}</h3>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Didaftarkan {{ formatDateTime(pendaftaran.created_at) }}</div>
              </div>
            </div>
            <div :style="`background:${getStatusConfig(pendaftaran.status).bg};border:1px solid ${getStatusConfig(pendaftaran.status).border};color:${getStatusConfig(pendaftaran.status).color};border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px;white-space:nowrap`">
              <i :class="`fa ${getStatusConfig(pendaftaran.status).icon}`"></i> {{ getStatusConfig(pendaftaran.status).label }}
            </div>
          </div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b;width:200px">
                  <i class="fa fa-certificate" style="color:#f59e0b;margin-right:8px"></i>Skema
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">
                  {{ pendaftaran.jadwal?.skema?.nama_skema }}
                </td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-calendar" style="color:#f59e0b;margin-right:8px"></i>Tanggal Ujian
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">
                  {{ formatDate(pendaftaran.jadwal?.tanggal) }}
                </td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-users" style="color:#f59e0b;margin-right:8px"></i>Kuota Jadwal
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">
                  {{ pendaftaran.jadwal?.kuota }} peserta (sisa: {{ pendaftaran.jadwal?.sisa_kuota }})
                </td>
              </tr>
              <tr v-if="pendaftaran.dokumen_berkas" style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-file" style="color:#f59e0b;margin-right:8px"></i>Dokumen
                </td>
                <td style="padding:13px 16px">
                  <a :href="`/files/${pendaftaran.dokumen_berkas}`" target="_blank"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#fff;border:1.5px solid #4f46e5;color:#4f46e5;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600"
                    @mouseover="$event.currentTarget.style.background='#4f46e5';$event.currentTarget.style.color='#fff'" 
                    @mouseout="$event.currentTarget.style.background='#fff';$event.currentTarget.style.color='#4f46e5'">
                    <i class="fa fa-file-download"></i> Lihat Dokumen
                  </a>
                </td>
              </tr>
              <tr v-if="pendaftaran.token_ujian" style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-key" style="color:#f59e0b;margin-right:8px"></i>Token Ujian
                </td>
                <td style="padding:13px 16px">
                  <code style="background:#eef2ff;padding:6px 14px;border-radius:8px;font-size:16px;font-weight:800;color:#4f46e5;letter-spacing:2px">{{ pendaftaran.token_ujian }}</code>
                </td>
              </tr>
              <tr v-if="pendaftaran.hasil_validasi">
                <td style="padding:13px 16px;font-size:13px;color:#64748b;vertical-align:top">
                  <i class="fa fa-clipboard-check" style="color:#f59e0b;margin-right:8px"></i>Hasil Validasi
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#475569;line-height:1.6">{{ pendaftaran.hasil_validasi }}</td>
              </tr>
            </table>
          </div>
        </div>

        <!-- TINDAKAN -->
        <div v-if="pendaftaran.status === 'menunggu'" class="card" style="border-left:4px solid #f59e0b">
          <div class="card-header">
            <h3><i class="fa fa-gavel" style="color:#d97706;margin-right:8px"></i>Tindakan Admin</h3>
          </div>
          <div class="card-body">
            <p style="font-size:13px;color:#64748b;margin-bottom:16px">Verifikasi dokumen dan berikan token ujian kepada peserta ini.</p>
            <button @click="verifikasi"
              style="width:100%;padding:13px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(16,185,129,.3)">
              <i class="fa fa-check"></i> 
              Verifikasi & Generate Token Ujian
            </button>
          </div>
        </div>
        
        <div v-else-if="pendaftaran.status === 'diverifikasi'" class="card" style="border-left:4px solid #3b82f6">
          <div class="card-body" style="text-align:center;padding:24px">
            <div style="font-size:36px;margin-bottom:8px">🎯</div>
            <p style="font-size:15px;font-weight:700;color:#1d4ed8">Sudah Diverifikasi</p>
            <p style="font-size:13px;color:#64748b;margin-top:4px">Token ujian telah diberikan. Peserta dapat mengikuti ujian.</p>
            <div style="margin-top:12px;display:inline-block;background:#eef2ff;border:1.5px solid #c7d2fe;border-radius:10px;padding:10px 24px">
              <span style="font-size:11px;color:#6366f1;font-weight:700;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px">Token Ujian</span>
              <code style="font-size:20px;font-weight:800;color:#4f46e5;letter-spacing:3px">{{ pendaftaran.token_ujian }}</code>
            </div>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div>
        <!-- PESERTA INFO -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-user" style="color:#4f46e5;margin-right:8px"></i>Informasi Peserta</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
              <img v-if="pendaftaran.user?.foto_profil" :src="`/files/${pendaftaran.user.foto_profil}`" alt="foto"
                style="width:52px;height:52px;border-radius:50%;object-fit:cover;flex-shrink:0">
              <div v-else style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700;flex-shrink:0">
                {{ pendaftaran.user?.name ? pendaftaran.user.name.substring(0, 1).toUpperCase() : 'M' }}
              </div>
              <div>
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ pendaftaran.user?.name }}</div>
                <div style="font-size:12px;color:#64748b">Mahasiswa</div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-envelope" style="width:16px;color:#94a3b8"></i> {{ pendaftaran.user?.email }}
              </div>
              <div v-if="pendaftaran.user?.nim_nip" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-id-card" style="width:16px;color:#94a3b8"></i> NIM: {{ pendaftaran.user.nim_nip }}
              </div>
              <div v-if="pendaftaran.user?.jurusan" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-graduation-cap" style="width:16px;color:#94a3b8"></i> {{ pendaftaran.user.jurusan }}
              </div>
            </div>
          </div>
        </div>

        <!-- PROGRES STATUS -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-list-check" style="color:#4f46e5;margin-right:8px"></i>Progres</h3>
          </div>
          <div class="card-body">
            <div style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:16px;bottom:16px;width:2px;background:#e2e8f0"></div>
              
              <div v-for="(s, i) in steps" :key="i" style="position:relative" :style="i === steps.length - 1 ? '' : 'margin-bottom:20px'">
                <div style="position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center"
                  :style="`background:${isDone(i) ? s.color : '#e2e8f0'}; ${isActive(i) ? 'box-shadow:0 0 0 4px '+s.color+'33' : ''}`">
                  <i :class="`fa ${s.icon}`" :style="`color:${isDone(i) ? '#fff' : '#94a3b8'};font-size:10px`"></i>
                </div>
                <div :style="`font-size:13px;font-weight:${isActive(i) ? '700' : '600'};color:${isDone(i) ? '#0f172a' : '#94a3b8'};margin-bottom:2px`">
                  {{ s.label }}
                  <span v-if="isActive(i)" :style="`font-size:10px;background:${s.color};color:#fff;border-radius:50px;padding:1px 7px;margin-left:4px;font-weight:700`">Saat ini</span>
                </div>
              </div>
            </div>
            
            <div v-if="pendaftaran.status === 'tidak_lulus'" style="margin-top:12px;background:#fff1f2;border:1px solid #fda4af;border-radius:8px;padding:10px;text-align:center">
              <span style="font-size:13px;font-weight:700;color:#be123c"><i class="fa fa-times-circle" style="margin-right:4px"></i>Tidak Lulus</span>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body" style="padding:14px 16px">
            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;margin:0">
              <i class="fa fa-clock"></i> Didaftarkan {{ formatDateTime(pendaftaran.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
