<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import GuestLayout from './GuestLayout.vue'

const props = defineProps({
  token: String,
  email: String,
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post('/reset-password', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <GuestLayout>
    <h2 class="auth-card-title">Reset Password 🔑</h2>
    <p class="auth-card-sub">Silakan masukkan password baru Anda.</p>

    <form @submit.prevent="submit">
        <div class="form-group">
            <label class="form-label" for="email">Alamat Email</label>
            <input id="email" class="form-input" :class="{ 'is-invalid': form.errors.email }"
                type="email" v-model="form.email" required readonly>
            <div v-if="form.errors.email" class="form-error">{{ form.errors.email }}</div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password Baru</label>
            <input id="password" class="form-input" :class="{ 'is-invalid': form.errors.password }"
                type="password" v-model="form.password" required autofocus
                placeholder="Min. 8 karakter">
            <div v-if="form.errors.password" class="form-error">{{ form.errors.password }}</div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" class="form-input"
                type="password" v-model="form.password_confirmation" required
                placeholder="Ulangi password baru">
        </div>

        <button type="submit" class="btn-auth" :disabled="form.processing">
            <i class="fa fa-lock" style="margin-right:8px"></i>
            {{ form.processing ? 'Memproses...' : 'Reset Password' }}
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-footer">
        <Link href="/login">Kembali ke halaman masuk</Link>
    </div>
  </GuestLayout>
</template>

<style scoped>
.auth-card-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px }
.auth-card-sub { font-size: 14px; color: #64748b; margin-bottom: 28px }
.form-group { margin-bottom: 18px }
.form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px }
.form-input {
    width: 100%; padding: 11px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: 14px; font-family: inherit; color: #1e293b; background: #f8fafc;
    transition: all .2s; outline: none; box-sizing: border-box;
}
.form-input:focus { border-color: var(--color-primary); background: #fff; box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb),.1) }
.form-input.is-invalid { border-color: #ef4444; background: #fff9f9 }
.form-input[readonly] { opacity: .7; cursor: not-allowed }
.form-error { color: #ef4444; font-size: 12px; margin-top: 4px }
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
.auth-footer a { color: var(--color-primary); font-weight: 600; text-decoration: none }
.auth-footer a:hover { text-decoration: underline }
.auth-divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0 }
</style>
