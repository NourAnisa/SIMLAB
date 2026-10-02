<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  surat: Object
})

const showTanggunganForm = ref(false)
const tanggunganForm = useForm({
  catatan: ''
})
const actionForm = useForm({})

function verifikasi() {
  if (!confirm('Verifikasi permohonan surat bebas lab ini?')) return
  actionForm.post(`/admin/surat-bebas-lab/${props.surat.id}/verifikasi`, {
    preserveScroll: true
  })
}

function tandaiTanggungan() {
  tanggunganForm.post(`/admin/surat-bebas-lab/${props.surat.id}/tanggungan`, {
    preserveScroll: true,
    onSuccess: () => {
      showTanggunganForm.value = false
    }
  })
}

function formatDateFull(dateStr) {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
  })
}

function formatDateTime(dateStr) {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  }) + ', ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

function getStatusConfig(status) {
  const cfg = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'masih_tanggungan': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-exclamation-triangle', label: 'Masih Tanggungan', dot: '#ef4444' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi Admin', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' }
  }
  return cfg[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

function getSteps(status) {
  const steps = [
    { key: 'menunggu', label: 'Diajukan', icon: 'fa-paper-plane', color: '#0ea5e9' },
    { key: 'diverifikasi', label: 'Diverifikasi Admin', icon: 'fa-user-check', color: '#3b82f6' },
    { key: 'disetujui', label: 'Disetujui Kepala Lab', icon: 'fa-check-circle', color: '#16a34a' }
  ]
  const order = { 'menunggu': 0, 'masih_tanggungan': 0, 'diverifikasi': 1, 'disetujui': 2 }
  const cur = order[status] !== undefined ? order[status] : 0
  
  return steps.map((s, i) => ({
    ...s,
    done: i <= cur,
    active: i === cur
  }))
}
</script>

<template>
  <AppLayout title="Detail Surat Bebas Lab">
    <div style="margin-bottom:20px">
      <Link href="/admin/surat-bebas-lab" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" @mouseover="$event.currentTarget.style.color='#0ea5e9'" @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <!-- DETAIL CARD -->
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
              <i :class="['fa', getStatusConfig(surat.status).icon]"></i> {{ getStatusConfig(surat.status).label }}
            </div>
          </div>
          <div class="card-body">
            <table style="width:100%;border-collapse:collapse">
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b;width:200px">
                  <i class="fa fa-calendar" style="color:#0ea5e9;margin-right:8px"></i>Tanggal Pengajuan
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">
                  {{ formatDateFull(surat.tanggal_pengajuan) }}
                </td>
              </tr>
              <tr v-if="surat.tanggal_disetujui" style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-calendar-check" style="color:#16a34a;margin-right:8px"></i>Tanggal Disetujui
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#16a34a;font-weight:600">
                  {{ formatDateFull(surat.tanggal_disetujui) }}
                </td>
              </tr>
              <tr v-if="surat.catatan">
                <td style="padding:13px 16px;font-size:13px;color:#64748b;vertical-align:top">
                  <i class="fa fa-comment-alt" style="color:#ef4444;margin-right:8px"></i>Catatan
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#dc2626;line-height:1.6">{{ surat.catatan }}</td>
              </tr>
            </table>

            <div v-if="surat.status === 'masih_tanggungan'" style="margin:16px;padding:16px;background:#fff1f2;border:1px solid #fda4af;border-radius:10px">
              <p style="font-size:13px;font-weight:700;color:#be123c;margin:0 0 6px 0"><i class="fa fa-exclamation-triangle"></i> Masih Ada Tanggungan</p>
              <p style="font-size:13px;color:#9f1239;line-height:1.6;margin:0">Mahasiswa masih memiliki tanggungan peminjaman lab yang belum diselesaikan.</p>
            </div>
          </div>
        </div>

        <!-- TINDAKAN ADMIN -->
        <div v-if="surat.status === 'menunggu' || surat.status === 'masih_tanggungan'" class="card" style="border-left:4px solid #f59e0b">
          <div class="card-header" style="background:linear-gradient(135deg,rgba(245,158,11,.05),transparent)">
            <h3><i class="fa fa-gavel" style="color:#d97706;margin-right:8px"></i>Tindakan Admin</h3>
          </div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <!-- VERIFIKASI -->
              <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:20px">
                <h4 style="font-size:14px;font-weight:700;color:#15803d;margin:0 0 8px 0"><i class="fa fa-check-circle" style="margin-right:6px"></i>Verifikasi</h4>
                <p style="font-size:13px;color:#64748b;margin:0 0 16px 0;line-height:1.5">Verifikasi dan teruskan ke Kepala Lab untuk persetujuan.</p>
                <button @click="verifikasi" :disabled="actionForm.processing"
                  style="width:100%;padding:10px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px"
                  :style="actionForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                  <i v-if="actionForm.processing" class="fa fa-spinner fa-spin"></i>
                  <i v-else class="fa fa-check"></i> Verifikasi Sekarang
                </button>
              </div>

              <!-- TANGGUNGAN -->
              <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:20px">
                <h4 style="font-size:14px;font-weight:700;color:#c2410c;margin:0 0 8px 0"><i class="fa fa-exclamation-triangle" style="margin-right:6px"></i>Tandai Tanggungan</h4>
                <p style="font-size:13px;color:#64748b;margin:0 0 12px 0;line-height:1.5">Tandai jika mahasiswa masih memiliki tanggungan lab.</p>
                <button v-if="!showTanggunganForm" @click="showTanggunganForm = true"
                  style="width:100%;padding:10px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px">
                  <i class="fa fa-exclamation-triangle"></i> Tandai Tanggungan
                </button>
                <form v-if="showTanggunganForm" @submit.prevent="tandaiTanggungan" style="margin-top:12px">
                  <p v-if="tanggunganForm.errors.catatan" style="color:#ef4444;font-size:12px;margin:0 0 6px 0">{{ tanggunganForm.errors.catatan }}</p>
                  <textarea v-model="tanggunganForm.catatan" rows="3" placeholder="Jelaskan tanggungan yang belum diselesaikan..." required
                    style="width:100%;padding:10px;border:1.5px solid #fed7aa;border-radius:8px;font-size:13px;font-family:inherit;resize:vertical;outline:none;background:#fff;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#f97316'" @blur="$event.currentTarget.style.borderColor='#fed7aa'"></textarea>
                  <div style="display:flex;gap:8px;margin-top:8px">
                    <button @click="showTanggunganForm = false" type="button" style="flex:1;padding:9px;background:#fff;color:#ea580c;border:1px solid #fed7aa;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">
                      Batal
                    </button>
                    <button type="submit" :disabled="tanggunganForm.processing" style="flex:2;padding:9px;background:#ea580c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit"
                      :style="tanggunganForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                      <i v-if="tanggunganForm.processing" class="fa fa-spinner fa-spin"></i>
                      <span v-else>Konfirmasi</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div>
        <!-- PEMOHON INFO -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-user" style="color:#4f46e5;margin-right:8px"></i>Informasi Mahasiswa</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
              <img v-if="surat.user?.foto_profil" :src="`/files/${surat.user.foto_profil}`" alt="foto"
                style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0">
              <div v-else style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#0284c7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;flex-shrink:0">
                {{ surat.user?.name?.substring(0, 1)?.toUpperCase() || '?' }}
              </div>
              <div>
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ surat.user?.name || '-' }}</div>
                <div style="font-size:12px;color:#64748b">Mahasiswa</div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-envelope" style="width:16px;color:#94a3b8"></i> {{ surat.user?.email || '-' }}
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

        <!-- PROGRES STATUS -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-list-check" style="color:#4f46e5;margin-right:8px"></i>Progres Status</h3>
          </div>
          <div class="card-body">
            <div v-if="surat.status === 'masih_tanggungan'" style="background:#fff1f2;border:1px solid #fda4af;border-radius:10px;padding:14px;text-align:center">
              <div style="font-size:28px;margin-bottom:8px">⚠️</div>
              <p style="font-size:13px;font-weight:700;color:#be123c;margin:0">Masih Tanggungan</p>
              <p style="font-size:12px;color:#64748b;margin:4px 0 0 0">Mahasiswa belum menyelesaikan tanggungan lab.</p>
            </div>
            <div v-else style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:16px;bottom:16px;width:2px;background:#e2e8f0"></div>
              <div v-for="(s, i) in getSteps(surat.status)" :key="i" :style="`position:relative;margin-bottom:${i === 2 ? '0' : '20px'}`">
                <div :style="`position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;background:${s.done ? s.color : '#e2e8f0'};display:flex;align-items:center;justify-content:center;${s.active ? 'box-shadow:0 0 0 4px '+s.color+'33' : ''}`">
                  <i :class="['fa', s.icon]" :style="`color:${s.done ? '#fff' : '#94a3b8'};font-size:10px`"></i>
                </div>
                <div :style="`font-size:13px;font-weight:${s.active ? '700' : '600'};color:${s.done ? '#0f172a' : '#94a3b8'};margin-bottom:2px`">
                  {{ s.label }}
                  <span v-if="s.active" :style="`font-size:10px;background:${s.color};color:#fff;border-radius:50px;padding:1px 7px;margin-left:4px;font-weight:700`">Saat ini</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- TIMESTAMP -->
        <div class="card">
          <div class="card-body" style="padding:14px 16px">
            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;margin:0">
              <i class="fa fa-clock"></i> Diajukan {{ formatDateTime(surat.created_at) }}
            </p>
            <p v-if="surat.updated_at !== surat.created_at" style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px;margin:6px 0 0 0">
              <i class="fa fa-edit"></i> Diupdate {{ formatDateTime(surat.updated_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
