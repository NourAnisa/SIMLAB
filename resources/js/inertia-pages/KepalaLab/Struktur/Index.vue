<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  strukturs: Array
})

const showAddModal = ref(false)
const showEditModal = ref(false)

const form = useForm({
  nama: '',
  jabatan: '',
  deskripsi: '',
  urutan: 0,
  foto: null
})

const editForm = useForm({
  _method: 'PUT',
  id: null,
  nama: '',
  jabatan: '',
  deskripsi: '',
  urutan: 0,
  foto: null
})

const fotoName = ref('Pilih Foto')
const editFotoName = ref('Ganti Foto')
const addFileInput = ref(null)
const editFileInput = ref(null)

function onAddFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.foto = file
    fotoName.value = file.name
  }
}

function onEditFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    editForm.foto = file
    editFotoName.value = file.name
  }
}

function openAddModal() {
  form.reset()
  fotoName.value = 'Pilih Foto'
  showAddModal.value = true
}

function openEditModal(struktur) {
  editForm.id = struktur.id
  editForm.nama = struktur.nama
  editForm.jabatan = struktur.jabatan
  editForm.deskripsi = struktur.deskripsi || ''
  editForm.urutan = struktur.urutan
  editForm.foto = null
  editFotoName.value = 'Ganti Foto'
  showEditModal.value = true
}

function submitAdd() {
  form.post('/kepala_lab/struktur', {
    onSuccess: () => {
      showAddModal.value = false
    }
  })
}

function submitEdit() {
  editForm.post(`/kepala_lab/struktur/${editForm.id}`, {
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}

function destroy(id) {
  if (!confirm('Hapus anggota ini dari struktur organisasi?')) return
  router.delete(`/kepala_lab/struktur/${id}`)
}
</script>

<template>
  <AppLayout title="Struktur Organisasi">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div>
        <p class="page-title">Struktur Organisasi Lab</p>
        <p class="page-sub">Kelola susunan organisasi laboratorium yang tampil di halaman publik.</p>
      </div>
      <button @click="openAddModal"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
        <i class="fa fa-plus"></i> Tambah Anggota
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div>
      <div v-if="strukturs.length === 0" style="grid-column:1/-1;text-align:center;padding:60px;color:#94a3b8;background:#fff;border-radius:16px;border:1px solid #f1f5f9">
        <div style="font-size:48px;margin-bottom:12px">👥</div>
        <p style="font-size:16px;font-weight:600">Belum ada data struktur organisasi</p>
      </div>

      <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px">
        <div v-for="s in strukturs" :key="s.id" style="background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;border:1px solid #f1f5f9">
          <div style="height:6px;background:linear-gradient(90deg,#4f46e5,#7c3aed)"></div>
          <div style="padding:20px;text-align:center">
            <img v-if="s.foto" :src="`/files/${s.foto}`" :alt="s.nama"
              style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e0e7ff;margin-bottom:12px">
            <div v-else style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px;color:#fff;font-weight:700">
              {{ s.nama.substring(0, 1).toUpperCase() }}
            </div>
            
            <div style="font-size:13px;font-weight:600;color:#4f46e5;background:#ede9fe;padding:3px 10px;border-radius:20px;display:inline-block;margin-bottom:8px">
              {{ s.jabatan }}
            </div>
            <h4 style="margin:0 0 6px;font-size:15px;color:#1e293b;font-weight:700">{{ s.nama }}</h4>
            <p v-if="s.deskripsi" style="margin:0;font-size:12px;color:#64748b;line-height:1.5">{{ s.deskripsi }}</p>
          </div>
          <div style="padding:12px 20px;border-top:1px solid #f1f5f9;display:flex;gap:8px">
            <button @click="openEditModal(s)"
              style="flex:1;padding:7px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;color:#4f46e5">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button @click="destroy(s.id)" 
              style="flex:1;padding:7px;background:#fff5f5;border:1px solid #fecaca;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;color:#dc2626">
              <i class="fa fa-trash"></i> Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ADD MODAL -->
    <div v-if="showAddModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b">Tambah Anggota</h3>
          <button @click="showAddModal = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
        </div>
        
        <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
          <ul style="margin-top:6px;padding-left:16px">
            <li v-for="(err, idx) in Object.values(form.errors).flat()" :key="idx">{{ err }}</li>
          </ul>
        </div>
          
        <form @submit.prevent="submitAdd">
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap *</label>
            <input v-model="form.nama" required placeholder="cth: Dr. Ahmad, M.Kom" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jabatan *</label>
            <input v-model="form.jabatan" required placeholder="cth: Kepala Laboratorium" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Deskripsi</label>
            <textarea v-model="form.deskripsi" rows="2" placeholder="Uraian singkat tugas/peran..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;resize:none;box-sizing:border-box"></textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
            <div>
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Foto</label>
              <div @click="addFileInput.click()" style="padding:10px;border:1.5px dashed #cbd5e1;border-radius:10px;text-align:center;cursor:pointer;font-size:13px;color:#64748b">
                <i class="fa fa-image"></i> {{ fotoName }}
              </div>
              <input type="file" ref="addFileInput" @change="onAddFileChange" accept="image/*" style="display:none">
            </div>
            <div>
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Urutan</label>
              <input v-model="form.urutan" type="number" min="0" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
            </div>
          </div>
          <div style="display:flex;gap:10px;margin-top:20px">
            <button type="button" @click="showAddModal = false" :disabled="form.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="form.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b">Edit Anggota</h3>
          <button @click="showEditModal = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
        </div>
        
        <div v-if="Object.keys(editForm.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
          <ul style="margin-top:6px;padding-left:16px">
            <li v-for="(err, idx) in Object.values(editForm.errors).flat()" :key="idx">{{ err }}</li>
          </ul>
        </div>

        <form @submit.prevent="submitEdit">
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap *</label>
            <input v-model="editForm.nama" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jabatan *</label>
            <input v-model="editForm.jabatan" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Deskripsi</label>
            <textarea v-model="editForm.deskripsi" rows="2" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;resize:none;box-sizing:border-box"></textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
            <div>
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Ganti Foto</label>
              <div @click="editFileInput.click()" style="padding:10px;border:1.5px dashed #cbd5e1;border-radius:10px;text-align:center;cursor:pointer;font-size:13px;color:#64748b">
                <i class="fa fa-image"></i> {{ editFotoName }}
              </div>
              <input type="file" ref="editFileInput" @change="onEditFileChange" accept="image/*" style="display:none">
            </div>
            <div>
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Urutan</label>
              <input v-model="editForm.urutan" type="number" min="0" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
            </div>
          </div>
          <div style="display:flex;gap:10px;margin-top:20px">
            <button type="button" @click="showEditModal = false" :disabled="editForm.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="editForm.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="editForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="editForm.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ editForm.processing ? 'Menyimpan...' : 'Update' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
