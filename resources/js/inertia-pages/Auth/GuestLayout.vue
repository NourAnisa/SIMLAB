<script setup>
/**
 * GuestLayout untuk Inertia — versi adaptasi dari layouts/GuestLayout.vue
 * Perbedaan utama: RouterLink diganti dengan Link dari @inertiajs/vue3
 */
import { ref, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const siteSetting = ref({})

onMounted(() => {
  const page = usePage()
  siteSetting.value = page.props.siteSetting || {}
  if (siteSetting.value.tema_warna === 'merah') {
    document.body.classList.add('theme-merah')
  } else {
    document.body.classList.remove('theme-merah')
  }
})
</script>

<template>
  <div class="guest-body">
    <div class="auth-wrap">
        <div class="auth-brand">
            <Link href="/" style="text-decoration:none">
                <img v-if="siteSetting.logo" :src="`/storage-public/${siteSetting.logo}`" alt="Logo" style="width:56px;height:56px;border-radius:14px;object-fit:contain;background:linear-gradient(135deg,var(--color-primary),var(--color-secondary));padding:6px;display:block;margin:0 auto 12px">
                <div v-else class="auth-logo">SL</div>
                <div class="auth-brand-title">{{ siteSetting.nama_website || 'SIMLAB STMIK' }}</div>
                <div class="auth-brand-sub">{{ siteSetting.nama_kampus || 'Sistem Informasi Manajemen Lab' }}</div>
            </Link>
        </div>
        <div class="auth-card">
            <slot />
        </div>
    </div>
  </div>
</template>

<style>
/* CSS diambil dari GuestLayout.vue asli — identik */
:root {
  --color-primary: #6366f1;
  --color-primary-rgb: 99,102,241;
  --color-secondary: #8b5cf6;
  --color-secondary-rgb: 139,92,246;
}
body.theme-merah {
  --color-primary: #ef4444;
  --color-primary-rgb: 239,68,68;
  --color-secondary: #f97316;
  --color-secondary-rgb: 249,115,22;
}
</style>

<style scoped>
.guest-body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    position: relative;
    width: 100vw;
}
.guest-body::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 30% 50%, rgba(var(--color-primary-rgb), .3) 0%, transparent 60%), radial-gradient(ellipse at 80% 20%, rgba(var(--color-secondary-rgb), .25) 0%, transparent 50%);
    pointer-events: none
}
.auth-wrap { width: 100%; max-width: 480px; position: relative; z-index: 1 }
.auth-brand { text-align: center; margin-bottom: 32px }
.auth-logo {
    width: 56px; height: 56px; border-radius: 14px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 22px; font-weight: 800; margin: 0 auto 12px
}
.auth-brand-title { color: #fff; font-size: 20px; font-weight: 800; line-height: 1.2 }
.auth-brand-sub { color: rgba(255,255,255,.55); font-size: 13px; margin-top: 2px }
.auth-card {
    background: #fff; border-radius: 24px; padding: 40px 36px;
    box-shadow: 0 24px 60px rgba(0,0,0,.25)
}
</style>
