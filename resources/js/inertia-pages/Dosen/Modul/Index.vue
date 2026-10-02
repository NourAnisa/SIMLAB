<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  moduls: Object,
})

const showAddModal = ref(false)
const fileRef = ref(null)

const form = useForm({
  nama_modul: '',
  mata_kuliah: '',
  deskripsi: '',
  file: null,
})

function onFileChange(e) {
  form.file = e.target.files[0]
}

function submitAdd() {
  form.post('/dosen/modul', {
    forceFormData: true,
    onSuccess: () => {
      showAddModal.value = false
      form.reset()
    }
  })
}

function destroy(id) {
  if (!confirm('Hapus modul ini?')) return
  router.delete(`/dosen/modul/${id}`, { preserveScroll: true })
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function getFileIcon(path) {
  if (!path) return 'fa-file'
  const ext = path.split('.').pop().toLowerCase()
  if (ext === 'pdf') return 'fa-file-pdf'
  if (['doc', 'docx'].includes(ext)) return 'fa-file-word'
  if (['ppt', 'pptx'].includes(ext)) return 'fa-file-powerpoint'
  return 'fa-file'
}
</script>

<template>
  <AppLayout title="Modul Praktikum">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div>
        <p class="page-title">Modul Praktikum</p>
        <p class="page-sub">Kelola modul praktikum yang dapat diakses mahasiswa.</p>
      </div>
      <button @click="showAddModal = true"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#06b6d4,#0891b2);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(6,182,212,.3)">
        <i class="fa fa-upload"></i> Unggah Modul
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div v-if="moduls.data.length === 0" class="card" style="text-align:center;padding:60px">
      <div style="font-size:48px;margin-bottom:12px">📚</div>
      <p style="font-size:15px;color:#64748b;font-weight:500">Belum ada modul yang diunggah.</p>
      <button @click="showAddModal = true"
        style="margin-top:16px;display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:linear-gradient(135deg,#06b6d4,#0891b2);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
        <i class="fa fa-upload"></i> Unggah Modul Pertama
      </button>
    </div>

    <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
      <div v-for="m in moduls.data" :key="m.id" class="card" style="border-top:4px solid #06b6d4">
        <div class="card-body" style="padding:20px">
          <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px">
            <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#06b6d4,#0891b2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i :class="`fa ${getFileIcon(m.file_path)}`" style="color:#fff;font-size:18px"></i>
            </div>
            <div style="flex:1;min-width:0">
              <h4 style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ m.nama_modul }}</h4>
              <div style="font-size:12px;color:#64748b;font-weight:500">{{ m.mata_kuliah }}</div>
            </div>
          </div>
          <p v-if="m.deskripsi" style="font-size:13px;color:#64748b;line-height:1.6;margin:0 0 16px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ m.deskripsi }}</p>
          <div style="font-size:11px;color:#94a3b8;margin-bottom:12px">{{ formatDate(m.created_at) }}</div>
          <div style="display:flex;gap:8px">
            <a :href="`/storage-public/${m.file_path}`" target="_blank"
              style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;background:#ecfeff;border:1px solid #a5f3fc;color:#0891b2;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none">
              <i class="fa fa-download"></i> Unduh
            </a>
            <button @click="destroy(m.id)"
              style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">
              <i class="fa fa-trash"></i> Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="moduls.last_page > 1" style="margin-top:20px;display:flex;justify-content:center;gap:8px">
      <Link v-for="(link, index) in moduls.links" :key="index"
        :href="link.url || ''" v-html="link.label" preserve-scroll
        :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#06b6d4' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
      </Link>
    </div>

    <!-- ADD MODAL -->
    <div v-if="showAddModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:520px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b">
            <i class="fa fa-upload" style="color:#06b6d4;margin-right:8px"></i>Unggah Modul Baru
          </h3>
          <button @click="showAddModal = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
        </div>

        <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <ul style="margin:0;padding-left:16px">
            <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
          </ul>
        </div>

        <form @submit.prevent="submitAdd">
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Modul <span style="color:#ef4444">*</span></label>
            <input v-model="form.nama_modul" required placeholder="cth: Modul Praktikum Jaringan Komputer..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Mata Kuliah <span style="color:#ef4444">*</span></label>
            <input v-model="form.mata_kuliah" required placeholder="cth: Jaringan Komputer" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Deskripsi <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
            <textarea v-model="form.deskripsi" rows="3" placeholder="Deskripsi singkat modul..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;resize:vertical;font-family:inherit"></textarea>
          </div>
          <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">File Modul <span style="color:#ef4444">*</span></label>
            <div @click="fileRef?.click()" style="border:2px dashed #a5f3fc;border-radius:12px;padding:20px;text-align:center;background:#ecfeff;cursor:pointer">
              <div v-if="form.file">
                <i class="fa fa-file-check" style="font-size:28px;color:#0891b2;margin-bottom:8px;display:block"></i>
                <p style="font-size:13px;color:#0891b2;font-weight:600;margin:0">{{ form.file.name }}</p>
              </div>
              <div v-else>
                <i class="fa fa-file-upload" style="font-size:28px;color:#67e8f9;margin-bottom:8px;display:block"></i>
                <p style="font-size:13px;color:#0891b2;font-weight:600;margin:0">Klik untuk pilih file</p>
                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">PDF, DOC, DOCX, PPT, PPTX — maks. 10MB</p>
              </div>
              <input ref="fileRef" type="file" @change="onFileChange" accept=".pdf,.doc,.docx,.ppt,.pptx" style="display:none">
            </div>
          </div>
          <div style="display:flex;gap:10px">
            <button type="button" @click="showAddModal = false" :disabled="form.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="form.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#06b6d4,#0891b2);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-upload"></i>
              {{ form.processing ? 'Mengunggah...' : 'Unggah Modul' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
