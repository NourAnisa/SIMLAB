<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

defineProps({
    jadwals: Object
})

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const options = { day: 'numeric', month: 'short', year: 'numeric' }
    return date.toLocaleDateString('id-ID', options)
}
</script>

<template>
    <AppLayout title="Sertifikasi Kompetensi">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
            <p class="page-title" style="margin-bottom:0">Sertifikasi Kompetensi</p>
            <Link href="/mahasiswa/sertifikasi/riwayat" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#f1f5f9;color:#475569;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:all .2s">
                <i class="fa fa-history"></i> Riwayat Pendaftaran
            </Link>
        </div>
        <p class="page-sub">Pilih jadwal ujian sertifikasi yang tersedia dan daftarkan diri Anda.</p>

        <div v-if="jadwals.data.length === 0" class="card" style="text-align:center;padding:60px 24px">
            <div style="font-size:52px;margin-bottom:16px">📅</div>
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Belum Ada Jadwal</h3>
            <p style="color:#64748b;font-size:14px">Tidak ada jadwal sertifikasi yang tersedia saat ini. Pantau terus untuk informasi terbaru.</p>
        </div>
        <div v-else style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px">
            <div v-for="jadwal in jadwals.data" :key="jadwal.id" class="card" style="overflow:visible">
                <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:12px 12px 0 0;padding:20px 24px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between">
                        <div>
                            <div style="font-size:11px;color:rgba(255,255,255,.65);font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:4px">Skema Sertifikasi</div>
                            <h3 style="color:#fff;font-size:16px;font-weight:700;line-height:1.3">{{ jadwal.skema.nama_skema }}</h3>
                        </div>
                        <div style="background:rgba(255,255,255,.15);border-radius:8px;padding:6px 12px;font-size:12px;color:#fff;font-weight:600;white-space:nowrap">
                            #{{ jadwal.id }}
                        </div>
                    </div>
                </div>
                <div class="card-body" style="padding:20px 24px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                        <div style="background:#f8fafc;border-radius:10px;padding:12px">
                            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Tanggal Ujian</div>
                            <div style="font-size:14px;font-weight:700;color:#0f172a">
                                <i class="fa fa-calendar" style="color:#4f46e5;margin-right:6px"></i>
                                {{ formatDate(jadwal.tanggal) }}
                            </div>
                        </div>
                        <div style="background:#f8fafc;border-radius:10px;padding:12px">
                            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Sisa Kuota</div>
                            <div :style="{ fontSize: '14px', fontWeight: '700', color: jadwal.sisa_kuota > 5 ? '#16a34a' : (jadwal.sisa_kuota > 0 ? '#d97706' : '#dc2626') }">
                                <i class="fa fa-users" style="margin-right:6px"></i>
                                {{ jadwal.sisa_kuota }}/{{ jadwal.kuota }} tempat
                            </div>
                        </div>
                    </div>

                    <p style="font-size:13px;color:#64748b;line-height:1.6;margin-bottom:16px">{{ jadwal.skema.deskripsi.substring(0, 100) }}{{ jadwal.skema.deskripsi.length > 100 ? '...' : '' }}</p>

                    <div v-if="jadwal.sudah_daftar" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#dcfce7;border-radius:8px;color:#16a34a;font-size:13px;font-weight:600">
                        <i class="fa fa-check-circle"></i> Anda sudah mendaftar
                    </div>
                    <div v-else-if="jadwal.sisa_kuota <= 0" style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:#fee2e2;border-radius:8px;color:#dc2626;font-size:13px;font-weight:600">
                        <i class="fa fa-times-circle"></i> Kuota penuh
                    </div>
                    <Link v-else :href="`/mahasiswa/sertifikasi/${jadwal.id}/daftar`" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border-radius:10px;text-decoration:none;font-size:14px;font-weight:700;transition:all .3s;box-shadow:0 4px 12px rgba(79,70,229,.3)">
                        <i class="fa fa-paper-plane"></i> Daftar Sekarang
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
