<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  programStudis: Array
})

const showModal = ref(false)
const isEditing = ref(false)

const form = useForm({
  id: null,
  nama_program_studi: '',
  jenjang: ''
})

function openAddModal() {
  form.reset()
  form.clearErrors()
  isEditing.value = false
  showModal.value = true
}

function openEditModal(ps) {
  form.id = ps.id
  form.nama_program_studi = ps.nama_program_studi
  form.jenjang = ps.jenjang || ''
  form.clearErrors()
  isEditing.value = true
  showModal.value = true
}

function submitForm() {
  if (isEditing.value) {
    form.put(`/admin/program-studi/${form.id}`, {
      onSuccess: () => showModal.value = false
    })
  } else {
    form.post('/admin/program-studi', {
      onSuccess: () => {
        showModal.value = false
        form.reset()
      }
    })
  }
}

function hapusProgramStudi(id) {
  if (!confirm('Hapus program studi ini?')) return
  router.delete(`/admin/program-studi/${id}`, {
    preserveScroll: true
  })
}
</script>

<template>
  <AppLayout title="Manajemen Program Studi">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
      <div>
        <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0">Manajemen Program Studi</h2>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0">Kelola daftar program studi / jurusan yang tersedia di kampus.</p>
      </div>
      <button @click="openAddModal"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit">
        <i class="fa fa-plus"></i> Tambah Program Studi
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="card-body" style="padding:0">
        <div v-if="programStudis.length === 0" style="padding:48px;text-align:center">
          <div style="font-size:40px;margin-bottom:12px">🎓</div>
          <p style="font-size:14px;color:#64748b;margin:0">Belum ada data program studi. Klik <strong>Tambah Program Studi</strong> untuk mulai.</p>
        </div>
        
        <div v-else style="overflow-x:auto">
          <table style="width:100%;border-collapse:collapse">
            <thead>
              <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">No</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Jenjang</th>
                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Nama Program Studi</th>
                <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(ps, idx) in programStudis" :key="ps.id" style="border-bottom:1px solid #f1f5f9;transition:background .15s" @mouseover="$event.currentTarget.style.background='#fafbff'" @mouseout="$event.currentTarget.style.background='transparent'">
                <td style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600">{{ idx + 1 }}</td>
                <td style="padding:12px 16px">
                  <span v-if="ps.jenjang" style="background:#eff6ff;color:#1d4ed8;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700">{{ ps.jenjang }}</span>
                  <span v-else style="color:#cbd5e1;font-size:11px;font-style:italic">-</span>
                </td>
                <td style="padding:12px 16px">
                  <div style="font-size:13px;font-weight:700;color:#0f172a">{{ ps.nama_program_studi }}</div>
                </td>
                <td style="padding:12px 16px;text-align:center">
                  <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <button @click="openEditModal(ps)"
                      style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit">
                      <i class="fa fa-edit"></i> Edit
                    </button>
                    <button @click="hapusProgramStudi(ps.id)"
                      style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#fff1f2;border:1px solid #fda4af;color:#be123c;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:flex;align-items:center;justify-content:center;padding:20px" @click.self="showModal = false">
      <div style="background:#fff;border-radius:16px;width:100%;max-width:450px;box-shadow:0 25px 60px rgba(0,0,0,.2)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #f1f5f9">
          <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0">
            <i class="fa" :class="isEditing ? 'fa-edit' : 'fa-plus'" style="color:#3b82f6;margin-right:8px"></i>
            {{ isEditing ? 'Edit Program Studi' : 'Tambah Program Studi' }}
          </h3>
          <button @click="showModal = false" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:4px"><i class="fa fa-times"></i></button>
        </div>
        <div style="padding:24px">
          <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px">
            <div v-for="(errs, field) in form.errors" :key="field">
              <div>• {{ errs }}</div>
            </div>
          </div>
          <form @submit.prevent="submitForm">
            <div style="margin-bottom:14px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Nama Program Studi <span style="color:#ef4444">*</span></label>
              <input v-model="form.nama_program_studi" type="text" placeholder="cth: Teknik Informatika" required
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box"
                @focus="$event.currentTarget.style.borderColor='#3b82f6'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
            </div>
            <div style="margin-bottom:20px">
              <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">Jenjang <span style="color:#94a3b8">(Opsional)</span></label>
              <input v-model="form.jenjang" type="text" placeholder="cth: S1"
                style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box"
                @focus="$event.currentTarget.style.borderColor='#3b82f6'" @blur="$event.currentTarget.style.borderColor='#e2e8f0'">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
              <button type="button" @click="showModal = false"
                style="padding:10px 20px;background:#f1f5f9;color:#64748b;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit">Batal</button>
              <button type="submit" :disabled="form.processing"
                style="padding:10px 24px;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px"
                :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
                <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-save"></i> 
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
