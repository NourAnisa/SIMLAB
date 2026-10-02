<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import GuestLayout from './GuestLayout.vue'

defineProps({
  status: String,
})

const form = useForm({})

function resendVerification() {
  form.post('/email/verification-notification')
}

function logout() {
  router.post('/logout')
}
</script>

<template>
  <GuestLayout>
    <h2 class="auth-card-title">Verifikasi Email 📧</h2>
    <p class="auth-card-sub">
        Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda
        dengan mengklik tautan yang telah kami kirimkan.
    </p>

    <div v-if="status === 'verification-link-sent'" class="session-status">
        Tautan verifikasi baru telah dikirim ke alamat email Anda.
    </div>

    <form @submit.prevent="resendVerification">
        <button type="submit" class="btn-auth" :disabled="form.processing">
            <i class="fa fa-envelope" style="margin-right:8px"></i>
            {{ form.processing ? 'Mengirim...' : 'Kirim Ulang Email Verifikasi' }}
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-footer">
        <button @click="logout" class="logout-link">Keluar dari akun</button>
    </div>
  </GuestLayout>
</template>

<style scoped>
.auth-card-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px }
.auth-card-sub { font-size: 14px; color: #64748b; margin-bottom: 28px }
.btn-auth {
    width: 100%; padding: 13px; border-radius: 10px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    color: #fff; font-size: 15px; font-weight: 700; font-family: inherit;
    border: none; cursor: pointer; transition: all .3s;
    box-shadow: 0 6px 20px rgba(var(--color-primary-rgb),.35); margin-top: 8px
}
.btn-auth:hover { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(var(--color-primary-rgb),.45) }
.btn-auth:disabled { opacity: .7; cursor: not-allowed; transform: none }
.auth-footer { text-align: center; margin-top: 20px; font-size: 13px; color: #64748b }
.auth-divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0 }
.logout-link {
    background: none; border: none; color: var(--color-primary);
    font-weight: 600; font-size: 13px; cursor: pointer; text-decoration: none;
    font-family: inherit;
}
.logout-link:hover { text-decoration: underline }
.session-status {
    background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3);
    color: #059669; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px
}
</style>
