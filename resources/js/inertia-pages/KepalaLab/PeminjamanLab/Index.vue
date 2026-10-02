<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const props = defineProps({
  peminjamans: Object
})

function setujui(id) {
  if (!confirm('Setujui peminjaman ini?')) return
  router.post(`/kepala_lab/peminjaman-lab/${id}/setujui`, {}, {
    preserveScroll: true
  })
}

function tolak(id) {
  if (!confirm('Tolak permohonan ini?')) return
  router.post(`/kepala_lab/peminjaman-lab/${id}/tolak`, {}, {
    preserveScroll: true
  })
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatTime(timeString) {
  if (!timeString) return ''
  return timeString.substring(0, 5)
}

function truncate(str, limit) {
  if (!str) return ''
  return str.length > limit ? str.substring(0, limit - 1) + '...' : str
}
</script>

<template>
  <AppLayout title="Peminjaman Lab">
    <p class="page-title">Persetujuan Peminjaman Lab</p>
    <p class="page-sub">Tinjau dan setujui permohonan peminjaman yang telah diverifikasi admin.</p>

    <div v-if="$page.props.flash?.success" style="background:#dcfce7;border:1px solid #86efac;color:#16a34a;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:14px">
      <i class="fa fa-check-circle"></i> {{ $page.props.flash.success }}
    </div>

    <div>
      <div v-if="peminjamans.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
        <div style="font-size:48px;margin-bottom:16px">✅</div>
        <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Tidak Ada Antrean</h3>
        <p style="color:#64748b;font-size:14px">Semua permohonan peminjaman sudah diproses atau belum ada yang diverifikasi admin.</p>
      </div>

      <div v-else style="display:flex;flex-direction:column;gap:14px">
        <div v-for="p in peminjamans.data" :key="p.id" class="card" style="border-left:4px solid #3b82f6">
          <div class="card-body" style="padding:18px 24px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                  <img v-if="p.user?.foto_profil" :src="`/files/${p.user.foto_profil}`" alt="foto" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0">
                  <div v-else style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span style="color:#fff;font-weight:700;font-size:15px">{{ p.user?.name ? p.user.name.substring(0,1).toUpperCase() : 'U' }}</span>
                  </div>
                  <div>
                    <div style="font-size:15px;font-weight:700;color:#0f172a">{{ p.user?.name }}</div>
                    <div style="font-size:12px;color:#94a3b8">{{ p.user?.nim_nip || p.user?.email }} · {{ p.user?.jurusan || 'Mahasiswa' }}</div>
                  </div>
                </div>
                
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:10px">
                  <div style="background:#f8fafc;border-radius:8px;padding:10px 14px">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:3px">RUANGAN</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a">{{ p.ruangan }}</div>
                  </div>
                  <div style="background:#f8fafc;border-radius:8px;padding:10px 14px">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:3px">TANGGAL</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a">{{ formatDate(p.tanggal_pinjam) }}</div>
                  </div>
                  <div style="background:#f8fafc;border-radius:8px;padding:10px 14px">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:3px">WAKTU</div>
                    <div style="font-size:14px;font-weight:700;color:#0f172a">{{ formatTime(p.jam_mulai) }} – {{ formatTime(p.jam_selesai) }}</div>
                  </div>
                </div>
                
                <p style="font-size:13px;color:#64748b;line-height:1.5"><strong>Keperluan:</strong> {{ truncate(p.keperluan, 120) }}</p>
              </div>
              
              <div style="display:flex;flex-direction:column;gap:8px;flex-shrink:0;min-width:140px">
                <Link :href="`/kepala_lab/peminjaman-lab/${p.id}`"
                  style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:#f8fafc;border:1.5px solid #e2e8f0;color:#374151;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
                  <i class="fa fa-eye"></i> Detail
                </Link>
                
                <button @click="setujui(p.id)"
                  style="width:100%;padding:9px 14px;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px">
                  <i class="fa fa-check"></i> Setujui
                </button>
                
                <button @click="tolak(p.id)"
                  style="width:100%;padding:9px 14px;background:#fff1f2;border:1.5px solid #fda4af;color:#be123c;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px">
                  <i class="fa fa-times"></i> Tolak
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="peminjamans.last_page > 1" style="display:flex;justify-content:center;gap:8px;margin-top:24px">
        <Link v-for="(link, index) in peminjamans.links" :key="index"
          :href="link.url || ''"
          v-html="link.label"
          preserve-scroll
          :style="`padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #e2e8f0;display:inline-block;text-decoration:none;pointer-events:${link.url && !link.active ? 'auto' : 'none'};opacity:${link.url ? '1' : '0.5'};background:${link.active ? '#4f46e5' : '#fff'};color:${link.active ? '#fff' : '#64748b'}`">
        </Link>
      </div>
    </div>
  </AppLayout>
</template>
