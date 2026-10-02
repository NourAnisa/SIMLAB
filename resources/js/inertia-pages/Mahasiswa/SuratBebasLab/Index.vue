<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  surats: Object
})

const submitting = ref(false)
const error = ref(null)

function submitSurat() {
  submitting.value = true
  error.value = null
  
  router.post('/mahasiswa/surat-bebas-lab/ajukan', {}, {
    preserveScroll: true,
    onError: (errors) => {
      error.value = errors.error || 'Gagal mengajukan surat bebas lab.'
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu', dot: '#d97706' },
    'masih_tanggungan': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-exclamation-triangle', label: 'Masih Tanggungan', dot: '#ef4444' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Diverifikasi', dot: '#3b82f6' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-check-circle', label: 'Disetujui', dot: '#16a34a' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function truncate(str, n) {
  if (!str) return ''
  return (str.length > n) ? str.slice(0, n - 1) + '...' : str
}
</script>

<template>
  <AppLayout title="Surat Bebas Lab">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
      <p class="page-title" style="margin-bottom:0">Surat Bebas Lab</p>
      <button @click="submitSurat" :disabled="submitting"
        style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;border-radius:10px;font-size:13px;font-weight:700;border:none;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(14,165,233,.3);transition:all .2s"
        @mouseover="!submitting ? $event.currentTarget.style.transform='translateY(-1px)' : null" 
        @mouseout="!submitting ? $event.currentTarget.style.transform='translateY(0)' : null"
        :style="submitting ? 'opacity:0.7;cursor:not-allowed' : ''">
        <i v-if="submitting" class="fa fa-spinner fa-spin"></i>
        <i v-else class="fa fa-plus"></i> 
        {{ submitting ? 'Mengajukan...' : 'Ajukan Surat Baru' }}
      </button>
    </div>
    <p class="page-sub">Ajukan surat keterangan bebas tanggungan laboratorium.</p>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>
    
    <div v-if="$page.props.flash?.info" style="background:#e0f2fe;border:1px solid #7dd3fc;color:#0369a1;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-info-circle"></i> {{ $page.props.flash.info }}
    </div>

    <div v-if="error || Object.keys($page.props.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px">
      {{ error || Object.values($page.props.errors)[0] }}
    </div>

    <!-- Info Box -->
    <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;padding:18px 20px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start">
      <div style="font-size:28px;flex-shrink:0">📋</div>
      <div>
        <h4 style="font-size:14px;font-weight:700;color:#0369a1;margin-bottom:4px">Apa itu Surat Bebas Lab?</h4>
        <p style="font-size:13px;color:#0c4a6e;line-height:1.6">Surat keterangan yang menyatakan Anda tidak memiliki tanggungan peminjaman laboratorium. Diperlukan untuk keperluan wisuda, nilai akhir, dan administrasi lainnya.</p>
      </div>
    </div>

    <div>
      <div v-if="surats.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
        <div style="font-size:56px;margin-bottom:16px">📄</div>
        <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Belum Ada Pengajuan</h3>
        <p style="color:#64748b;font-size:14px;margin-bottom:24px">Anda belum pernah mengajukan surat bebas lab. Klik tombol di atas untuk mengajukan.</p>
      </div>
      
      <div v-else style="display:flex;flex-direction:column;gap:14px">
        <Link v-for="s in surats.data" :key="s.id" :href="`/mahasiswa/surat-bebas-lab/${s.id}`"
          style="display:block;text-decoration:none"
          @mouseover="$event.currentTarget.querySelector('.card').style.transform='translateY(-2px)'; $event.currentTarget.querySelector('.card').style.boxShadow='0 8px 24px rgba(0,0,0,.08)'"
          @mouseout="$event.currentTarget.querySelector('.card').style.transform='translateY(0)'; $event.currentTarget.querySelector('.card').style.boxShadow='none'">
          
          <div class="card" :style="`border-left:4px solid ${getStatusConfig(s.status).dot};transition:all .25s`">
            <div class="card-body" style="padding:18px 24px">
              <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                <div style="display:flex;align-items:center;gap:12px">
                  <div :style="`width:40px;height:40px;border-radius:10px;background:${getStatusConfig(s.status).bg};border:1px solid ${getStatusConfig(s.status).border};display:flex;align-items:center;justify-content:center;flex-shrink:0`">
                    <i :class="`fa ${getStatusConfig(s.status).icon}`" :style="`color:${getStatusConfig(s.status).color};font-size:16px`"></i>
                  </div>
                  <div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a">Surat Bebas Lab #{{ s.id }}</div>
                    <div style="font-size:13px;color:#64748b;margin-top:2px">
                      <i class="fa fa-calendar" style="margin-right:4px"></i>
                      Diajukan {{ formatDate(s.tanggal_pengajuan) }}
                      <template v-if="s.tanggal_disetujui">
                        · <span style="color:#16a34a"><i class="fa fa-check-circle"></i> Disetujui {{ formatDate(s.tanggal_disetujui) }}</span>
                      </template>
                    </div>
                    <div v-if="s.catatan" style="font-size:12px;color:#ef4444;margin-top:4px">
                      <i class="fa fa-comment"></i> {{ truncate(s.catatan, 60) }}
                    </div>
                  </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
                  <div :style="`background:${getStatusConfig(s.status).bg};border:1px solid ${getStatusConfig(s.status).border};color:${getStatusConfig(s.status).color};`" style="border-radius:8px;padding:5px 12px;font-size:12px;font-weight:700;white-space:nowrap">
                    <i :class="`fa ${getStatusConfig(s.status).icon}`"></i> {{ getStatusConfig(s.status).label }}
                  </div>
                  <span style="font-size:11px;color:#94a3b8">Lihat Detail →</span>
                </div>
              </div>
            </div>
          </div>
        </Link>

        <!-- Pagination -->
        <div v-if="surats.last_page > 1" style="display:flex;justify-content:center;gap:8px;margin-top:16px">
          <Link v-for="(link, index) in surats.links" :key="index"
            :href="link.url || ''"
            v-html="link.label"
            preserve-scroll
            :style="`padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#0ea5e9' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
