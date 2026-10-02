<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

const props = defineProps({
  user: Object,
  validation: Object,
  programStudis: Array,
})

const page = usePage()

const form = useForm({
  name: props.user.name || '',
  email: props.user.email || '',
  nim_nip: props.user.nim_nip || '',
  jurusan: props.user.jurusan || '',
  foto_profil: null,
  tanda_tangan: null,
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const fotoRef = ref(null)
const ttdRef = ref(null)
const fotoPreview = ref(props.user.foto_profil ? `/files/${props.user.foto_profil}` : null)
const ttdPreview = ref(props.user.tanda_tangan ? `/files/${props.user.tanda_tangan}` : null)

function onFotoChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.foto_profil = file
    const reader = new FileReader()
    reader.onload = e => { fotoPreview.value = e.target.result }
    reader.readAsDataURL(file)
  }
}

function onTtdChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.tanda_tangan = file
    const reader = new FileReader()
    reader.onload = e => { ttdPreview.value = e.target.result }
    reader.readAsDataURL(file)
  }
}

function submitUpdate() {
  form.transform(data => ({
    ...data,
    _method: 'PATCH',
  })).post('/profile', {
    forceFormData: true,
    preserveScroll: true,
  })
}

function updatePassword() {
  passwordForm.put('/password', {
    preserveScroll: true,
    errorBag: 'updatePassword',
    onSuccess: () => passwordForm.reset(),
    onError: () => {
      if (passwordForm.errors.password) {
        passwordForm.reset('password', 'password_confirmation')
      }
      if (passwordForm.errors.current_password) {
        passwordForm.reset('current_password')
      }
    },
  })
}
</script>

<template>
  <AppLayout title="Profil Saya">
    <div style="margin-bottom:20px">
      <p class="page-title">Profil Saya</p>
      <p class="page-sub">Perbarui informasi akun dan foto profil Anda.</p>
    </div>

    <!-- Flash Messages -->
    <div v-if="$page.props.flash?.status === 'profile-updated' || $page.props.flash?.success"
      style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> Profil berhasil diperbarui.
    </div>

    <!-- Validation Warnings (for mahasiswa) -->
    <div v-if="validation && !validation.is_complete && user.role === 'mahasiswa'"
      style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px">
      <div style="display:flex;align-items:center;gap:8px;font-weight:700;margin-bottom:8px">
        <i class="fa fa-exclamation-triangle"></i> Profil Belum Lengkap
      </div>
      <ul style="margin:0;padding-left:16px">
        <li v-for="(msg, i) in validation.messages" :key="i">{{ msg }}</li>
      </ul>
    </div>

    <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start">
      <!-- FOTO PROFIL -->
      <div class="card">
        <div class="card-body" style="text-align:center;padding:24px">
          <div @click="fotoRef?.click()" style="cursor:pointer;margin-bottom:16px">
            <img v-if="fotoPreview" :src="fotoPreview" alt="Foto Profil"
              style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:4px solid #e2e8f0;margin:0 auto;display:block">
            <div v-else
              style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:32px;font-weight:800;color:#fff">
              {{ user.name?.substring(0, 1).toUpperCase() }}
            </div>
            <div style="margin-top:10px;font-size:12px;color:#6366f1;font-weight:600">
              <i class="fa fa-camera" style="margin-right:4px"></i>Ganti Foto
            </div>
          </div>
          <input ref="fotoRef" type="file" @change="onFotoChange" accept="image/*" style="display:none">

          <div style="font-size:16px;font-weight:700;color:#0f172a">{{ user.name }}</div>
          <div style="font-size:12px;color:#64748b;text-transform:capitalize;margin-top:2px">{{ user.role?.replace('_', ' ') }}</div>
          <div style="font-size:12px;color:#94a3b8;margin-top:4px">{{ user.email }}</div>
        </div>

        <!-- TANDA TANGAN (Kepala Lab only) -->
        <template v-if="user.role === 'kepala_lab'">
          <div style="border-top:1px solid #f1f5f9;padding:16px">
            <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:10px">
              <i class="fa fa-signature" style="margin-right:4px;color:#4f46e5"></i>Tanda Tangan Digital
            </div>
            <div @click="ttdRef?.click()"
              style="border:2px dashed #c7d2fe;border-radius:10px;padding:14px;text-align:center;background:#f8faff;cursor:pointer">
              <img v-if="ttdPreview" :src="ttdPreview" alt="TTD" style="max-height:60px;max-width:100%;object-fit:contain;display:block;margin:0 auto">
              <div v-else>
                <i class="fa fa-pen-nib" style="font-size:24px;color:#a5b4fc;display:block;margin-bottom:4px"></i>
                <p style="font-size:12px;color:#6366f1;font-weight:600;margin:0">Upload Tanda Tangan</p>
              </div>
            </div>
            <input ref="ttdRef" type="file" @change="onTtdChange" accept="image/*" style="display:none">
          </div>
        </template>
      </div>

      <!-- FORM DATA PROFIL -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa fa-user-edit" style="color:#4f46e5;margin-right:8px"></i>Data Pribadi</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(form.errors).length > 0"
            style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px">
            <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
            <ul style="margin-top:6px;padding-left:16px;margin-bottom:0">
              <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
            </ul>
          </div>

          <form @submit.prevent="submitUpdate">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap <span style="color:#ef4444">*</span></label>
                <input v-model="form.name" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
              </div>
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Email <span style="color:#ef4444">*</span></label>
                <input v-model="form.email" type="email" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
              </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">NIM / NIP</label>
                <input v-model="form.nim_nip" placeholder="Nomor Induk..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
              </div>
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Program Studi / Jurusan</label>
                <select v-model="form.jurusan" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;background:#fff;font-family:inherit">
                  <option value="">-- Pilih Program Studi --</option>
                  <option v-for="ps in programStudis" :key="ps.id" :value="ps.nama_program_studi">
                    {{ ps.jenjang ? ps.jenjang + ' - ' : '' }}{{ ps.nama_program_studi }}
                  </option>
                </select>
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end">
              <button type="submit" :disabled="form.processing"
                style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(79,70,229,.3)"
                :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-save"></i>
                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- FORM UBAH PASSWORD -->
      <div class="card" style="margin-top:24px">
        <div class="card-header">
          <h3><i class="fa fa-lock" style="color:#ef4444;margin-right:8px"></i>Ubah Password</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(passwordForm.errors).length > 0"
            style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px">
            <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
            <ul style="margin-top:6px;padding-left:16px;margin-bottom:0">
              <li v-for="(err, key) in passwordForm.errors" :key="key">{{ err }}</li>
            </ul>
          </div>
          
          <div v-if="$page.props.flash?.status === 'password-updated'"
            style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px">
            <i class="fa fa-check-circle"></i> Password berhasil diperbarui.
          </div>

          <form @submit.prevent="updatePassword">
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password Saat Ini</label>
              <input v-model="passwordForm.current_password" type="password" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password Baru</label>
                <input v-model="passwordForm.password" type="password" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
              </div>
              <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Konfirmasi Password Baru</label>
                <input v-model="passwordForm.password_confirmation" type="password" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
              </div>
            </div>

            <div style="display:flex;justify-content:flex-end">
              <button type="submit" :disabled="passwordForm.processing"
                style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#ef4444,#b91c1c);color:#fff;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(239,68,68,.3)"
                :style="passwordForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="passwordForm.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-key"></i>
                {{ passwordForm.processing ? 'Menyimpan...' : 'Perbarui Password' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
