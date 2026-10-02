<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const form = useForm({
  judul_penelitian: '',
  tujuan: '',
  dokumen_berkas: null,
  data_dibutuhkan: []
})

const customDataList = ref([])

const fileInput = ref(null)
const fileName = ref('atau drag & drop proposal penelitian di sini')
const isDragging = ref(false)

const defaultData = [
  'Visi Misi',
  'Logo',
  'Denah Lab',
  'Fasilitas Lab',
  'Modul Praktikum',
  'Profil Laboratorium',
  'Struktur Organisasi Laboratorium',
  'Data Sistem Layanan yang akan dibuat',
  'Database yg Ingin Digunakan',
  'Wawancara',
  'Informasi Tambahan yang Relevan',
]

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

function addCustomData() {
  customDataList.value.push('')
}

function removeCustomData(index) {
  customDataList.value.splice(index, 1)
}

function submit() {
  const finalDataDibutuhkan = [
    ...form.data_dibutuhkan,
    ...customDataList.value.filter(item => item.trim() !== '')
  ]

  // Clone form without mutating the original reactive object for data_dibutuhkan to avoid UI glitches
  // We can just set it on the form before submitting
  form.transform((data) => ({
    ...data,
    data_dibutuhkan: finalDataDibutuhkan
  })).post('/mahasiswa/izin-penelitian/buat')
}
</script>

<template>
  <AppLayout title="Ajukan Izin Penelitian">
    <div style="margin-bottom:20px">
      <Link href="/mahasiswa/izin-penelitian" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#4f46e5'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start">
      <!-- FORM CARD -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-flask" style="color:#10b981;margin-right:8px"></i>Form Pengajuan Izin Penelitian</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px">
            <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
            <ul style="margin-top:6px;padding-left:16px">
              <li v-for="(err, idx) in Object.values(form.errors).flat()" :key="idx">{{ err }}</li>
            </ul>
          </div>

          <form @submit.prevent="submit">
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px" for="judul">
                Judul Penelitian <span style="color:#ef4444">*</span>
              </label>
              <input id="judul" v-model="form.judul_penelitian" type="text"
                placeholder="Masukkan judul penelitian Anda..."
                :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.judul_penelitian ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;transition:all .2s`"
                @focus="$event.currentTarget.style.borderColor='#4f46e5'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(79,70,229,.1)'"
                @blur="$event.currentTarget.style.borderColor=form.errors.judul_penelitian ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'">
              <p v-if="form.errors.judul_penelitian" style="color:#ef4444;font-size:12px;margin-top:5px"><i class="fa fa-exclamation-circle"></i> {{ form.errors.judul_penelitian }}</p>
            </div>

            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px" for="tujuan">
                Tujuan Penelitian <span style="color:#ef4444">*</span>
              </label>
              <textarea id="tujuan" v-model="form.tujuan" rows="5"
                placeholder="Jelaskan tujuan dan latar belakang penelitian Anda secara singkat..."
                :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.tujuan ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;resize:vertical;transition:all .2s;line-height:1.6`"
                @focus="$event.currentTarget.style.borderColor='#4f46e5'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(79,70,229,.1)'"
                @blur="$event.currentTarget.style.borderColor=form.errors.tujuan ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'"></textarea>
              <p v-if="form.errors.tujuan" style="color:#ef4444;font-size:12px;margin-top:5px"><i class="fa fa-exclamation-circle"></i> {{ form.errors.tujuan }}</p>
            </div>

            <div style="margin-bottom:24px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Upload Dokumen/Proposal <span style="color:#94a3b8;font-weight:400">(Opsional — PDF/JPG/PNG, maks. 2MB)</span>
              </label>
              <div 
                @click="fileInput.click()" 
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false" 
                @drop="handleDrop"
                :style="`border:2px dashed ${isDragging ? '#10b981' : '#e2e8f0'};border-radius:12px;padding:32px 24px;text-align:center;cursor:pointer;transition:all .2s;background:${isDragging ? '#f0fdf4' : '#fafbfc'}`"
                @mouseover="$event.currentTarget.style.borderColor='#10b981'; $event.currentTarget.style.background='#f0fdf4'"
                @mouseout="$event.currentTarget.style.borderColor=isDragging ? '#10b981' : '#e2e8f0'; $event.currentTarget.style.background=isDragging ? '#f0fdf4' : '#fafbfc'">
                <div style="font-size:40px;margin-bottom:10px">📄</div>
                <p style="font-size:14px;font-weight:600;color:#475569;margin-bottom:4px">Klik untuk pilih file</p>
                <p style="font-size:12px;color:#94a3b8" v-html="form.dokumen_berkas ? `<strong style='color:#10b981'><i class='fa fa-check-circle'></i> ${fileName}</strong>` : fileName"></p>
              </div>
              <input type="file" ref="fileInput" @change="onFileChange" accept=".pdf,.jpg,.jpeg,.png" style="display:none">
              <p v-if="form.errors.dokumen_berkas" style="color:#ef4444;font-size:12px;margin-top:6px">{{ form.errors.dokumen_berkas }}</p>
            </div>

            <!-- DATA YANG DIBUTUHKAN -->
            <div style="margin-bottom:24px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:8px">
                <i class="fa fa-list-check" style="color:#6366f1;margin-right:6px"></i>
                Data yang Dibutuhkan
                <span style="color:#94a3b8;font-weight:400">(Centang data yang diperlukan untuk penelitian)</span>
              </label>
              <div style="border:1.5px solid #e2e8f0;border-radius:12px;padding:16px;background:#fafbfc">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
                  <label v-for="item in defaultData" :key="item" 
                    :style="`display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 10px;border-radius:8px;border:1.5px solid ${form.data_dibutuhkan.includes(item) ? '#6366f1' : '#e2e8f0'};background:${form.data_dibutuhkan.includes(item) ? '#eef2ff' : '#fff'};transition:all .15s;font-size:13px;color:#374151`"
                    @mouseover="$event.currentTarget.style.borderColor='#6366f1'; $event.currentTarget.style.background='#eef2ff'"
                    @mouseout="$event.currentTarget.style.borderColor=form.data_dibutuhkan.includes(item) ? '#6366f1' : '#e2e8f0'; $event.currentTarget.style.background=form.data_dibutuhkan.includes(item) ? '#eef2ff' : '#fff'">
                    <input type="checkbox" :value="item" v-model="form.data_dibutuhkan"
                      style="width:16px;height:16px;accent-color:#6366f1;flex-shrink:0">
                    {{ item }}
                  </label>
                </div>
                
                <!-- Tambah data custom -->
                <div style="border-top:1px solid #e2e8f0;padding-top:12px">
                  <p style="font-size:12px;font-weight:600;color:#64748b;margin-bottom:8px"><i class="fa fa-plus-circle" style="color:#6366f1"></i> Tambah data lainnya:</p>
                  <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:8px">
                    <div v-for="(custom, index) in customDataList" :key="index" style="display:flex;gap:6px;align-items:center">
                      <input type="text" v-model="customDataList[index]" placeholder="Nama data yang dibutuhkan..."
                        style="flex:1;padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none"
                        @focus="$event.currentTarget.style.borderColor='#6366f1'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                      <button type="button" @click="removeCustomData(index)"
                        style="padding:7px 10px;background:#fee2e2;border:none;border-radius:8px;color:#dc2626;cursor:pointer;font-size:12px">✕</button>
                    </div>
                  </div>
                  <button type="button" @click="addCustomData"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#eef2ff;border:1.5px solid #c7d2fe;color:#4f46e5;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
                    <i class="fa fa-plus"></i> Tambah Item Lain
                  </button>
                </div>
              </div>
            </div>

            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:14px 16px;margin-bottom:24px;font-size:13px;color:#15803d">
              <strong><i class="fa fa-info-circle" style="margin-right:6px"></i>Alur Proses:</strong>
              Setelah diajukan → Admin verifikasi → Kepala Lab menyetujui → Izin diterbitkan
            </div>

            <button type="submit" :disabled="form.processing"
              style="width:100%;padding:13px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 6px 20px rgba(16,185,129,.3);transition:all .3s"
              @mouseover="if(!form.processing) { $event.currentTarget.style.transform='translateY(-1px)'; $event.currentTarget.style.boxShadow='0 10px 28px rgba(16,185,129,.4)' }"
              @mouseout="if(!form.processing) { $event.currentTarget.style.transform='translateY(0)'; $event.currentTarget.style.boxShadow='0 6px 20px rgba(16,185,129,.3)' }"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-paper-plane"></i> 
              {{ form.processing ? 'Mengirim...' : 'Kirim Permohonan' }}
            </button>
          </form>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-list-check" style="color:#10b981;margin-right:8px"></i>Alur Pengajuan</h3>
          </div>
          <div class="card-body">
            <div style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:20px;bottom:20px;width:2px;background:linear-gradient(180deg,#10b981,#4f46e5)"></div>
              
              <div v-for="(s, i) in [
                {icon:'fa-paper-plane',title:'Isi & Kirim Formulir',desc:'Lengkapi judul, tujuan, dan upload proposal (opsional)',color:'#10b981'},
                {icon:'fa-user-check',title:'Verifikasi Admin',desc:'Admin memeriksa kelengkapan dokumen permohonan',color:'#3b82f6'},
                {icon:'fa-clipboard-check',title:'Persetujuan Kepala Lab',desc:'Kepala Lab meninjau dan menyetujui izin penelitian',color:'#8b5cf6'},
                {icon:'fa-certificate',title:'Izin Diterbitkan',desc:'Izin resmi Anda siap digunakan untuk penelitian',color:'#f59e0b'}
              ]" :key="i" :style="`position:relative;margin-bottom:${i === 3 ? '0' : '20px'}`">
                <div :style="`position:absolute;left:-28px;top:0;width:22px;height:22px;border-radius:50%;background:${s.color};display:flex;align-items:center;justify-content:center`">
                  <i :class="`fa ${s.icon}`" style="color:#fff;font-size:10px"></i>
                </div>
                <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:2px">{{ s.title }}</div>
                <div style="font-size:12px;color:#64748b;line-height:1.5">{{ s.desc }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-question-circle" style="color:#f59e0b;margin-right:8px"></i>Persyaratan</h3>
          </div>
          <div class="card-body">
            <ul style="list-style:none;display:flex;flex-direction:column;gap:10px">
              <li v-for="req in ['Mahasiswa aktif STMIK Samarinda','Judul penelitian yang jelas','Tujuan penelitian yang spesifik','Proposal penelitian (disarankan)','Mendapat persetujuan dosen pembimbing']" :key="req" style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-check-circle" style="color:#10b981;margin-top:1px;flex-shrink:0"></i>
                {{ req }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
