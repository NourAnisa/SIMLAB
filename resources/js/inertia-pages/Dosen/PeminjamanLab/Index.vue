<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjamans: Object,
  ruangans: Array,
})

const showAddModal = ref(false)

const form = useForm({
  ruangan: '',
  tanggal_pinjam: '',
  jam_mulai: '',
  jam_selesai: '',
  keperluan: '',
  surat_keterangan: null,
})

const fileRef = ref(null)

function onFileChange(e) {
  form.surat_keterangan = e.target.files[0]
}

function submitAdd() {
  form.post('/dosen/peminjaman-lab', {
    forceFormData: true,
    onSuccess: () => {
      showAddModal.value = false
      form.reset()
    }
  })
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function getStatusConfig(status) {
  const c = {
    'menunggu': { bg: '#fffbeb', border: '#fde68a', color: '#92400e', label: 'Menunggu' },
    'menunggu_kembali': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', label: 'Menunggu Verifikasi Kembali' },
    'diverifikasi': { bg: '#eff6ff', border: '#bfdbfe', color: '#1d4ed8', label: 'Diverifikasi' },
    'disetujui': { bg: '#f0fdf4', border: '#86efac', color: '#15803d', label: 'Disetujui' },
    'ditolak': { bg: '#fff1f2', border: '#fda4af', color: '#be123c', label: 'Ditolak' },
    'selesai': { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: 'Selesai' },
  }
  return c[status] || { bg: '#f8fafc', border: '#e2e8f0', color: '#64748b', label: status }
}
</script>

<template>
  <AppLayout title="Peminjaman Lab">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div>
        <p class="page-title">Peminjaman Lab</p>
        <p class="page-sub">Kelola pengajuan peminjaman ruangan laboratorium Anda.</p>
      </div>
      <button @click="showAddModal = true"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(245,158,11,.3)">
        <i class="fa fa-plus"></i> Ajukan Peminjaman
      </button>
    </div>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash?.error" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px">
      <i class="fa fa-times-circle"></i> {{ $page.props.flash.error }}
    </div>

    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Riwayat Peminjaman</h3>
        <span style="font-size:12px;color:#94a3b8">{{ peminjamans.total }} pengajuan</span>
      </div>
      <div style="overflow-x:auto">
        <div v-if="peminjamans.data.length === 0" style="padding:60px;text-align:center">
          <div style="font-size:48px;margin-bottom:12px">🏫</div>
          <p style="font-size:15px;color:#64748b;font-weight:500">Belum ada peminjaman lab.</p>
        </div>
        <table v-else style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:2px solid #e2e8f0">
              <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase">Ruangan</th>
              <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase">Tanggal</th>
              <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase">Waktu</th>
              <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase">Status</th>
              <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in peminjamans.data" :key="p.id"
              style="border-bottom:1px solid #f1f5f9;transition:background .15s"
              @mouseover="$event.currentTarget.style.background='#fafbff'"
              @mouseout="$event.currentTarget.style.background='transparent'">
              <td style="padding:12px 16px">
                <div style="font-size:14px;font-weight:600;color:#0f172a">{{ p.ruangan }}</div>
                <div style="font-size:12px;color:#94a3b8">{{ p.keperluan }}</div>
              </td>
              <td style="padding:12px 16px;font-size:13px;color:#475569">{{ formatDate(p.tanggal_pinjam) }}</td>
              <td style="padding:12px 16px;font-size:13px;color:#475569">{{ p.jam_mulai }} – {{ p.jam_selesai }}</td>
              <td style="padding:12px 16px">
                <span :style="`background:${getStatusConfig(p.status).bg};border:1px solid ${getStatusConfig(p.status).border};color:${getStatusConfig(p.status).color};border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700`">
                  {{ getStatusConfig(p.status).label }}
                </span>
              </td>
              <td style="padding:12px 16px;text-align:center">
                <Link :href="`/dosen/peminjaman-lab/${p.id}`"
                  style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700">
                  <i class="fa fa-eye"></i> Detail
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="peminjamans.last_page > 1" style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:center;gap:8px">
        <Link v-for="(link, index) in peminjamans.links" :key="index"
          :href="link.url || ''" v-html="link.label" preserve-scroll
          :style="`padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#f59e0b' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>

    <!-- ADD MODAL -->
    <div v-if="showAddModal" style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
      <div style="background:#fff;border-radius:20px;width:100%;max-width:540px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2);margin:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b">
            <i class="fa fa-door-open" style="color:#f59e0b;margin-right:8px"></i>Ajukan Peminjaman Lab
          </h3>
          <button @click="showAddModal = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
        </div>

        <div v-if="Object.keys(form.errors).length > 0" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px">
          <strong><i class="fa fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
          <ul style="margin-top:6px;padding-left:16px;margin-bottom:0">
            <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
          </ul>
        </div>

        <form @submit.prevent="submitAdd">
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Ruangan <span style="color:#ef4444">*</span></label>
            <select v-model="form.ruangan" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;background:#fff;font-family:inherit">
              <option value="">-- Pilih Ruangan --</option>
              <option v-for="r in ruangans" :key="r.id" :value="r.nama_ruangan">{{ r.nama_ruangan }}</option>
            </select>
          </div>
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Tanggal <span style="color:#ef4444">*</span></label>
            <input v-model="form.tanggal_pinjam" type="date" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
            <div>
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jam Mulai <span style="color:#ef4444">*</span></label>
              <input v-model="form.jam_mulai" type="time" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
            </div>
            <div>
              <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jam Selesai <span style="color:#ef4444">*</span></label>
              <input v-model="form.jam_selesai" type="time" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
            </div>
          </div>
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Keperluan <span style="color:#ef4444">*</span></label>
            <textarea v-model="form.keperluan" required rows="3" placeholder="Jelaskan keperluan peminjaman..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box;resize:vertical;font-family:inherit"></textarea>
          </div>
          <div style="margin-bottom:24px">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Surat Keterangan <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
            <input ref="fileRef" type="file" @change="onFileChange" accept=".pdf,.jpg,.jpeg,.png"
              style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;box-sizing:border-box">
          </div>
          <div style="display:flex;gap:10px">
            <button type="button" @click="showAddModal = false" :disabled="form.processing"
              style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
            <button type="submit" :disabled="form.processing"
              style="flex:2;padding:11px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px"
              :style="form.processing ? 'opacity:0.7;cursor:not-allowed' : ''">
              <i v-if="form.processing" class="fa fa-spinner fa-spin"></i>
              <i v-else class="fa fa-paper-plane"></i>
              {{ form.processing ? 'Mengirim...' : 'Ajukan Peminjaman' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
