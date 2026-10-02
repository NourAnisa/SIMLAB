<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  skemas: Array
})

const showAddModal = ref(false)
const showEditModal = ref(false)

const form = useForm({
  nama_skema: '',
  deskripsi: '',
  logo: null
})

const editForm = useForm({
  _method: 'PUT',
  id: null,
  nama_skema: '',
  deskripsi: '',
  logo: null
})

const logoPreview = ref(null)
const editLogoPreview = ref(null)
const addFileInput = ref(null)
const editFileInput = ref(null)

function onAddFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.logo = file
    const reader = new FileReader()
    reader.onload = e => { logoPreview.value = e.target.result }
    reader.readAsDataURL(file)
  }
}

function onEditFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    editForm.logo = file
    const reader = new FileReader()
    reader.onload = e => { editLogoPreview.value = e.target.result }
    reader.readAsDataURL(file)
  }
}

function openAddModal() {
  form.reset()
  form.clearErrors()
  logoPreview.value = null
  showAddModal.value = true
}

function openEditModal(skema) {
  editForm.id = skema.id
  editForm.nama_skema = skema.nama_skema
  editForm.deskripsi = skema.deskripsi
  editForm.logo = null
  editLogoPreview.value = skema.logo ? `/storage-public/${skema.logo}` : null
  editForm.clearErrors()
  showEditModal.value = true
}

function submitAdd() {
  form.post('/admin/skema', {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showAddModal.value = false
      form.reset()
      logoPreview.value = null
    }
  })
}

function submitEdit() {
  editForm.post(`/admin/skema/${editForm.id}`, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}

function destroy(id) {
  if (!confirm('Hapus skema sertifikasi ini? Perhatian: Jadwal dan pendaftaran yang terkait mungkin ikut terhapus!')) return
  router.delete(`/admin/skema/${id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <AppLayout title="Skema Sertifikasi">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div>
        <p class="page-title">Skema Sertifikasi</p>
        <p class="page-sub">{{ skemas.length }} skema terdaftar</p>
      </div>
      <button @click="openAddModal"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(79,70,229,.3)">
        <i class="fa fa-plus"></i> Tambah Skema
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div>
      <div v-if="skemas.length === 0" class="card" style="text-align:center;padding:60px">
        <div style="font-size:48px;margin-bottom:12px">📋</div>
        <p style="font-size:15px;color:#64748b;font-weight:500;margin-bottom:16px">Belum ada skema sertifikasi.</p>
        <button @click="openAddModal"
          style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
          <i class="fa fa-plus"></i> Tambah Skema Pertama
        </button>
      </div>

      <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
        <div v-for="s in skemas" :key="s.id" class="card" style="border-top:4px solid #4f46e5;transition:all .25s">
          <div class="card-body" style="padding:20px">
            <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px">
              <img v-if="s.logo" :src="`/storage-public/${s.logo}`" :alt="s.nama_skema"
                style="width:48px;height:48px;border-radius:10px;object-fit:contain;border:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0;padding:4px">
              <div v-else style="width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa fa-certificate" style="color:#fff;font-size:18px"></i>
              </div>
              <div style="flex:1">
                <h4 style="font-size:15px;font-weight:700;color:#0f172a;line-height:1.3;margin:0">{{ s.nama_skema }}</h4>
                <div style="font-size:12px;color:#94a3b8;margin-top:2px">{{ s.jadwals_count || 0 }} jadwal tersedia</div>
              </div>
            </div>
            <p style="font-size:13px;color:#64748b;line-height:1.6;margin:0 0 16px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden">{{ s.deskripsi }}</p>
            <div style="display:flex;gap:8px">
              <button @click="openEditModal(s)"
                style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;background:#eef2ff;border:1px solid #c7d2fe;color:#4f46e5;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
                <i class="fa fa-edit"></i> Edit
              </button>
              <button @click="destroy(s.id)"
                style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
                <i class="fa fa-trash"></i> Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ADD MODAL -->
    <div v-if="showAddModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:540px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
            <i class="fa fa-certificate" style="color:#4f46e5"></i> Tambah Skema Sertifikasi
          </h3>
          <button @click="showAddModal = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
        </div>
        
        <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
          <ul style="margin-top:6px;padding-left:16px;margin-bottom:0">
            <li v-for="(err, idx) in Object.values(form.errors).flat()" :key="idx">{{ err }}</li>
          </ul>
        </div>
          
        <form @submit.prevent="submitAdd">
          <div style="margin-bottom:20px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Logo Aplikasi / Skema <span style="color:#94a3b8;font-weight:400">(opsional)</span>
            </label>
            <div @click="addFileInput.click()" style="border:2px dashed #c7d2fe;border-radius:12px;padding:20px;text-align:center;background:#f8faff;cursor:pointer">
              <div v-if="logoPreview" style="margin-bottom:10px">
                <img :src="logoPreview" style="height:64px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0">
              </div>
              <div v-else>
                <i class="fa fa-image" style="font-size:28px;color:#a5b4fc;margin-bottom:8px;display:block"></i>
                <p style="font-size:13px;color:#6366f1;font-weight:600;margin:0">Klik untuk upload logo</p>
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">PNG, JPG, SVG, WebP — maks. 2MB</p>
              </div>
              <input type="file" ref="addFileInput" @change="onAddFileChange" accept="image/*" style="display:none">
            </div>
          </div>
          
          <div style="margin-bottom:20px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Nama Skema <span style="color:#ef4444">*</span>
            </label>
            <input v-model="form.nama_skema" required placeholder="cth: Skema Administrasi Jaringan..." 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          
          <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Deskripsi <span style="color:#ef4444">*</span>
            </label>
            <textarea v-model="form.deskripsi" required rows="4" placeholder="Deskripsi detail skema sertifikasi ini..." 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;resize:vertical"></textarea>
          </div>
          
          <div style="display:flex;gap:10px">
            <button type="button" @click="showAddModal = false" :disabled="form.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="form.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ form.processing ? 'Menyimpan...' : 'Simpan Skema' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:540px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
            <i class="fa fa-edit" style="color:#4f46e5"></i> Edit Skema Sertifikasi
          </h3>
          <button @click="showEditModal = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
        </div>
        
        <div v-if="Object.keys(editForm.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
          <ul style="margin-top:6px;padding-left:16px;margin-bottom:0">
            <li v-for="(err, idx) in Object.values(editForm.errors).flat()" :key="idx">{{ err }}</li>
          </ul>
        </div>
          
        <form @submit.prevent="submitEdit">
          <div style="margin-bottom:20px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Logo Aplikasi / Skema <span style="color:#94a3b8;font-weight:400">(opsional)</span>
            </label>
            <div @click="editFileInput.click()" style="border:2px dashed #c7d2fe;border-radius:12px;padding:20px;text-align:center;background:#f8faff;cursor:pointer">
              <div v-if="editLogoPreview" style="margin-bottom:10px">
                <img :src="editLogoPreview" style="height:64px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0">
                <div style="font-size:12px;color:#4f46e5;margin-top:8px">Klik untuk mengganti logo</div>
              </div>
              <div v-else>
                <i class="fa fa-image" style="font-size:28px;color:#a5b4fc;margin-bottom:8px;display:block"></i>
                <p style="font-size:13px;color:#6366f1;font-weight:600;margin:0">Klik untuk upload logo</p>
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">PNG, JPG, SVG, WebP — maks. 2MB</p>
              </div>
              <input type="file" ref="editFileInput" @change="onEditFileChange" accept="image/*" style="display:none">
            </div>
          </div>
          
          <div style="margin-bottom:20px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Nama Skema <span style="color:#ef4444">*</span>
            </label>
            <input v-model="editForm.nama_skema" required 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          
          <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Deskripsi <span style="color:#ef4444">*</span>
            </label>
            <textarea v-model="editForm.deskripsi" required rows="4" 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;resize:vertical"></textarea>
          </div>
          
          <div style="display:flex;gap:10px">
            <button type="button" @click="showEditModal = false" :disabled="editForm.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="editForm.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="editForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="editForm.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ editForm.processing ? 'Menyimpan...' : 'Update Skema' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
