<script setup>
import { ref, watch } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  beritas: Object,
  kategoris: Array,
  filters: Object
})

const filterSearch = ref(props.filters?.search || '')
const filterKategori = ref(props.filters?.kategori || '')

const showAddModal = ref(false)
const showEditModal = ref(false)

const form = useForm({
  judul: '',
  kategori: '',
  ringkasan: '',
  konten: '',
  gambar: null
})

const editForm = useForm({
  id: null,
  _method: 'PUT',
  judul: '',
  kategori: '',
  ringkasan: '',
  konten: '',
  gambar: null,
  currentGambar: ''
})

const fileInput = ref(null)
const editFileInput = ref(null)

function loadData() {
  const params = {}
  if (filterSearch.value) params.search = filterSearch.value
  if (filterKategori.value) params.kategori = filterKategori.value

  router.get('/admin/berita', params, {
    preserveState: true,
    preserveScroll: true
  })
}

let filterTimeout = null
watch([filterSearch, filterKategori], () => {
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => loadData(), 300)
})

function clearFilters() {
  filterSearch.value = ''
  filterKategori.value = ''
}

function handleFile(e, type) {
  const file = e.target.files[0]
  if (type === 'add') {
    form.gambar = file
  } else {
    editForm.gambar = file
  }
}

function openAddModal() {
  form.reset()
  form.clearErrors()
  if (fileInput.value) fileInput.value.value = ''
  showAddModal.value = true
}

function submitAdd() {
  form.post('/admin/berita', {
    onSuccess: () => {
      showAddModal.value = false
      form.reset()
    }
  })
}

function openEditModal(b) {
  editForm.id = b.id
  editForm.judul = b.judul
  editForm.kategori = b.kategori || ''
  editForm.ringkasan = b.ringkasan || ''
  editForm.konten = b.konten
  editForm.gambar = null
  editForm.currentGambar = b.gambar
  editForm.clearErrors()
  if (editFileInput.value) editFileInput.value.value = ''
  showEditModal.value = true
}

function submitEdit() {
  editForm.post(`/admin/berita/${editForm.id}`, {
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}

function hapusBerita(id) {
  if (!confirm('Hapus berita ini?')) return
  router.delete(`/admin/berita/${id}`)
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric'
  })
}

function getKatConfig(kat) {
  const colors = {
    'Pengumuman': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8' },
    'Kegiatan': { bg: '#f0fdf4', border: '#86efac', color: '#15803d' },
    'Info Wisuda': { bg: '#fdf4ff', border: '#e9d5ff', color: '#7c3aed' },
    'Akademik': { bg: '#fff7ed', border: '#fed7aa', color: '#c2410c' },
    'Lainnya': { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b' }
  }
  return colors[kat] || colors['Lainnya']
}
</script>

<template>
  <AppLayout title="Berita & Pengumuman">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
      <div>
        <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0">Manajemen Berita & Pengumuman</h2>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0">Kelola konten berita dan pengumuman yang tampil di halaman publik.</p>
      </div>
      <button @click="openAddModal"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit">
        <i class="fa fa-plus"></i> Tambah Berita
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <!-- FILTER -->
    <div class="card" style="margin-bottom:16px">
      <div class="card-body" style="padding:12px 16px">
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <input v-model="filterSearch" placeholder="Cari judul..."
            style="flex:1;min-width:200px;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none"
            @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
          <select v-model="filterKategori" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff"
            @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
            <option value="">Semua Kategori</option>
            <option value="Pengumuman">Pengumuman</option>
            <option value="Kegiatan">Kegiatan</option>
            <option value="Info Wisuda">Info Wisuda</option>
            <option value="Akademik">Akademik</option>
            <option value="Lainnya">Lainnya</option>
          </select>
          <button v-if="filterSearch || filterKategori" @click="clearFilters"
            style="padding:8px 14px;background:#f1f5f9;color:#64748b;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer">
            <i class="fa fa-times"></i> Reset
          </button>
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="card-body" style="padding:0">
        <div v-if="beritas.data.length === 0" style="padding:48px;text-align:center">
          <div style="font-size:40px;margin-bottom:12px">📰</div>
          <p style="font-size:14px;color:#64748b;margin:0">Belum ada berita. Klik <strong>Tambah Berita</strong> untuk mulai.</p>
        </div>
        
        <div v-else style="overflow-x:auto">
          <table style="width:100%;border-collapse:collapse">
            <thead>
              <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Berita</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Kategori</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Penulis</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Tanggal</th>
                <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="b in beritas.data" :key="b.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s" @mouseover="$event.currentTarget.style.background='#fafbff'" @mouseout="$event.currentTarget.style.background='transparent'">
                <td style="padding:12px 16px">
                  <div style="display:flex;align-items:center;gap:12px">
                    <img v-if="b.gambar" :src="`/storage-public/${b.gambar}`" alt="gambar"
                      style="width:50px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0">
                    <div v-else style="width:50px;height:40px;border-radius:6px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <i class="fa fa-newspaper" style="color:#fff;font-size:14px"></i>
                    </div>
                    <div>
                      <div style="font-size:13px;font-weight:600;color:#0f172a;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ b.judul }}</div>
                      <div v-if="b.ringkasan" style="font-size:11px;color:#94a3b8;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ b.ringkasan }}</div>
                    </div>
                  </div>
                </td>
                <td style="padding:12px 16px">
                  <span :style="`background:${getKatConfig(b.kategori).bg};border:1px solid ${getKatConfig(b.kategori).border};color:${getKatConfig(b.kategori).color};border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700`">
                    {{ b.kategori || '—' }}
                  </span>
                </td>
                <td style="padding:12px 16px">
                  <div style="font-size:12px;font-weight:600;color:#0f172a">{{ b.user?.name || '—' }}</div>
                  <span :style="`background:${b.user?.role === 'admin' ? '#fffbeb' : b.user?.role === 'kepala_lab' ? '#fdf4ff' : '#f8fafc'};color:${b.user?.role === 'admin' ? '#d97706' : b.user?.role === 'kepala_lab' ? '#7c3aed' : '#64748b'};border-radius:5px;padding:2px 7px;font-size:10px;font-weight:700`">
                    {{ b.user?.role === 'kepala_lab' ? 'Ketua Lab' : (b.user?.role ? b.user.role.charAt(0).toUpperCase() + b.user.role.slice(1) : '—') }}
                  </span>
                </td>
                <td style="padding:12px 16px;font-size:12px;color:#64748b">{{ formatDate(b.created_at) }}</td>
                <td style="padding:12px 16px;text-align:center">
                  <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <button @click="openEditModal(b)"
                      style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit">
                      <i class="fa fa-edit"></i> Edit
                    </button>
                    <button @click="hapusBerita(b.id)"
                      style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="beritas.last_page > 1" style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
          <Link v-for="(link, index) in beritas.links" :key="index"
            :href="link.url || ''"
            v-html="link.label"
            preserve-scroll
            :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
          </Link>
        </div>
      </div>
    </div>

    <!-- ADD MODAL -->
    <div v-if="showAddModal" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px" @click.self="showAddModal = false">
      <div style="background:#fff;border-radius:16px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,.2)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9;position:sticky;top:0;background:#fff;z-index:1">
          <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0"><i class="fa fa-plus" style="color:#f59e0b;margin-right:8px"></i>Tambah Berita</h3>
          <button @click="showAddModal = false" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:4px"><i class="fa fa-times"></i></button>
        </div>
        <div style="padding:24px">
          <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px">
            <div v-for="(err, field) in form.errors" :key="field">• {{ err }}</div>
          </div>
          <form @submit.prevent="submitAdd">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
              <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Judul Berita <span style="color:#ef4444">*</span></label>
                <input v-model="form.judul" type="text" placeholder="Judul berita..." required
                  style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
              </div>
              <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Kategori</label>
                <select v-model="form.kategori" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                  <option value="">-- Pilih Kategori --</option>
                  <option value="Pengumuman">Pengumuman</option>
                  <option value="Kegiatan">Kegiatan</option>
                  <option value="Info Wisuda">Info Wisuda</option>
                  <option value="Akademik">Akademik</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
              <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Gambar <span style="color:#94a3b8">(opsional, maks. 2MB)</span></label>
                <input ref="fileInput" type="file" accept="image/*" @change="e => handleFile(e, 'add')"
                  style="width:100%;padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
              </div>
              <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Ringkasan <span style="color:#94a3b8">(maks. 255 karakter)</span></label>
                <input v-model="form.ringkasan" type="text" placeholder="Deskripsi singkat yang tampil di kartu berita..." maxlength="255"
                  style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
              </div>
              <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Isi Konten <span style="color:#ef4444">*</span></label>
                <textarea v-model="form.konten" rows="6" placeholder="Tulis isi berita di sini..." required
                  style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;resize:vertical;box-sizing:border-box"
                  @focus="$event.currentTarget.style.borderColor='#f59e0b'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'"></textarea>
              </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
              <button type="button" @click="showAddModal = false"
                style="padding:10px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">Batal</button>
              <button type="submit" :disabled="form.processing"
                style="padding:10px 24px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px"
                :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-save"></i> 
                {{ form.processing ? 'Menyimpan...' : 'Simpan Berita' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px" @click.self="showEditModal = false">
      <div style="background:#fff;border-radius:16px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,.2)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9;position:sticky;top:0;background:#fff;z-index:1">
          <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0"><i class="fa fa-edit" style="color:#4f46e5;margin-right:8px"></i>Edit Berita</h3>
          <button @click="showEditModal = false" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:4px"><i class="fa fa-times"></i></button>
        </div>
        <div style="padding:24px">
          <div v-if="Object.keys(editForm.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px">
            <div v-for="(err, field) in editForm.errors" :key="field">• {{ err }}</div>
          </div>
          <form @submit.prevent="submitEdit">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
              <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Judul Berita <span style="color:#ef4444">*</span></label>
                <input v-model="editForm.judul" type="text" placeholder="Judul berita..." required
                  style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box"
                  @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
              </div>
              <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Kategori</label>
                <select v-model="editForm.kategori" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff"
                  @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
                  <option value="">-- Pilih Kategori --</option>
                  <option value="Pengumuman">Pengumuman</option>
                  <option value="Kegiatan">Kegiatan</option>
                  <option value="Info Wisuda">Info Wisuda</option>
                  <option value="Akademik">Akademik</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
              <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Ganti Gambar <span style="color:#94a3b8">(opsional)</span></label>
                <input ref="editFileInput" type="file" accept="image/*" @change="e => handleFile(e, 'edit')"
                  style="width:100%;padding:7px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
                <div v-if="editForm.currentGambar" style="margin-top:8px">
                  <img :src="`/storage-public/${editForm.currentGambar}`" style="height:50px;border-radius:6px;object-fit:cover">
                  <span style="font-size:11px;color:#94a3b8;margin-left:6px">Gambar saat ini</span>
                </div>
              </div>
              <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Ringkasan</label>
                <input v-model="editForm.ringkasan" type="text" placeholder="Deskripsi singkat..." maxlength="255"
                  style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box"
                  @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
              </div>
              <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Isi Konten <span style="color:#ef4444">*</span></label>
                <textarea v-model="editForm.konten" rows="6" required
                  style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;resize:vertical;box-sizing:border-box"
                  @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'"></textarea>
              </div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
              <button type="button" @click="showEditModal = false"
                style="padding:10px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">Batal</button>
              <button type="submit" :disabled="editForm.processing"
                style="padding:10px 24px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px"
                :style="editForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="editForm.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-save"></i> 
                {{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
