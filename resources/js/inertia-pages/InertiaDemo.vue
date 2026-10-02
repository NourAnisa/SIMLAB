<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-6">
    <div class="max-w-2xl w-full">

      <!-- Header Badge -->
      <div class="flex justify-center mb-6">
        <span class="inline-flex items-center gap-2 bg-blue-500/20 border border-blue-400/30 text-blue-300 text-sm font-semibold px-4 py-1.5 rounded-full backdrop-blur-sm">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          Inertia.js Aktif
        </span>
      </div>

      <!-- Main Card -->
      <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
        <h1 class="text-3xl font-extrabold text-white text-center mb-2 tracking-tight">
          Inertia.js Demo
        </h1>
        <p class="text-blue-300 text-center text-sm mb-8">
          Halaman ini dirender oleh Inertia — data dikirim langsung dari Laravel Controller sebagai props Vue.
        </p>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
          <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-xs text-blue-400 font-semibold uppercase tracking-widest mb-1">Aplikasi</p>
            <p class="text-white font-bold text-lg">{{ appName }}</p>
          </div>
          <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-xs text-blue-400 font-semibold uppercase tracking-widest mb-1">Versi Laravel</p>
            <p class="text-white font-bold text-lg">{{ laravelVersion }}</p>
          </div>
          <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-xs text-blue-400 font-semibold uppercase tracking-widest mb-1">Versi PHP</p>
            <p class="text-white font-bold text-lg">{{ phpVersion }}</p>
          </div>
          <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-xs text-blue-400 font-semibold uppercase tracking-widest mb-1">Status SPA Utama</p>
            <p class="text-green-400 font-bold text-lg flex items-center gap-2">
              <span class="w-2 h-2 bg-green-400 rounded-full inline-block"></span>
              Tidak Terganggu
            </p>
          </div>
        </div>

        <!-- How It Works -->
        <div class="bg-blue-500/10 border border-blue-400/20 rounded-2xl p-5 mb-8">
          <h2 class="text-blue-300 font-bold text-sm uppercase tracking-widest mb-3">Cara Kerja Halaman Ini</h2>
          <ul class="space-y-2">
            <li class="flex items-start gap-3 text-sm text-slate-300">
              <span class="text-blue-400 mt-0.5">①</span>
              Browser mengakses <code class="bg-white/10 px-1.5 py-0.5 rounded text-blue-300 font-mono">/inertia-demo</code>
            </li>
            <li class="flex items-start gap-3 text-sm text-slate-300">
              <span class="text-blue-400 mt-0.5">②</span>
              Laravel memanggil <code class="bg-white/10 px-1.5 py-0.5 rounded text-blue-300 font-mono">InertiaDemoController</code>
            </li>
            <li class="flex items-start gap-3 text-sm text-slate-300">
              <span class="text-blue-400 mt-0.5">③</span>
              Controller mengirim data (props) via <code class="bg-white/10 px-1.5 py-0.5 rounded text-blue-300 font-mono">Inertia::render()</code>
            </li>
            <li class="flex items-start gap-3 text-sm text-slate-300">
              <span class="text-blue-400 mt-0.5">④</span>
              Vue merender komponen ini dengan data dari server — <strong class="text-white">tanpa Axios!</strong>
            </li>
          </ul>
        </div>

        <!-- Props Display -->
        <div class="bg-slate-900/60 rounded-2xl p-5 mb-8">
          <p class="text-xs text-slate-400 font-semibold uppercase tracking-widest mb-3">Props yang Diterima dari Server</p>
          <pre class="text-green-400 font-mono text-sm overflow-auto">{{ JSON.stringify({ appName, laravelVersion, phpVersion, message }, null, 2) }}</pre>
        </div>

        <!-- Message from Server -->
        <div class="text-center mb-8">
          <p class="text-slate-300 italic text-sm">"{{ message }}"</p>
        </div>

        <!-- Back Button -->
        <div class="flex justify-center">
          <a
            href="/"
            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Aplikasi Utama
          </a>
        </div>
      </div>

      <!-- Footer note -->
      <p class="text-center text-slate-500 text-xs mt-6">
        SPA Vue Router + Pinia tetap berjalan normal di route lain · Inertia hanya aktif di <code>/inertia-demo</code>
      </p>

    </div>
  </div>
</template>

<script setup>
defineProps({
  appName: {
    type: String,
    default: 'SILAB'
  },
  laravelVersion: {
    type: String,
    default: '-'
  },
  phpVersion: {
    type: String,
    default: '-'
  },
  message: {
    type: String,
    default: 'Inertia.js berhasil terpasang!'
  }
})
</script>
