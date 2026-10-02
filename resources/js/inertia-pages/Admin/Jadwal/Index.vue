<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  jadwals: Object,
  skemas: Array
})

const showAddModal = ref(false)
const showEditModal = ref(false)

const form = useForm({
  skema_id: '',
  tanggal: '',
  kuota: 1
})

const editForm = useForm({
  id: null,
  skema_id: '',
  tanggal: '',
  kuota: 1
})

function openAddModal() {
  form.reset()
  form.clearErrors()
  showAddModal.value = true
}

function openEditModal(jadwal) {
  editForm.id = jadwal.id
  editForm.skema_id = jadwal.skema_id
  editForm.tanggal = jadwal.tanggal ? jadwal.tanggal.split('T')[0] : ''
  editForm.kuota = jadwal.kuota
  editForm.clearErrors()
  showEditModal.value = true
}

function submitAdd() {
  form.post('/admin/jadwal', {
    onSuccess: () => {
      showAddModal.value = false
      form.reset()
    }
  })
}

function submitEdit() {
  editForm.put(`/admin/jadwal/${editForm.id}`, {
    onSuccess: () => {
      showEditModal.value = false
    }
  })
}

function destroy(id) {
  if (!confirm('Hapus jadwal ini? Pendaftaran yang terkait juga mungkin akan terhapus atau error.')) return
  router.delete(`/admin/jadwal/${id}`, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatDay(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long' })
}

function isPast(dateString) {
  if (!dateString) return false
  const date = new Date(dateString)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  return date < today
}
</script>

<template>
  <AppLayout title="Jadwal Sertifikasi">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <div>
        <p class="page-title">Jadwal Sertifikasi</p>
        <p class="page-sub">{{ jadwals.total }} jadwal terdaftar</p>
      </div>
      <button @click="openAddModal"
        style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;box-shadow:0 4px 12px rgba(245,158,11,.3);cursor:pointer">
        <i class="fa fa-plus"></i> Tambah Jadwal
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-calendar-alt" style="color:#f59e0b;margin-right:8px"></i>Daftar Jadwal Sertifikasi</h3>
      </div>
      
      <div style="overflow-x:auto">
        <div v-if="jadwals.data.length === 0" style="padding:60px;text-align:center">
          <div style="font-size:48px;margin-bottom:12px">📅</div>
          <p style="font-size:15px;color:#64748b;font-weight:500;margin-bottom:16px">Belum ada jadwal sertifikasi.</p>
          <button @click="openAddModal"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
            <i class="fa fa-plus"></i> Tambah Jadwal Pertama
          </button>
        </div>
        
        <table v-else style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Skema</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Tanggal</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Kuota</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Sisa Kuota</th>
              <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Pendaftar</th>
              <th style="padding:14px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="j in jadwals.data" :key="j.id" 
              style="border-bottom:1px solid #f1f5f9;transition:background .15s" 
              :style="isPast(j.tanggal) ? 'opacity:.6' : ''"
              @mouseover="$event.currentTarget.style.background='#fafbff'" 
              @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:14px 16px">
                <div style="display:flex;align-items:center;gap:8px">
                  <img v-if="j.skema?.logo" :src="`/files/${j.skema.logo}`" :alt="j.skema.nama_skema"
                    style="width:32px;height:32px;border-radius:8px;object-fit:contain;border:1px solid #e2e8f0;background:#f8fafc;padding:3px;flex-shrink:0">
                  <div v-else style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fa fa-certificate" style="color:#fff;font-size:12px"></i>
                  </div>
                  <div>
                    <div style="font-size:14px;font-weight:600;color:#0f172a">{{ j.skema?.nama_skema || '-' }}</div>
                    <div v-if="isPast(j.tanggal)" style="font-size:11px;color:#94a3b8">Sudah lewat</div>
                  </div>
                </div>
              </td>
              <td style="padding:14px 16px">
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ formatDate(j.tanggal) }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ formatDay(j.tanggal) }}</div>
              </td>
              <td style="padding:14px 16px;font-size:14px;font-weight:600;color:#475569">
                {{ j.kuota }} peserta
              </td>
              <td style="padding:14px 16px">
                <div style="display:flex;align-items:center;gap:8px">
                  <div style="flex:1;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden">
                    <div style="height:100%;transition:width .3s"
                      :style="`width:${j.kuota > 0 ? Math.round(((j.kuota - j.sisa_kuota) / j.kuota) * 100) : 0}%;background:${j.sisa_kuota <= 0 ? '#ef4444' : (j.sisa_kuota <= 3 ? '#f59e0b' : '#10b981')}`"></div>
                  </div>
                  <span :style="`font-size:13px;font-weight:700;white-space:nowrap;color:${j.sisa_kuota <= 0 ? '#ef4444' : (j.sisa_kuota <= 3 ? '#f59e0b' : '#10b981')}`">
                    {{ j.sisa_kuota }} sisa
                  </span>
                </div>
              </td>
              <td style="padding:14px 16px">
                <span style="background:#eef2ff;border:1px solid #c7d2fe;color:#4f46e5;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700">
                  {{ j.pendaftarans_count || 0 }} orang
                </span>
              </td>
              <td style="padding:14px 16px;text-align:center">
                <div style="display:flex;align-items:center;justify-content:center;gap:8px">
                  <button @click="openEditModal(j)"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit">
                    <i class="fa fa-edit"></i> Edit
                  </button>
                  <button @click="destroy(j.id)"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit">
                    <i class="fa fa-trash"></i> Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="jadwals.last_page > 1" style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
        <Link v-for="(link, index) in jadwals.links" :key="index"
          :href="link.url || ''"
          v-html="link.label"
          preserve-scroll
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>

    <!-- ADD MODAL -->
    <div v-if="showAddModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
            <i class="fa fa-calendar-alt" style="color:#f59e0b"></i> Tambah Jadwal Sertifikasi
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
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Pilih Skema Sertifikasi <span style="color:#ef4444">*</span>
            </label>
            <select v-model="form.skema_id" required style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;font-family:inherit;background:#fff">
              <option value="" disabled>Pilih salah satu skema...</option>
              <option v-for="s in skemas" :key="s.id" :value="s.id">{{ s.nama_skema }}</option>
            </select>
          </div>
          
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Tanggal Pelaksanaan <span style="color:#ef4444">*</span>
            </label>
            <input v-model="form.tanggal" type="date" required 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          
          <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Kuota Peserta <span style="color:#ef4444">*</span>
            </label>
            <input v-model="form.kuota" type="number" min="1" required 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          
          <div style="display:flex;gap:10px">
            <button type="button" @click="showAddModal = false" :disabled="form.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="form.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ form.processing ? 'Menyimpan...' : 'Simpan Jadwal' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:8px">
            <i class="fa fa-edit" style="color:#f59e0b"></i> Edit Jadwal Sertifikasi
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
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Skema Sertifikasi <span style="color:#ef4444">*</span>
            </label>
            <select v-model="editForm.skema_id" required style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;font-family:inherit;background:#fff">
              <option v-for="s in skemas" :key="s.id" :value="s.id">{{ s.nama_skema }}</option>
            </select>
          </div>
          
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Tanggal Pelaksanaan <span style="color:#ef4444">*</span>
            </label>
            <input v-model="editForm.tanggal" type="date" required 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          
          <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
              Kuota Peserta <span style="color:#ef4444">*</span>
            </label>
            <input v-model="editForm.kuota" type="number" min="1" required 
              style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          
          <div style="display:flex;gap:10px">
            <button type="button" @click="showEditModal = false" :disabled="editForm.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="editForm.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="editForm.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="editForm.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-save"></i> 
              {{ editForm.processing ? 'Menyimpan...' : 'Update Jadwal' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
