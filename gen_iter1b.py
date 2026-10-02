
import textwrap

section = textwrap.dedent("""\
**B. Code 1 - Pseudocode**

**Tabel 3.9 Pseudocode Autentikasi**

```
FUNCTION authenticate(email, password):
  VALIDASI: email harus format valid, password wajib diisi
  cari user berdasarkan email di tabel users
  IF user tidak ditemukan OR password tidak cocok THEN
    RETURN error "Email atau password salah"
  END IF
  IF email belum diverifikasi THEN
    RETURN error "Email belum diverifikasi"
  END IF
  buat sesi login untuk user
  IF role == "admin"      THEN redirect /admin/dashboard
  IF role == "kepala_lab" THEN redirect /kepala-lab/dashboard
  IF role == "dosen"      THEN redirect /dosen/dashboard
  ELSE redirect /mahasiswa/dashboard
END FUNCTION

FUNCTION register(name, nim, email, password, role, program_studi_id):
  ambil domain_mahasiswa dan domain_pegawai dari site_settings
  VALIDASI: semua field wajib diisi
  IF domain email tidak sesuai konfigurasi institusi THEN
    RETURN error "Email tidak sesuai domain institusi"
  END IF
  buat record User baru dengan data tervalidasi
  kirim email verifikasi ke alamat pendaftar
  redirect ke halaman konfirmasi verifikasi email
END FUNCTION
```

**Tabel 3.10 Penjelasan Pseudocode Iterasi 1**

| Proses | Penjelasan (Output) |
|--------|---------------------|
| Validasi login | Sistem memeriksa kecocokan email dan password. Jika tidak cocok, pesan error ditampilkan. |
| Redirect berbasis peran | Setelah login berhasil, pengguna diarahkan ke dashboard sesuai role masing-masing. |
| Validasi domain registrasi | Sistem memeriksa domain email terhadap konfigurasi domain di site_settings. |
| Pembuatan akun baru | Data disimpan ke tabel users dengan status email belum diverifikasi. |
| Pengiriman email verifikasi | Sistem otomatis mengirimkan email verifikasi ke alamat yang terdaftar. |

> *Gambar 3.6 Halaman Login SIMLAB*

> *Gambar 3.7 Halaman Dashboard Admin*

### 3.4.1.4 Retrospective 1

Seluruh user story (US-01 hingga US-05) berhasil diimplementasikan. Fondasi sistem berhasil dibangun mencakup halaman company profile publik, autentikasi berbasis peran, dan dashboard untuk semua jenis pengguna.

**Tabel 3.11 Hasil Retrospective 1**

| Faktor Keberhasilan | Faktor Hambatan |
|---------------------|-----------------|
| Fitur bersifat CRUD standar sehingga implementasi efisien dan konsisten. | Konfigurasi awal arsitektur VILT memerlukan waktu penyesuaian tambahan. |
| Eloquent ORM mempermudah pembangunan skema basis data secara terstruktur. | Validasi domain email dinamis memerlukan rekayasa tambahan via site_settings. |

---

""")

with open(r'c:\xampp\htdocs\SIMLAB\bab3_4_metode_implementasi.md', 'a', encoding='utf-8') as f:
    f.write(section)

print("OK - Iterasi 1 pseudocode + retrospective done")
