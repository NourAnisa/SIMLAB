<script setup>
import { ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
  ruangans: Array
})

const form = useForm({
  ruangan: '',
  tanggal_pinjam: '',
  jam_mulai: '',
  jam_selesai: '',
  keperluan: '',
  surat_keterangan: null
})

const fileInput = ref(null)
const fileName = ref('atau drag & drop surat keterangan di sini')
const isDragging = ref(false)

const minDate = new Date().toISOString().split('T')[0]

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.surat_keterangan = file
    fileName.value = file.name
  }
}

function handleDrop(e) {
  e.preventDefault()
  isDragging.value = false
  const file = e.dataTransfer.files[0]
  if (file) {
    form.surat_keterangan = file
    fileName.value = file.name
  }
}

const jadwalTerpakai = ref([])
const loadingJadwal = ref(false)

watch([() => form.ruangan, () => form.tanggal_pinjam], async ([ruangan, tanggal]) => {
  if (ruangan && tanggal) {
    loadingJadwal.value = true
    try {
      const response = await fetch(`/api/peminjaman-lab/cek-jadwal?ruangan=${encodeURIComponent(ruangan)}&tanggal=${tanggal}`)
      jadwalTerpakai.value = await response.json()
    } catch (error) {
      console.error('Error fetching jadwal:', error)
      jadwalTerpakai.value = []
    } finally {
      loadingJadwal.value = false
    }
  } else {
    jadwalTerpakai.value = []
  }
})

function submit() {
  form.post('/mahasiswa/peminjaman-lab/buat')
}
</script>

<template>
  <AppLayout title="Ajukan Peminjaman Lab">
    <div style="margin-bottom:20px">
      <Link href="/mahasiswa/peminjaman-lab" 
        style="display:inline-flex;align-items:center;gap:6px;color:#64748b;text-decoration:none;font-size:14px;font-weight:500;transition:color .2s" 
        @mouseover="$event.currentTarget.style.color='#f59e0b'" 
        @mouseout="$event.currentTarget.style.color='#64748b'">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </Link>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <!-- FORM CARD -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Form Peminjaman Ruangan Lab</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px">
            <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
            <ul style="margin-top:6px;padding-left:16px">
              <li v-for="(err, idx) in Object.values(form.errors).flat()" :key="idx">{{ err }}</li>
            </ul>
          </div>

          <form @submit.prevent="submit">
            <!-- Ruangan -->
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Ruangan <span style="color:#ef4444">*</span>
              </label>
              <select v-model="form.ruangan"
                :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.ruangan ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;transition:all .2s;appearance:none`"
                @focus="$event.currentTarget.style.borderColor='#f59e0b'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(245,158,11,.1)'"
                @blur="$event.currentTarget.style.borderColor=form.errors.ruangan ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'">
                <option value="">-- Pilih Ruangan --</option>
                <option v-for="r in ruangans" :key="r.id" :value="r.nama_ruangan">{{ r.nama_ruangan }}</option>
              </select>
              <p v-if="form.errors.ruangan" style="color:#ef4444;font-size:12px;margin-top:5px"><i class="fa fa-exclamation-circle"></i> {{ form.errors.ruangan }}</p>
            </div>

            <!-- Tanggal -->
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Tanggal Pinjam <span style="color:#ef4444">*</span>
              </label>
              <input type="date" v-model="form.tanggal_pinjam" :min="minDate"
                :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.tanggal_pinjam ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;transition:all .2s`"
                @focus="$event.currentTarget.style.borderColor='#f59e0b'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(245,158,11,.1)'"
                @blur="$event.currentTarget.style.borderColor=form.errors.tanggal_pinjam ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'">
              <p v-if="form.errors.tanggal_pinjam" style="color:#ef4444;font-size:12px;margin-top:5px"><i class="fa fa-exclamation-circle"></i> {{ form.errors.tanggal_pinjam }}</p>
            </div>

            <!-- Jam Mulai & Selesai -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                  Jam Mulai <span style="color:#ef4444">*</span>
                </label>
                <input type="time" v-model="form.jam_mulai"
                  :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.jam_mulai ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;transition:all .2s`"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(245,158,11,.1)'"
                  @blur="$event.currentTarget.style.borderColor=form.errors.jam_mulai ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'">
                <p v-if="form.errors.jam_mulai" style="color:#ef4444;font-size:12px;margin-top:5px">{{ form.errors.jam_mulai }}</p>
              </div>
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                  Jam Selesai <span style="color:#ef4444">*</span>
                </label>
                <input type="time" v-model="form.jam_selesai"
                  :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.jam_selesai ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;transition:all .2s`"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(245,158,11,.1)'"
                  @blur="$event.currentTarget.style.borderColor=form.errors.jam_selesai ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'">
                <p v-if="form.errors.jam_selesai" style="color:#ef4444;font-size:12px;margin-top:5px">{{ form.errors.jam_selesai }}</p>
              </div>
            </div>

            <!-- Keperluan -->
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Keperluan / Tujuan <span style="color:#ef4444">*</span>
              </label>
              <textarea v-model="form.keperluan" rows="4"
                placeholder="Jelaskan keperluan peminjaman ruangan lab..."
                :style="`width:100%;padding:11px 14px;border:1.5px solid ${form.errors.keperluan ? '#ef4444' : '#e2e8f0'};border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;background:#f8fafc;outline:none;resize:vertical;line-height:1.6;transition:all .2s`"
                @focus="$event.currentTarget.style.borderColor='#f59e0b'; $event.currentTarget.style.background='#fff'; $event.currentTarget.style.boxShadow='0 0 0 3px rgba(245,158,11,.1)'"
                @blur="$event.currentTarget.style.borderColor=form.errors.keperluan ? '#ef4444' : '#e2e8f0'; $event.currentTarget.style.background='#f8fafc'; $event.currentTarget.style.boxShadow='none'"></textarea>
              <p v-if="form.errors.keperluan" style="color:#ef4444;font-size:12px;margin-top:5px"><i class="fa fa-exclamation-circle"></i> {{ form.errors.keperluan }}</p>
            </div>

            <!-- Surat Keterangan -->
            <div style="margin-bottom:24px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                Surat Keterangan <span style="color:#94a3b8;font-weight:400">(Opsional — PDF/JPG/PNG, maks. 2MB)</span>
              </label>
              <div 
                @click="fileInput.click()" 
                @dragover.prevent="isDragging = true" 
                @dragleave.prevent="isDragging = false" 
                @drop="handleDrop"
                :style="`border:2px dashed ${isDragging ? '#f59e0b' : '#e2e8f0'};border-radius:12px;padding:28px 24px;text-align:center;cursor:pointer;transition:all .2s;background:${isDragging ? '#fffbeb' : '#fafbfc'}`"
                @mouseover="$event.currentTarget.style.borderColor='#f59e0b'; $event.currentTarget.style.background='#fffbeb'"
                @mouseout="$event.currentTarget.style.borderColor=isDragging ? '#f59e0b' : '#e2e8f0'; $event.currentTarget.style.background=isDragging ? '#fffbeb' : '#fafbfc'">
                <div style="font-size:36px;margin-bottom:8px">📋</div>
                <p style="font-size:14px;font-weight:600;color:#475569;margin-bottom:4px">Klik untuk pilih file</p>
                <p style="font-size:12px;color:#94a3b8" v-html="form.surat_keterangan ? `<strong style='color:#f59e0b'><i class='fa fa-check-circle'></i> ${fileName}</strong>` : fileName"></p>
              </div>
              <input type="file" ref="fileInput" @change="onFileChange" accept=".pdf,.jpg,.jpeg,.png" style="display:none">
              <p v-if="form.errors.surat_keterangan" style="color:#ef4444;font-size:12px;margin-top:6px">{{ form.errors.surat_keterangan }}</p>
            </div>

            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:24px;font-size:13px;color:#92400e">
              <strong><i class="fa fa-info-circle" style="margin-right:6px"></i>Alur Proses:</strong>
              Setelah diajukan → Admin verifikasi → Kepala Lab menyetujui → Peminjaman dikonfirmasi
            </div>

            <button type="submit" :disabled="form.processing"
              style="width:100%;padding:13px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:15px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 6px 20px rgba(245,158,11,.3);transition:all .3s"
              @mouseover="!form.processing ? ($event.currentTarget.style.transform='translateY(-1px)', $event.currentTarget.style.boxShadow='0 10px 28px rgba(245,158,11,.4)') : null"
              @mouseout="!form.processing ? ($event.currentTarget.style.transform='translateY(0)', $event.currentTarget.style.boxShadow='0 6px 20px rgba(245,158,11,.3)') : null"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-paper-plane"></i> 
              {{ form.processing ? 'Mengirim...' : 'Kirim Permohonan' }}
            </button>
          </form>
        </div>
      </div>

      <!-- SIDEBAR INFO -->
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <h3><i class="fa fa-list-check" style="color:#f59e0b;margin-right:8px"></i>Alur Pengajuan</h3>
          </div>
          <div class="card-body">
            <div style="position:relative;padding-left:28px">
              <div style="position:absolute;left:11px;top:20px;bottom:20px;width:2px;background:linear-gradient(180deg,#f59e0b,#4f46e5)"></div>
              
              <div v-for="(s, i) in [
                {icon:'fa-paper-plane',title:'Isi & Kirim Formulir',desc:'Pilih ruangan, tanggal, waktu dan keperluan',color:'#f59e0b'},
                {icon:'fa-user-check',title:'Verifikasi Admin',desc:'Admin memeriksa kelengkapan dan kesesuaian jadwal',color:'#3b82f6'},
                {icon:'fa-clipboard-check',title:'Persetujuan Kepala Lab',desc:'Kepala Lab meninjau dan menyetujui peminjaman',color:'#8b5cf6'},
                {icon:'fa-key',title:'Peminjaman Dikonfirmasi',desc:'Ruangan siap digunakan sesuai jadwal yang disetujui',color:'#16a34a'}
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
            <h3><i class="fa fa-building" style="color:#f59e0b;margin-right:8px"></i>Ruangan Tersedia</h3>
          </div>
          <div class="card-body">
            <ul style="list-style:none;display:flex;flex-direction:column;gap:8px">
              <li v-for="r in ruangans" :key="r.id" style="display:flex;align-items:center;gap:8px;font-size:13px;color:#475569">
                <i class="fa fa-door-open" style="color:#f59e0b;flex-shrink:0"></i> {{ r.nama_ruangan }}
              </li>
            </ul>
            <p style="font-size:12px;color:#94a3b8;margin-top:12px"><i class="fa fa-info-circle"></i> Cek jadwal sebelum mengajukan untuk menghindari konflik waktu.</p>
          </div>
        </div>

        <!-- Jadwal Terpakai Sidebar -->
        <div v-if="form.ruangan && form.tanggal_pinjam" class="card" style="margin-top:16px">
          <div class="card-header" style="background:#fff1f2;border-bottom:1px solid #ffe4e6;border-radius:12px 12px 0 0">
            <h3 style="color:#be123c"><i class="fa fa-calendar-times" style="margin-right:8px"></i>Jadwal Terpakai</h3>
          </div>
          <div class="card-body">
            <div v-if="loadingJadwal" style="text-align:center;padding:16px 0;color:#94a3b8;font-size:13px">
              <i class="fa fa-spinner fa-spin" style="margin-right:6px"></i> Memeriksa jadwal...
            </div>
            <div v-else-if="jadwalTerpakai.length === 0" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px;font-size:13px;color:#15803d">
              <i class="fa fa-check-circle" style="margin-right:6px"></i> Belum ada peminjaman di tanggal ini. Ruangan kosong!
            </div>
            <div v-else>
              <p style="font-size:12px;color:#64748b;margin-bottom:12px;line-height:1.4">Berikut adalah daftar peminjaman pada <strong>{{ form.tanggal_pinjam }}</strong> di ruangan <strong>{{ form.ruangan }}</strong>:</p>
              <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;padding:0;margin:0">
                <li v-for="j in jadwalTerpakai" :key="j.id" style="background:#fff;border:1px solid #e2e8f0;border-left:3px solid #ef4444;border-radius:6px;padding:12px;box-shadow:0 1px 3px rgba(0,0,0,0.05)">
                  <div style="font-weight:700;color:#0f172a;margin-bottom:4px;font-size:13px">
                    <i class="fa fa-clock" style="color:#ef4444;margin-right:4px"></i> {{ j.jam_mulai.substring(0,5) }} – {{ j.jam_selesai.substring(0,5) }}
                  </div>
                  <div style="color:#475569;font-size:12px;display:flex;align-items:center;gap:6px;margin-bottom:4px">
                    <i class="fa fa-user" style="color:#94a3b8;width:12px;text-align:center"></i> {{ j.user?.name || 'Pengguna' }}
                  </div>
                  <div style="color:#64748b;font-size:11px;margin-bottom:6px;display:flex;align-items:flex-start;gap:6px">
                    <i class="fa fa-align-left" style="color:#94a3b8;width:12px;text-align:center;margin-top:2px"></i>
                    <span style="flex:1;line-height:1.4">"{{ j.keperluan }}"</span>
                  </div>
                  <div>
                    <span :style="`font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;background:${j.status === 'disetujui' ? '#dcfce7' : (j.status === 'diverifikasi' ? '#dbeafe' : '#fef3c7')};color:${j.status === 'disetujui' ? '#166534' : (j.status === 'diverifikasi' ? '#1e40af' : '#92400e')};border:1px solid ${j.status === 'disetujui' ? '#bbf7d0' : (j.status === 'diverifikasi' ? '#bfdbfe' : '#fde68a')}`">
                      {{ j.status.toUpperCase() }}
                    </span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
