<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  jadwal: Object
})

const form = useForm({
  dokumen_berkas: null
})

const fileInput = ref(null)
const fileName = ref('atau drag & drop di sini')
const isDragging = ref(false)

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.dokumen_berkas = file
    fileName.value = file.name
  }
}

function handleDrop(e) {
  e.preventDefault()
  isDragging.value = false
  const file = e.dataTransfer.files[0]
  if (file) {
    form.dokumen_berkas = file
    fileName.value = file.name
  }
}

function submit() {
  form.post(`/mahasiswa/sertifikasi/${props.jadwal.id}/daftar`, {
    preserveScroll: true
  })
}

function formatDateTime(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

<template>
  <AppLayout title="Daftar Sertifikasi">
    <div style="margin-bottom:20px">
      <Link href="/mahasiswa/sertifikasi" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500">
        <i class="fa fa-arrow-left"></i> Kembali ke Jadwal
      </Link>
    </div>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start">
      <!-- FORM -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-paper-plane" style="color:#4f46e5;margin-right:8px"></i>Form Pendaftaran Sertifikasi</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(form.errors).length > 0 && !form.errors.dokumen_berkas" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px">
            <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
            <ul style="margin-top:6px;padding-left:16px">
              <li v-for="(err, idx) in Object.values(form.errors).flat()" :key="idx">{{ err }}</li>
            </ul>
          </div>

          <form @submit.prevent="submit">
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Jadwal Dipilih
              </label>
              <div style="padding:14px;background:#f8fafc;border-radius:10px;border:1.5px solid #e2e8f0">
                <div style="font-size:15px;font-weight:700;color:#0f172a">{{ jadwal.skema.nama_skema }}</div>
                <div style="font-size:13px;color:#64748b;margin-top:4px">
                  <i class="fa fa-calendar" style="color:#4f46e5;margin-right:4px"></i>
                  {{ formatDateTime(jadwal.tanggal) }}
                </div>
              </div>
            </div>

            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Upload Dokumen Transkip Nilai <span style="color:#ef4444">*</span> <span style="color:#94a3b8;font-weight:400">(Wajib — PDF/JPG/PNG, maks. 2MB)</span>
              </label>
              <div 
                @click="fileInput.click()" 
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false" 
                @drop="handleDrop"
                :style="`border:2px dashed ${isDragging ? '#4f46e5' : '#e2e8f0'};border-radius:12px;padding:28px;text-align:center;cursor:pointer;transition:all .2s;background:${isDragging ? '#faf5ff' : '#fafbfc'}`"
              >
                <div style="font-size:36px;margin-bottom:8px">📎</div>
                <p style="font-size:14px;font-weight:600;color:#475569">Klik untuk pilih file</p>
                <p style="font-size:12px;color:#94a3b8;margin-top:4px">{{ fileName }}</p>
              </div>
              <input type="file" ref="fileInput" @change="onFileChange" accept=".pdf,.jpg,.jpeg,.png" style="display:none">
              <p v-if="form.errors.dokumen_berkas" style="color:#ef4444;font-size:12px;margin-top:6px">{{ form.errors.dokumen_berkas }}</p>
            </div>

            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:24px;font-size:13px;color:#92400e">
              <strong><i class="fa fa-info-circle" style="color:#d97706;margin-right:6px"></i>Perhatian:</strong>
              Setelah mendaftar, permohonan Anda akan diverifikasi oleh Admin. Token ujian akan diberikan setelah verifikasi selesai.
            </div>

            <button type="submit" :disabled="form.processing" style="width:100%;padding:13px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 6px 20px rgba(79,70,229,.35);transition:all .3s" :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-paper-plane"></i> 
              {{ form.processing ? 'Mengirim...' : 'Kirim Pendaftaran' }}
            </button>
          </form>
        </div>
      </div>

      <!-- SIDEBAR INFO -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-info-circle" style="color:#4f46e5;margin-right:8px"></i>Detail Jadwal</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:14px">
              <div>
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Skema</div>
                <div style="font-size:14px;font-weight:700;color:#0f172a">{{ jadwal.skema.nama_skema }}</div>
              </div>
              <div>
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Tanggal Ujian</div>
                <div style="font-size:14px;font-weight:600;color:#0f172a"><i class="fa fa-calendar" style="color:#4f46e5;margin-right:6px"></i>{{ formatDate(jadwal.tanggal) }}</div>
              </div>
              <div>
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Kuota Tersisa</div>
                <div style="font-size:14px;font-weight:600;color:#16a34a"><i class="fa fa-users" style="margin-right:6px"></i>{{ jadwal.sisa_kuota }} dari {{ jadwal.kuota }} tempat</div>
              </div>
              <hr style="border:none;border-top:1px solid #f1f5f9">
              <div>
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Deskripsi Skema</div>
                <p style="font-size:13px;color:#64748b;line-height:1.7">{{ jadwal.skema.deskripsi }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-list-check" style="color:#10b981;margin-right:8px"></i>Alur Pendaftaran</h3>
          </div>
          <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px">
              <div v-for="(icon, step) in {'Isi form & upload berkas': 'fa-paper-plane', 'Verifikasi oleh Admin': 'fa-user-check', 'Terima token ujian': 'fa-key', 'Ikuti ujian sertifikasi': 'fa-graduation-cap'}" :key="step" style="display:flex;align-items:center;gap:10px">
                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,rgba(79,70,229,.1),rgba(124,58,237,.1));display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <i :class="`fa ${icon}`" style="color:#4f46e5;font-size:13px"></i>
                </div>
                <span style="font-size:13px;color:#475569;font-weight:500">{{ step }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
