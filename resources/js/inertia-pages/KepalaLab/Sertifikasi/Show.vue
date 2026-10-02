<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  pendaftaran: Object
})

const form = useForm({
  status: 'lulus',
  nilai: '',
  hasil_validasi: ''
})

function submitNilai() {
  if (!confirm('Simpan penilaian untuk peserta ini? Tindakan ini tidak dapat diubah.')) return
  
  form.post(`/kepala_lab/sertifikasi/${props.pendaftaran.id}/nilai`, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatDateTime(dateString) {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function getStatusConfig(status) {
  const configs = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', icon: 'fa-clock', label: 'Menunggu Verifikasi', dot: '#d97706' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', icon: 'fa-search', label: 'Menunggu Penilaian', dot: '#3b82f6' },
    'lulus': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', icon: 'fa-trophy', label: 'Lulus', dot: '#16a34a' },
    'tidak_lulus': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', icon: 'fa-times-circle', label: 'Tidak Lulus', dot: '#ef4444' }
  }
  return configs[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', icon: 'fa-question', label: status, dot: '#94a3b8' }
}

function getGradeInfo(nilai) {
  if (nilai >= 86) return { label: 'EXCELLENT', color: '#15803d', bg: '#f0fdf4', border: '#86efac' }
  if (nilai >= 70) return { label: 'GOOD', color: '#1d4ed8', bg: '#eff6ff', border: '#bfdbfe' }
  if (nilai >= 55) return { label: 'AVERAGE', color: '#92400e', bg: '#fffbeb', border: '#fde68a' }
  return { label: 'POOR', color: '#be123c', bg: '#fff1f2', border: '#fda4af' }
}
</script>

<template>
  <AppLayout title="Penilaian Sertifikasi">
    <div style="margin-bottom:20px">
      <Link href="/kepala_lab/sertifikasi" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#f59e0b'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <!-- DETAIL + PENILAIAN -->
      <div>
        <div class="card" style="margin-bottom:16px" :style="`border-left:4px solid ${getStatusConfig(pendaftaran.status).dot}`">
          <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa fa-trophy" style="color:#fff;font-size:18px"></i>
              </div>
              <div>
                <h3 style="font-size:17px;font-weight:700;color:#0f172a">{{ pendaftaran.jadwal?.skema?.nama_skema }}</h3>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">Didaftarkan {{ formatDate(pendaftaran.created_at) }}</div>
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
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ pendaftaran.jadwal?.skema?.nama_skema }}</td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-calendar" style="color:#f59e0b;margin-right:8px"></i>Tanggal Ujian
                </td>
                <td style="padding:13px 16px;font-size:14px;color:#0f172a;font-weight:600">{{ pendaftaran.jadwal?.tanggal ? formatDate(pendaftaran.jadwal.tanggal) : '-' }}</td>
              </tr>
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-key" style="color:#f59e0b;margin-right:8px"></i>Token Ujian
                </td>
                <td style="padding:13px 16px">
                  <code style="background:#eef2ff;padding:6px 14px;border-radius:8px;font-size:16px;font-weight:800;color:#4f46e5;letter-spacing:2px">{{ pendaftaran.token_ujian }}</code>
                </td>
              </tr>
              <tr v-if="pendaftaran.dokumen_berkas" style="border-bottom:1px solid #f1f5f9">
                <td style="padding:13px 16px;font-size:13px;color:#64748b">
                  <i class="fa fa-file" style="color:#f59e0b;margin-right:8px"></i>Dokumen
                </td>
                <td style="padding:13px 16px">
                  <a :href="`/files/${pendaftaran.dokumen_berkas}`" target="_blank"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#fff;border:1.5px solid #f59e0b;color:#d97706;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600"
                    @mouseover="$event.currentTarget.style.background='#f59e0b';$event.currentTarget.style.color='#fff'" 
                    @mouseout="$event.currentTarget.style.background='#fff';$event.currentTarget.style.color='#d97706'">
                    <i class="fa fa-file-download"></i> Lihat Dokumen
                  </a>
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

        <!-- FORM PENILAIAN -->
        <div v-if="pendaftaran.status === 'diverifikasi'" class="card" style="border-left:4px solid #f59e0b">
          <div class="card-header" style="background:linear-gradient(135deg,rgba(245,158,11,.05),transparent)">
            <h3><i class="fa fa-clipboard-check" style="color:#d97706;margin-right:8px"></i>Berikan Penilaian</h3>
          </div>
          <div class="card-body">
            <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px">
              <ul style="margin:0;padding-left:16px">
                <li v-for="(err, idx) in Object.values(form.errors).flat()" :key="idx">{{ err }}</li>
              </ul>
            </div>
            
            <form @submit.prevent="submitNilai">
              <!-- NILAI -->
              <div style="margin-bottom:18px">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px">
                  Nilai (0 - 100) <span style="color:#ef4444">*</span>
                </label>
                <input type="number" v-model="form.nilai" min="0" max="100" required
                  placeholder="Masukkan nilai 0-100"
                  style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;background:#f8fafc;outline:none"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b';$event.currentTarget.style.background='#fff'"
                  @blur="$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#f8fafc'">
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:10px">
                  <div style="padding:8px 10px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;text-align:center">
                    <div style="font-size:11px;font-weight:700;color:#15803d">EXCELLENT</div>
                    <div style="font-size:10px;color:#64748b">86 – 100</div>
                  </div>
                  <div style="padding:8px 10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;text-align:center">
                    <div style="font-size:11px;font-weight:700;color:#1d4ed8">GOOD</div>
                    <div style="font-size:10px;color:#64748b">70 – 85</div>
                  </div>
                  <div style="padding:8px 10px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;text-align:center">
                    <div style="font-size:11px;font-weight:700;color:#92400e">AVERAGE</div>
                    <div style="font-size:10px;color:#64748b">55 – 69</div>
                  </div>
                  <div style="padding:8px 10px;background:#fff1f2;border:1px solid #fda4af;border-radius:8px;text-align:center">
                    <div style="font-size:11px;font-weight:700;color:#be123c">POOR</div>
                    <div style="font-size:10px;color:#64748b">0 – 54</div>
                  </div>
                </div>
              </div>
              
              <!-- STATUS -->
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:16px;border-radius:12px;border:2px solid #e2e8f0;background:#fff;transition:all .2s"
                  :style="form.status === 'lulus' ? 'border-color:#16a34a;background:#f0fdf4' : ''"
                  @mouseover="$event.currentTarget.style.borderColor='#16a34a';$event.currentTarget.style.background='#f0fdf4'"
                  @mouseout="if(form.status !== 'lulus'){$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#fff'}">
                  <input type="radio" v-model="form.status" value="lulus" required style="width:18px;height:18px;accent-color:#16a34a">
                  <div>
                    <div style="font-size:15px;font-weight:700;color:#15803d"><i class="fa fa-trophy" style="margin-right:6px"></i>Lulus</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px">Peserta dinyatakan lulus ujian</div>
                  </div>
                </label>
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:16px;border-radius:12px;border:2px solid #e2e8f0;background:#fff;transition:all .2s"
                  :style="form.status === 'tidak_lulus' ? 'border-color:#ef4444;background:#fff1f2' : ''"
                  @mouseover="$event.currentTarget.style.borderColor='#ef4444';$event.currentTarget.style.background='#fff1f2'"
                  @mouseout="if(form.status !== 'tidak_lulus'){$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#fff'}">
                  <input type="radio" v-model="form.status" value="tidak_lulus" required style="width:18px;height:18px;accent-color:#ef4444">
                  <div>
                    <div style="font-size:15px;font-weight:700;color:#be123c"><i class="fa fa-times-circle" style="margin-right:6px"></i>Tidak Lulus</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px">Peserta belum memenuhi syarat</div>
                  </div>
                </label>
              </div>
              
              <!-- CATATAN -->
              <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                  Catatan <span style="font-size:11px;font-weight:400;color:#94a3b8">(opsional)</span>
                </label>
                <textarea v-model="form.hasil_validasi" rows="3"
                  placeholder="Catatan tambahan untuk peserta (tidak ditampilkan di sertifikat)..."
                  style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;resize:vertical;line-height:1.6"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b';$event.currentTarget.style.background='#fff';$event.currentTarget.style.boxShadow='0 0 0 3px rgba(245,158,11,.1)'"
                  @blur="$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#f8fafc';$event.currentTarget.style.boxShadow='none'"></textarea>
              </div>
              
              <button type="submit" :disabled="form.processing"
                style="width:100%;padding:13px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(245,158,11,.35)"
                :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-save"></i> 
                {{ form.processing ? 'Menyimpan...' : 'Simpan Penilaian' }}
              </button>
            </form>
          </div>
        </div>
        
        <div v-else-if="['lulus','tidak_lulus'].includes(pendaftaran.status)" class="card" :style="`border-left:4px solid ${pendaftaran.status === 'lulus' ? '#16a34a' : '#ef4444'}`">
          <div class="card-body" style="text-align:center;padding:24px">
            <div style="font-size:40px;margin-bottom:8px">{{ pendaftaran.status === 'lulus' ? '🏆' : '❌' }}</div>
            <p :style="`font-size:16px;font-weight:700;color:${pendaftaran.status === 'lulus' ? '#15803d' : '#be123c'}`">
              Penilaian: {{ pendaftaran.status === 'lulus' ? 'LULUS' : 'TIDAK LULUS' }}
            </p>
            <div v-if="pendaftaran.nilai !== null" :style="`margin-top:14px;display:inline-flex;align-items:center;gap:12px;padding:12px 20px;background:${getGradeInfo(pendaftaran.nilai).bg};border:1.5px solid ${getGradeInfo(pendaftaran.nilai).border};border-radius:12px`">
              <span :style="`font-size:28px;font-weight:800;color:${getGradeInfo(pendaftaran.nilai).color}`">{{ pendaftaran.nilai }}</span>
              <span :style="`font-size:16px;font-weight:700;color:${getGradeInfo(pendaftaran.nilai).color}`">{{ getGradeInfo(pendaftaran.nilai).label }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-user" style="color:#4f46e5;margin-right:8px"></i>Informasi Peserta</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
              <img v-if="pendaftaran.user?.foto_profil" :src="`/files/${pendaftaran.user.foto_profil}`" alt="foto"
                style="width:52px;height:52px;border-radius:50%;object-fit:cover;flex-shrink:0">
              <div v-else style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700;flex-shrink:0">
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

        <div class="card">
          <div class="card-body" style="padding:14px 16px">
            <p style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px">
              <i class="fa fa-clock"></i> Didaftarkan {{ formatDateTime(pendaftaran.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
