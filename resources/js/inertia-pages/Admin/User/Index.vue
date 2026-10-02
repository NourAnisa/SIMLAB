<script setup>
import { ref, watch } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  users: Object,
  stats: Object,
  filters: Object,
  programStudis: Array
})

const search = ref(props.filters?.search || '')
const roleFilter = ref(props.filters?.role || '')

const showEditModal = ref(false)

const addForm = useForm({
  name: '',
  email: '',
  role: 'mahasiswa',
  nim_nip: '',
  jurusan: '',
  password: '',
  password_confirmation: ''
})

const editForm = useForm({
  id: null,
  name: '',
  email: '',
  role: 'mahasiswa',
  nim_nip: '',
  jurusan: '',
  password: '',
  password_confirmation: ''
})

function loadData() {
  const params = {}
  if (search.value) params.search = search.value
  if (roleFilter.value) params.role = roleFilter.value
  
  router.get('/admin/users', params, {
    preserveState: true,
    preserveScroll: true
  })
}

// Debounce search
let searchTimeout = null
watch([search, roleFilter], () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadData()
  }, 300)
})

function clearFilter() {
  search.value = ''
  roleFilter.value = ''
}

function openEditModal(u) {
  editForm.id = u.id
  editForm.name = u.name
  editForm.email = u.email
  editForm.role = u.role
  editForm.nim_nip = u.nim_nip || ''
  editForm.jurusan = u.jurusan || ''
  editForm.password = ''
  editForm.password_confirmation = ''
  editForm.clearErrors()
  showEditModal.value = true
}

function submitAdd() {
  addForm.post('/admin/users', {
    onSuccess: () => {
      addForm.reset()
      addForm.clearErrors()
    }
  })
}

function submitEdit() {
  const payload = { ...editForm.data() }
  if (!payload.password) {
    delete payload.password
    delete payload.password_confirmation
  }
  
  router.put(`/admin/users/${editForm.id}`, payload, {
    onSuccess: () => {
      showEditModal.value = false
    },
    onError: (errors) => {
      editForm.errors = errors
    }
  })
}

function destroy(id, name) {
  if (!confirm(`Hapus user ${name}?`)) return
  router.delete(`/admin/users/${id}`)
}

function getRoleConfig(role) {
  const cfg = {
    'mahasiswa': { bg: '#f0f9ff', border: '#bae6fd', color: '#0369a1', label: 'Mahasiswa' },
    'admin': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', label: 'Admin' },
    'kepala_lab': { bg: '#fdf4ff', border: '#e9d5ff', color: '#6b21a8', label: 'Kepala Lab' },
    'dosen': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', label: 'Dosen' }
  }
  return cfg[role] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: role }
}

</script>

<template>
  <AppLayout title="Manajemen User">
    <div style="margin-bottom:20px">
      <p class="page-title">Manajemen User</p>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>
    
    <div v-if="$page.props.flash?.error" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-times-circle"></i> {{ $page.props.flash.error }}
    </div>

    <!-- STATS -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px">
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-users" style="color:#4f46e5;font-size:15px"></i>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a">{{ stats.total }}</div>
            <div style="font-size:11px;color:#64748b">Total User</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-graduation-cap" style="color:#0ea5e9;font-size:15px"></i>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a">{{ stats.mahasiswa }}</div>
            <div style="font-size:11px;color:#64748b">Mahasiswa</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-chalkboard-teacher" style="color:#15803d;font-size:15px"></i>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a">{{ stats.dosen }}</div>
            <div style="font-size:11px;color:#64748b">Dosen</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:14px 16px;display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa fa-user-shield" style="color:#d97706;font-size:15px"></i>
          </div>
          <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a">{{ stats.admin_lab }}</div>
            <div style="font-size:11px;color:#64748b">Admin / Lab</div>
          </div>
        </div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">
      <!-- USER TABLE -->
      <div>
        <!-- FILTER -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-body" style="padding:14px 16px">
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
              <input v-model="search" placeholder="Cari nama/email/NIM..."
                style="flex:1;min-width:160px;padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none"
                @focus="$event.currentTarget.style.borderColor='#4f46e5'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
              <select v-model="roleFilter" style="padding:9px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff">
                <option value="">Semua Role</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="admin">Admin</option>
                <option value="kepala_lab">Kepala Lab</option>
                <option value="dosen">Dosen</option>
              </select>
              <button v-if="search || roleFilter" @click="clearFilter"
                style="padding:9px 14px;background:#f1f5f9;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="fa fa-users" style="color:#4f46e5;margin-right:8px"></i>Daftar User</h3>
            <span style="font-size:12px;color:#94a3b8">{{ users.total }} user</span>
          </div>
          
          <div style="overflow-x:auto">
            <div v-if="users.data.length === 0" style="padding:40px;text-align:center">
              <p style="font-size:14px;color:#64748b">Tidak ada user ditemukan.</p>
            </div>
            <table v-else style="width:100%;border-collapse:collapse">
              <thead>
                <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
                  <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">User</th>
                  <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">NIM/NIP</th>
                  <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Role</th>
                  <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="u in users.data" :key="u.id" 
                  style="border-bottom:1px solid #f1f5f9;transition:background .15s" 
                  @mouseover="$event.currentTarget.style.background='#fafbff'" 
                  @mouseout="$event.currentTarget.style.background='transparent'">
                  <td style="padding:12px 16px">
                    <div style="display:flex;align-items:center;gap:10px">
                      <img v-if="u.foto_profil" :src="`/files/${u.foto_profil}`" alt="foto"
                        style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0">
                      <div v-else style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0">
                        {{ u.name.substring(0, 1).toUpperCase() }}
                      </div>
                      <div>
                        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ u.name }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ u.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td style="padding:12px 16px;font-size:13px;color:#475569">{{ u.nim_nip || '—' }}</td>
                  <td style="padding:12px 16px">
                    <span :style="`background:${getRoleConfig(u.role).bg};border:1px solid ${getRoleConfig(u.role).border};color:${getRoleConfig(u.role).color};border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700`">
                      {{ getRoleConfig(u.role).label }}
                    </span>
                  </td>
                  <td style="padding:12px 16px;text-align:center">
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                      <button @click="openEditModal(u)"
                        style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit">
                        <i class="fa fa-edit"></i> Edit
                      </button>
                      <button @click="destroy(u.id, u.name)"
                        style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="users.last_page > 1" style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
            <Link v-for="(link, index) in users.links" :key="index"
              :href="link.url || ''"
              v-html="link.label"
              preserve-scroll
              :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
            </Link>
          </div>
        </div>
      </div>

      <!-- TAMBAH USER FORM -->
      <div class="card" style="position:sticky;top:20px">
        <div class="card-header">
          <h3><i class="fa fa-user-plus" style="color:#10b981;margin-right:8px"></i>Tambah User Baru</h3>
        </div>
        <div class="card-body">
          <div v-if="Object.keys(addForm.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px">
            <div v-for="(errs, key) in addForm.errors" :key="key">
              <div v-for="(e, i) in errs" :key="i">• {{ e }}</div>
            </div>
          </div>

          <form @submit.prevent="submitAdd">
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Nama Lengkap</label>
              <input v-model="addForm.name" type="text" required placeholder="Nama lengkap..."
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Email</label>
              <input v-model="addForm.email" type="email" required placeholder="email@example.com"
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">NIM / NIP</label>
              <input v-model="addForm.nim_nip" type="text" placeholder="Nomor Induk..."
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Program Studi / Jurusan</label>
              <select v-model="addForm.jurusan"
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
                <option value="">-- Pilih Program Studi --</option>
                <option v-for="ps in programStudis" :key="ps.id" :value="ps.nama_program_studi">{{ ps.jenjang ? ps.jenjang + ' - ' : '' }}{{ ps.nama_program_studi }}</option>
              </select>
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Role</label>
              <select v-model="addForm.role" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="admin">Admin</option>
                <option value="kepala_lab">Kepala Lab</option>
                <option value="dosen">Dosen</option>
              </select>
            </div>
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Password</label>
              <input v-model="addForm.password" type="password" required placeholder="Min 8 karakter"
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
            </div>
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Konfirmasi Password</label>
              <input v-model="addForm.password_confirmation" type="password" required placeholder="Ulangi password"
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#f8fafc;box-sizing:border-box">
            </div>
            <button type="submit" :disabled="addForm.processing"
              style="width:100%;padding:11px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:14px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px"
              :style="addForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="addForm.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-user-plus"></i> 
              {{ addForm.processing ? 'Menyimpan...' : 'Tambah User' }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:16px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9">
          <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0"><i class="fa fa-edit" style="color:#4f46e5;margin-right:8px"></i>Edit User</h3>
          <button @click="showEditModal = false" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:4px">✕</button>
        </div>
        <div style="padding:24px">
          <div v-if="Object.keys(editForm.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px">
            <div v-for="(errs, key) in editForm.errors" :key="key">
              <div v-for="(e, i) in errs" :key="i">• {{ e }}</div>
            </div>
          </div>

          <form @submit.prevent="submitEdit">
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap</label>
              <input v-model="editForm.name" type="text" required
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
            </div>
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Email</label>
              <input v-model="editForm.email" type="email" required
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
            </div>
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">NIM / NIP</label>
              <input v-model="editForm.nim_nip" type="text"
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
            </div>
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Program Studi / Jurusan</label>
              <select v-model="editForm.jurusan"
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;background:#fff">
                <option value="">-- Pilih Program Studi --</option>
                <option v-for="ps in programStudis" :key="ps.id" :value="ps.nama_program_studi">{{ ps.jenjang ? ps.jenjang + ' - ' : '' }}{{ ps.nama_program_studi }}</option>
              </select>
            </div>
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Role</label>
              <select v-model="editForm.role" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;background:#fff">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="admin">Admin</option>
                <option value="kepala_lab">Kepala Lab</option>
                <option value="dosen">Dosen</option>
              </select>
            </div>
            <div style="margin-bottom:16px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Password Baru <span style="color:#94a3b8;font-weight:400">(kosongkan jika tidak diubah)</span></label>
              <input v-model="editForm.password" type="password" placeholder="Password baru..."
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
            </div>
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Konfirmasi Password</label>
              <input v-model="editForm.password_confirmation" type="password" placeholder="Ulangi password baru..."
                style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box">
            </div>
            <button type="submit" :disabled="editForm.processing"
              style="width:100%;padding:12px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;font-size:14px;font-weight:700;font-family:inherit;border:none;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="editForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="editForm.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
