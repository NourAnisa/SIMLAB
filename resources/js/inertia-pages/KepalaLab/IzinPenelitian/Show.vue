<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  izin: Object
})

const showRejectForm = ref(false)

const formSetuju = useForm({})
const formTolak = useForm({
  catatan_verifikasi: ''
})

function setujui() {
  if (!confirm('Setujui izin penelitian ini?')) return
  formSetuju.post(`/kepala_lab/izin-penelitian/${props.izin.id}/setujui`, {
    preserveScroll: true
  })
}

function tolak() {
  formTolak.post(`/kepala_lab/izin-penelitian/${props.izin.id}/tolak`, {
    preserveScroll: true,
    onSuccess: () => {
      showRejectForm.value = false
    }
  })
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function formatDateTime(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function getStatusConfig(status) {
  const configs = {
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Menunggu Persetujuan Kepala Lab', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Ditolak', dot: '#ef4444' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

const steps = [
  { key: 'menunggu', label: 'Diajukan', desc: 'Permohonan diterima', icon: 'fa-paper-plane', color: '#f59e0b' },
  { key: 'diverifikasi', label: 'Diverifikasi Admin', desc: 'Diteruskan ke Kepala Lab', icon: 'fa-user-check', color: '#3b82f6' },
  { key: 'disetujui', label: 'Disetujui', desc: 'Izin telah disetujui', icon: 'fa-check-circle', color: '#10b981' }
]
const order = { 'menunggu': 0, 'diverifikasi': 1, 'disetujui': 2, 'ditolak': 1 }
</script>

<template>
  <AppLayout title="Detail Izin Penelitian">
    <div style="margin-bottom:20px">
      <Link href="/kepala_lab/izin-penelitian" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#10b981'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <!-- MAIN DETAIL -->
      <div>
        <div class="card" style="margin-bottom:16px" :style="`border-left:4px solid ${getStatusConfig(izin.status).dot}`">
          <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa fa-flask" style="color:#fff;font-size:18px"></i>
              </div>
              <div>
                <h3 style="font-size:17px;font-weight:700;color:#0f172a">{{ izin.judul_penelitian }}</h3>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Diajukan {{ formatDateTime(izin.created_at) }}</div>
              </div>
            </div>
            <div :style="`background:${getStatusConfig(izin.status).bg};border:1px solid ${getStatusConfig(izin.status).border};color:${getStatusConfig(izin.status).color};border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px;white-space:nowrap;margin-top:16px`">
              <i :class="`fa ${getStatusConfig(izin.status).icon}`"></i> {{ getStatusConfig(izin.status).label }}
            </div>
          </div>
          <div class="card-body">
            <div style="margin-bottom:20px">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Tujuan Penelitian</div>
              <p style="font-size:14px;color:#475569;line-height:1.8;white-space:pre-line">{{ izin.tujuan }}</p>
            </div>

            <div v-if="izin.data_dibutuhkan && izin.data_dibutuhkan.length > 0" style="margin-bottom:20px">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
                <i class="fa fa-list-check" style="color:#6366f1;margin-right:4px"></i> Data yang Dibutuhkan
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
                <div v-for="(item, idx) in izin.data_dibutuhkan" :key="idx" style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;font-size:13px;color:#4338ca">
                  <i class="fa fa-check-circle" style="color:#6366f1;flex-shrink:0;font-size:12px"></i>
                  {{ item }}
                </div>
              </div>
            </div>

            <div v-if="izin.dokumen_berkas" style="padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Dokumen Lampiran</div>
              <a :href="`/files/${izin.dokumen_berkas}`" target="_blank"
                style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:#fff;border:1.5px solid #10b981;color:#10b981;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:all .2s"
                @mouseover="$event.currentTarget.style.background='#10b981';$event.currentTarget.style.color='#fff'" 
                @mouseout="$event.currentTarget.style.background='#fff';$event.currentTarget.style.color='#10b981'">
                <i class="fa fa-file-download"></i> Lihat / Download Dokumen
              </a>
            </div>

            <div v-if="izin.catatan_verifikasi" :style="`padding:14px 16px;background:${izin.status === 'ditolak' ? '#fff1f2' : '#eff6ff'};border-radius:10px;border-left:3px solid ${izin.status === 'ditolak' ? '#ef4444' : '#3b82f6'}`">
              <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Catatan</div>
              <p style="font-size:14px;color:#475569;line-height:1.7">{{ izin.catatan_verifikasi }}</p>
            </div>
          </div>
        </div>

        <!-- ACTION CARD (only if diverifikasi) -->
        <div v-if="izin.status === 'diverifikasi'" class="card" style="border-left:4px solid #10b981">
          <div class="card-header" style="background:linear-gradient(135deg,rgba(16,185,129,.05),transparent)">
            <h3><i class="fa fa-gavel" style="color:#059669;margin-right:8px"></i>Keputusan Kepala Lab</h3>
          </div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <!-- APPROVE -->
              <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:20px">
                <h4 style="font-size:14px;font-weight:700;color:#15803d;margin-bottom:8px"><i class="fa fa-check-circle" style="margin-right:6px"></i>Setujui</h4>
                <p style="font-size:13px;color:#64748b;margin-bottom:16px;line-height:1.5">Setujui permohonan izin penelitian ini secara resmi.</p>
                <form @submit.prevent="setujui">
                  <button type="submit" :disabled="formSetuju.processing"
                    style="width:100%;padding:10px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px"
                    :style="formSetuju.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                    <i v-if="formSetuju.processing" class="fa fa-spinner fa-spin"></i>
                    <i v-else class="fa fa-check"></i> 
                    Setujui Sekarang
                  </button>
                </form>
              </div>

              <!-- REJECT -->
              <div style="background:#fff1f2;border:1px solid #fda4af;border-radius:12px;padding:20px">
                <h4 style="font-size:14px;font-weight:700;color:#be123c;margin-bottom:8px"><i class="fa fa-times-circle" style="margin-right:6px"></i>Tolak</h4>
                <p style="font-size:13px;color:#64748b;margin-bottom:12px;line-height:1.5">Tolak permohonan jika tidak memenuhi syarat.</p>
                
                <button v-if="!showRejectForm" @click="showRejectForm = true"
                  style="width:100%;padding:10px;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px">
                  <i class="fa fa-times"></i> Tolak Permohonan
                </button>
                
                <div v-else style="margin-top:12px">
                  <form @submit.prevent="tolak">
                    <p v-if="formTolak.errors.catatan_verifikasi" style="color:#ef4444;font-size:12px;margin-bottom:6px">{{ formTolak.errors.catatan_verifikasi }}</p>
                    <textarea v-model="formTolak.catatan_verifikasi" rows="3" placeholder="Alasan penolakan..." required
                      style="width:100%;padding:10px;border:1.5px solid #fda4af;border-radius:8px;font-size:13px;font-family:inherit;resize:vertical;outline:none;background:#fff"
                      @focus="$event.currentTarget.style.borderColor='#ef4444'" 
                      @blur="$event.currentTarget.style.borderColor='#fda4af'"></textarea>
                    <div style="display:flex;gap:6px;margin-top:8px">
                      <button @click="showRejectForm = false" type="button" style="flex:1;padding:9px;background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                        Batal
                      </button>
                      <button type="submit" :disabled="formTolak.processing" style="flex:1;padding:9px;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer">
                        {{ formTolak.processing ? 'Proses...' : 'Konfirmasi' }}
                      </button>
                    </div>
                  </form>
                </div>
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
            <h3><i class="fa fa-user" style="color:#10b981;margin-right:8px"></i>Informasi Pemohon</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
              <img v-if="izin.user?.foto_profil" :src="`/files/${izin.user.foto_profil}`" alt="foto" style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0">
              <div v-else style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;flex-shrink:0">
                {{ izin.user?.name ? izin.user.name.substring(0, 1).toUpperCase() : 'M' }}
              </div>
              <div>
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ izin.user?.name }}</div>
                <div style="font-size:12px;color:#64748b;text-transform:capitalize">{{ izin.user?.role }}</div>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
              <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-envelope" style="width:16px;color:#94a3b8"></i> {{ izin.user?.email }}
              </div>
              <div v-if="izin.user?.nim_nip" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-id-card" style="width:16px;color:#94a3b8"></i> {{ izin.user.nim_nip }}
              </div>
            </div>
          </div>
        </div>

        <!-- STATUS TIMELINE -->
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-list-check" style="color:#10b981;margin-right:8px"></i>Progres Status</h3>
          </div>
          <div class="card-body">
            <div v-if="izin.status === 'ditolak'" style="text-align:center;padding:16px 0">
              <div style="font-size:36px;margin-bottom:8px">❌</div>
              <div style="font-size:14px;font-weight:700;color:#be123c">Permohonan Ditolak</div>
              <div style="font-size:12px;color:#94a3b8;margin-top:4px">{{ formatDateTime(izin.updated_at) }}</div>
            </div>
            <div v-else style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:16px;bottom:16px;width:2px;background:#e2e8f0"></div>
              
              <div v-for="(s, i) in steps" :key="i" :style="`position:relative;margin-bottom:${i === steps.length - 1 ? '0' : '20px'}`">
                <div :style="`position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;background:${i <= (order[izin.status] || 0) ? s.color : '#e2e8f0'};display:flex;align-items:center;justify-content:center;${i === (order[izin.status] || 0) ? 'box-shadow:0 0 0 4px ' + s.color + '33' : ''}`">
                  <i :class="`fa ${s.icon}`" :style="`color:${i <= (order[izin.status] || 0) ? '#fff' : '#94a3b8'};font-size:10px`"></i>
                </div>
                <div :style="`font-size:13px;font-weight:${i === (order[izin.status] || 0) ? '700' : '600'};color:${i <= (order[izin.status] || 0) ? '#0f172a' : '#94a3b8'};margin-bottom:2px`">
                  {{ s.label }}
                  <span v-if="i === (order[izin.status] || 0)" :style="`font-size:10px;background:${s.color};color:#fff;border-radius:50px;padding:1px 7px;margin-left:4px;font-weight:700`">Saat ini</span>
                </div>
                <div :style="`font-size:11px;color:${i <= (order[izin.status] || 0) ? '#64748b' : '#cbd5e1'}`">{{ s.desc }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
