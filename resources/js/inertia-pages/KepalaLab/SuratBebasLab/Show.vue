<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  surat: Object
})

const formAction = useForm({})

function setujui() {
  if (!confirm(`Setujui dan terbitkan surat bebas lab untuk ${props.surat.user?.name}?`)) return
  formAction.post(`/kepala_lab/surat-bebas-lab/${props.surat.id}/setujui`, {
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
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu', dot: '#d97706' },
    'masih_tanggungan': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-exclamation-triangle', label: 'Masih Tanggungan', dot: '#ef4444' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi — Menunggu Persetujuan Anda', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}
</script>

<template>
  <AppLayout title="Detail Surat Bebas Lab">
    <div style="margin-bottom:20px">
      <Link href="/kepala_lab/surat-bebas-lab" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#0ea5e9'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <!-- DETAIL -->
      <div>
        <div class="card" style="margin-bottom:16px" :style="`border-left:4px solid ${getStatusConfig(surat.status).dot}`">
          <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#0ea5e9,#0284c7);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa fa-file-alt" style="color:#fff;font-size:18px"></i>
              </div>
              <div>
                <h3 style="font-size:17px;font-weight:700;color:#0f172a">Surat Bebas Lab #{{ surat.id }}</h3>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Diajukan {{ formatDateTime(surat.created_at) }}</div>
              </div>
            </div>
            <div :style="`background:${getStatusConfig(surat.status).bg};border:1px solid ${getStatusConfig(surat.status).border};color:${getStatusConfig(surat.status).color};border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px;white-space:nowrap`">
              <i :class="`fa ${getStatusConfig(surat.status).icon}`"></i> {{ getStatusConfig(surat.status).label }}
            </div>
          </div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b;width:200px">
                  <i class="fa fa-calendar" style="color:#0ea5e9;margin-right:8px"></i>Tanggal Pengajuan
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">
                  {{ formatDate(surat.tanggal_pengajuan) }}
                </td>
              </tr>
              <tr v-if="surat.tanggal_disetujui" style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-calendar-check" style="color:#16a34a;margin-right:8px"></i>Tanggal Disetujui
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#16a34a;font-weight:600">
                  {{ formatDate(surat.tanggal_disetujui) }}
                </td>
              </tr>
              <tr v-if="surat.catatan">
                <td style="padding:13px 16px;font-size:13px;color:#64748b;vertical-align:top">
                  <i class="fa fa-comment-alt" style="color:#ef4444;margin-right:8px"></i>Catatan Admin
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#dc2626;line-height:1.6">{{ surat.catatan }}</td>
              </tr>
            </table>
          </div>
        </div>

        <!-- TINDAKAN KEPALA LAB -->
        <div v-if="surat.status === 'diverifikasi'" class="card" style="border-left:4px solid #10b981">
          <div class="card-header" style="background:linear-gradient(135deg,rgba(16,185,129,.05),transparent)">
            <h3><i class="fa fa-stamp" style="color:#059669;margin-right:8px"></i>Tindakan Kepala Lab</h3>
          </div>
          <div class="card-body">
            <p style="font-size:13px;color:#64748b;margin-bottom:16px">Dokumen telah diverifikasi oleh admin. Berikan persetujuan untuk menerbitkan surat bebas lab.</p>
            <button @click="setujui" :disabled="formAction.processing"
              style="width:100%;padding:13px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(16,185,129,.3)"
              :style="formAction.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="formAction.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-check-double"></i> 
              {{ formAction.processing ? 'Memproses...' : 'Setujui & Terbitkan Surat Bebas Lab' }}
            </button>
          </div>
        </div>
        
        <div v-else-if="surat.status === 'disetujui'" class="card">
          <div class="card-body" style="text-align:center;padding:24px">
            <div style="font-size:36px;margin-bottom:8px">✅</div>
            <p style="font-size:15px;font-weight:700;color:#15803d">Surat Bebas Lab Telah Disetujui</p>
            <p style="font-size:13px;color:#64748b;margin-top:4px;margin-bottom:16px">Disetujui pada {{ formatDate(surat.tanggal_disetujui) }}</p>
            <a :href="`/kepala_lab/surat-bebas-lab/${surat.id}/cetak`" target="_blank"
              style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(14,165,233,.3)">
              <i class="fa fa-print"></i> Cetak Surat
            </a>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-user" style="color:#4f46e5;margin-right:8px"></i>Informasi Mahasiswa</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
              <img v-if="surat.user?.foto_profil" :src="`/files/${surat.user.foto_profil}`" alt="foto" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0">
              <div v-else style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#0284c7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;flex-shrink:0">
                {{ surat.user?.name ? surat.user.name.substring(0, 1).toUpperCase() : 'M' }}
              </div>
              <div>
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ surat.user?.name }}</div>
                <div style="font-size:12px;color:#64748b">Mahasiswa</div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-envelope" style="width:16px;color:#94a3b8"></i> {{ surat.user?.email }}
              </div>
              <div v-if="surat.user?.nim_nip" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-id-card" style="width:16px;color:#94a3b8"></i> NIM: {{ surat.user.nim_nip }}
              </div>
              <div v-if="surat.user?.jurusan" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-graduation-cap" style="width:16px;color:#94a3b8"></i> {{ surat.user.jurusan }}
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body" style="padding:14px 16px">
            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px">
              <i class="fa fa-clock"></i> Diajukan {{ formatDateTime(surat.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
