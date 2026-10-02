<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  setting: Object,
  mail_username: String
})

const existingGallery = ref(props.setting?.gallery_images || [])
const logoPreview = ref('')
const currentLogo = ref(props.setting?.logo || '')
const newGalleryPreviews = ref([])

const form = useForm({
  _method: 'PUT',
  nama_website: props.setting?.nama_website || '',
  nama_kampus: props.setting?.nama_kampus || '',
  deskripsi: props.setting?.deskripsi || '',
  alamat: props.setting?.alamat || '',
  telepon: props.setting?.telepon || '',
  email_kontak: props.setting?.email_kontak || '',
  jam_operasional: props.setting?.jam_operasional || '',
  facebook: props.setting?.facebook || '',
  instagram: props.setting?.instagram || '',
  twitter: props.setting?.twitter || '',
  youtube: props.setting?.youtube || '',
  daftar_ruangan: props.setting?.daftar_ruangan || '',
  tema_warna: props.setting?.tema_warna || 'ungu',
  domain_email_mahasiswa: props.setting?.domain_email_mahasiswa || '',
  domain_email_pegawai: props.setting?.domain_email_pegawai || '',
  logo: null,
  gallery: [],
  delete_gallery: [],
  mail_username: props.mail_username || '',
  mail_password: ''
})

function handleLogoChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.logo = file
    const reader = new FileReader()
    reader.onload = (evt) => {
      logoPreview.value = evt.target.result
    }
    reader.readAsDataURL(file)
  }
}

function handleGalleryChange(e) {
  const files = e.target.files
  form.gallery = Array.from(files)
  
  newGalleryPreviews.value = []
  Array.from(files).forEach(file => {
    const reader = new FileReader()
    reader.onload = (evt) => {
      newGalleryPreviews.value.push(evt.target.result)
    }
    reader.readAsDataURL(file)
  })
}

function toggleDeleteGallery(img) {
  const idx = form.delete_gallery.indexOf(img)
  if (idx === -1) {
    form.delete_gallery.push(img)
  } else {
    form.delete_gallery.splice(idx, 1)
  }
}

function submitForm() {
  form.post('/admin/site-setting', {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      form.logo = null
      form.gallery = []
      form.delete_gallery = []
      form.mail_password = ''
      logoPreview.value = ''
      newGalleryPreviews.value = []
      existingGallery.value = props.setting?.gallery_images || []
      currentLogo.value = props.setting?.logo || ''
      window.location.reload()
    }
  })
}
</script>

<template>
  <AppLayout title="Profil Website">
    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start">
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-globe" style="color:#4f46e5;margin-right:8px"></i>Pengaturan Profil Website</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px">
            <div v-for="(errs, field) in form.errors" :key="field">
              <div>• {{ errs }}</div>
            </div>
          </div>

          <form @submit.prevent="submitForm">
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                <i class="fa fa-tag" style="color:#4f46e5;margin-right:4px"></i> Nama Website / Sistem
              </label>
              <input v-model="form.nama_website" type="text" placeholder="cth: SILAB" required
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                @focus="$event.currentTarget.style.borderColor='#4f46e5';$event.currentTarget.style.background='#fff'"
                @blur="$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#f8fafc'">
              <p style="font-size:11px;color:#94a3b8;margin-top:4px">Nama ini tampil di header, footer, dan sertifikat.</p>
            </div>

            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                <i class="fa fa-university" style="color:#4f46e5;margin-right:4px"></i> Nama Kampus / Instansi
              </label>
              <input v-model="form.nama_kampus" type="text" placeholder="cth: STMIK Samarinda" required
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                @focus="$event.currentTarget.style.borderColor='#4f46e5';$event.currentTarget.style.background='#fff'"
                @blur="$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#f8fafc'">
              <p style="font-size:11px;color:#94a3b8;margin-top:4px">Tampil di sertifikat, PDF, dan halaman publik.</p>
            </div>

            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                <i class="fa fa-palette" style="color:#4f46e5;margin-right:4px"></i> Tema Warna Aplikasi
              </label>
              <div style="display:flex;gap:16px">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#374151">
                  <input type="radio" v-model="form.tema_warna" value="ungu" style="accent-color:#4f46e5;width:16px;height:16px"> 
                  <span style="display:inline-block;width:12px;height:12px;background:#4f46e5;border-radius:50%;margin-left:4px"></span>
                  Ungu (Default)
                </label>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#374151">
                  <input type="radio" v-model="form.tema_warna" value="merah" style="accent-color:#e11d48;width:16px;height:16px"> 
                  <span style="display:inline-block;width:12px;height:12px;background:#e11d48;border-radius:50%;margin-left:4px"></span>
                  Merah
                </label>
              </div>
              <p style="font-size:11px;color:#94a3b8;margin-top:8px">Warna ini akan mengubah tampilan warna dominan aplikasi.</p>
            </div>

            <div style="margin-bottom:28px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                <i class="fa fa-image" style="color:#4f46e5;margin-right:4px"></i> Logo Website
              </label>

              <div v-if="currentLogo" style="margin-bottom:12px;display:flex;align-items:center;gap:12px;padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px">
                <img :src="`/storage-public/${currentLogo}`" alt="Logo" style="height:50px;object-fit:contain">
                <div>
                  <div style="font-size:12px;font-weight:600;color:#374151">Logo aktif</div>
                  <div style="font-size:11px;color:#94a3b8">Upload baru untuk mengganti</div>
                </div>
              </div>

              <label style="position:relative;border:2px dashed #c7d2fe;border-radius:10px;padding:24px;text-align:center;background:#f8fafc;cursor:pointer;display:block"
                @dragover.prevent="$event.currentTarget.style.borderColor='#4f46e5'"
                @dragleave="$event.currentTarget.style.borderColor='#c7d2fe'">
                <i class="fa fa-cloud-upload-alt" style="font-size:28px;color:#a5b4fc;margin-bottom:8px;display:block"></i>
                <div style="font-size:13px;font-weight:600;color:#4f46e5">Klik untuk upload logo</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px">PNG, JPG, SVG — maks. 2MB</div>
                <input type="file" accept="image/*" style="display:none" @change="handleLogoChange">
              </label>
              
              <div v-if="logoPreview" style="margin-top:12px;text-align:center">
                <img :src="logoPreview" style="max-height:80px;object-fit:contain;border:1px solid #e2e8f0;border-radius:8px;padding:8px">
              </div>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:4px"><i class="fa fa-images" style="color:#4f46e5;margin-right:6px"></i>Galeri Foto (Slideshow Halaman Publik)</div>
              <p style="font-size:11px;color:#94a3b8;margin-bottom:14px">Foto-foto ini akan tampil sebagai slideshow di section "Tentang" landing page. Maks. 3MB per foto.</p>

              <div v-if="existingGallery.length > 0" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;margin-bottom:14px">
                <div v-for="(img, idx) in existingGallery" :key="idx" 
                  :style="`position:relative;border-radius:10px;overflow:hidden;aspect-ratio:4/3;background:#f1f5f9;transition:opacity 0.2s;opacity:${form.delete_gallery.includes(img) ? '0.4' : '1'}`">
                  <img :src="`/storage-public/${img}`" style="width:100%;height:100%;object-fit:cover">
                  <label style="position:absolute;top:5px;right:5px;background:rgba(220,38,38,.85);border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;cursor:pointer" title="Hapus">
                    <input type="checkbox" :checked="form.delete_gallery.includes(img)" @change="toggleDeleteGallery(img)" style="display:none">
                    <i class="fa fa-times" style="color:#fff;font-size:10px"></i>
                  </label>
                </div>
              </div>
              <p v-if="existingGallery.length > 0" style="font-size:11px;color:#94a3b8;margin-bottom:12px">Centang tanda × pada foto untuk menghapus foto tersebut saat menyimpan.</p>

              <label style="border:2px dashed #c7d2fe;border-radius:10px;padding:20px;text-align:center;background:#f8fafc;cursor:pointer;display:block"
                @dragover.prevent="$event.currentTarget.style.borderColor='#4f46e5'"
                @dragleave="$event.currentTarget.style.borderColor='#c7d2fe'">
                <i class="fa fa-images" style="font-size:24px;color:#a5b4fc;margin-bottom:8px;display:block"></i>
                <div style="font-size:13px;font-weight:600;color:#4f46e5">Klik untuk upload foto galeri</div>
                <div style="font-size:11px;color:#94a3b8;margin-top:4px">PNG, JPG, WEBP — bisa pilih beberapa sekaligus</div>
                <input type="file" accept="image/*" multiple style="display:none" @change="handleGalleryChange">
              </label>
              
              <div v-if="newGalleryPreviews.length > 0" style="margin-top:12px;display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px">
                <div v-for="(preview, idx) in newGalleryPreviews" :key="'p'+idx" style="border-radius:8px;overflow:hidden;aspect-ratio:4/3;background:#f1f5f9">
                  <img :src="preview" style="width:100%;height:100%;object-fit:cover">
                </div>
              </div>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:16px"><i class="fa fa-align-left" style="color:#4f46e5;margin-right:6px"></i>Deskripsi Singkat</div>
              <textarea v-model="form.deskripsi" rows="3" placeholder="cth: Sistem informasi layanan laboratorium untuk civitas akademika..."
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box;resize:vertical"
                @focus="$event.currentTarget.style.borderColor='#4f46e5';$event.currentTarget.style.background='#fff'"
                @blur="$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#f8fafc'"></textarea>
              <p style="font-size:11px;color:#94a3b8;margin-top:4px">Tampil di footer landing page.</p>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:16px"><i class="fa fa-door-open" style="color:#4f46e5;margin-right:6px"></i>Daftar Ruangan Lab</div>
              <textarea v-model="form.daftar_ruangan" rows="3" placeholder="cth: Lab Komputer 1, Lab Komputer 2, Lab Multimedia"
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box;resize:vertical"
                @focus="$event.currentTarget.style.borderColor='#4f46e5';$event.currentTarget.style.background='#fff'"
                @blur="$event.currentTarget.style.borderColor='#e2e8f0';$event.currentTarget.style.background='#f8fafc'"></textarea>
              <p style="font-size:11px;color:#94a3b8;margin-top:4px">Pisahkan dengan koma. Daftar ruangan ini akan muncul di form Peminjaman Lab (untuk Dosen & Mahasiswa).</p>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:16px"><i class="fa fa-shield-alt" style="color:#4f46e5;margin-right:6px"></i>Pembatasan Domain Email (Registrasi)</div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-user-graduate" style="color:#64748b;margin-right:4px"></i> Domain Email Mahasiswa</label>
                  <input v-model="form.domain_email_mahasiswa" type="text" placeholder="cth: @mhs.stmik.ac.id"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                  <p style="font-size:11px;color:#94a3b8;margin-top:4px">Biarkan kosong jika tidak dibatasi.</p>
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-user-tie" style="color:#64748b;margin-right:4px"></i> Domain Email Pegawai (Dosen/Admin)</label>
                  <input v-model="form.domain_email_pegawai" type="text" placeholder="cth: @stmik-smd.ac.id"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                  <p style="font-size:11px;color:#94a3b8;margin-top:4px">Biarkan kosong jika tidak dibatasi.</p>
                </div>
              </div>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:16px"><i class="fa fa-address-card" style="color:#4f46e5;margin-right:6px"></i>Informasi Kontak</div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-map-marker-alt" style="color:#64748b;margin-right:4px"></i> Alamat</label>
                  <input v-model="form.alamat" type="text" placeholder="Jl. ... Samarinda"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-phone" style="color:#64748b;margin-right:4px"></i> Telepon</label>
                  <input v-model="form.telepon" type="text" placeholder="(0541) xxx-xxxx"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-envelope" style="color:#64748b;margin-right:4px"></i> Email Kontak</label>
                  <input v-model="form.email_kontak" type="email" placeholder="lab@stmik.ac.id"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-clock" style="color:#64748b;margin-right:4px"></i> Jam Operasional</label>
                  <input v-model="form.jam_operasional" type="text" placeholder="Senin–Jumat, 08.00–16.00"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
              </div>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:16px"><i class="fab fa-facebook" style="color:#4f46e5;margin-right:6px"></i>Media Sosial</div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fab fa-facebook-f" style="color:#1877f2;margin-right:4px"></i> Facebook URL</label>
                  <input v-model="form.facebook" type="url" placeholder="https://facebook.com/..."
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fab fa-instagram" style="color:#e1306c;margin-right:4px"></i> Instagram URL</label>
                  <input v-model="form.instagram" type="url" placeholder="https://instagram.com/..."
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fab fa-twitter" style="color:#1da1f2;margin-right:4px"></i> Twitter/X URL</label>
                  <input v-model="form.twitter" type="url" placeholder="https://twitter.com/..."
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fab fa-youtube" style="color:#ff0000;margin-right:4px"></i> YouTube URL</label>
                  <input v-model="form.youtube" type="url" placeholder="https://youtube.com/..."
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                </div>
              </div>
            </div>

            <div style="border-top:2px solid #f1f5f9;margin:24px 0 20px;padding-top:20px">
              <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:16px"><i class="fa fa-envelope-open-text" style="color:#4f46e5;margin-right:6px"></i>Pengaturan Email (SMTP / Mailtrap)</div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-user" style="color:#64748b;margin-right:4px"></i> Username SMTP / Mailtrap</label>
                  <input v-model="form.mail_username" type="text" placeholder="username mailtrap / email asli"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                  <p style="font-size:11px;color:#94a3b8;margin-top:4px">Username (Tersimpan di .env).</p>
                </div>
                <div>
                  <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px"><i class="fa fa-key" style="color:#64748b;margin-right:4px"></i> Password SMTP / Mailtrap</label>
                  <input v-model="form.mail_password" type="password" placeholder="Kosongkan jika tidak ingin mengubah"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box"
                    @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                  <p style="font-size:11px;color:#94a3b8;margin-top:4px">Password (Tersimpan di .env).</p>
                </div>
              </div>
            </div>

            <button type="submit" :disabled="form.processing"
              style="padding:12px 28px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:14px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}
            </button>
          </form>
        </div>
      </div>

      <!-- PREVIEW -->
      <div>
        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-eye" style="color:#10b981;margin-right:8px"></i>Preview</h3>
          </div>
          <div class="card-body" style="text-align:center;padding:28px 20px">
            <img v-if="logoPreview || currentLogo" :src="logoPreview || `/storage-public/${currentLogo}`" alt="Logo" style="max-height:70px;object-fit:contain;margin-bottom:14px;display:block;margin-left:auto;margin-right:auto">
            <div v-else style="width:70px;height:70px;border-radius:16px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
              <i class="fa fa-flask" style="color:#fff;font-size:28px"></i>
            </div>
            <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:4px">{{ form.nama_website || 'SILAB' }}</div>
            <div style="font-size:12px;color:#64748b">{{ form.nama_kampus || 'STMIK Samarinda' }}</div>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9">
              <div style="font-size:11px;color:#94a3b8">Tampil di header, footer, dan sertifikat</div>
            </div>
          </div>
        </div>

        <div class="card" style="margin-top:16px">
          <div class="card-body" style="padding:16px">
            <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:8px"><i class="fa fa-info-circle" style="color:#4f46e5;margin-right:4px"></i>Keterangan</div>
            <ul style="font-size:11px;color:#64748b;padding-left:16px;line-height:1.8;margin:0">
              <li>Logo tampil di landing page dan header</li>
              <li>Nama website tampil di browser tab</li>
              <li>Nama kampus tampil di sertifikat & PDF</li>
              <li>Logo sebaiknya berformat PNG transparan</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
