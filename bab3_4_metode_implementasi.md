# 3.4 Metode Implementasi

Metode implementasi menggunakan **Personal Extreme Programming (PXP)**. Setiap iterasi terdiri dari tahapan: *Iteration Initialization*, *Design*, *Implementation* (Unit Testing + Code), dan *Retrospective*.

---

## 3.4.1 Iterasi 1 - Core System dan Company Profile

Iterasi pertama membangun fondasi sistem mencakup: (1) halaman company profile laboratorium yang dapat diakses publik, (2) modul autentikasi berbasis peran, (3) halaman dashboard untuk setiap peran pengguna, serta (4) fitur admin untuk mengelola konten company profile. Iterasi ini merupakan prasyarat sebelum modul layanan dikembangkan pada iterasi berikutnya.

### 3.4.1.1 Iteration Initialization 1

Pada tahap ini ditentukan user story yang dikerjakan dan task yang perlu diselesaikan dalam iterasi pertama.

**Tabel 3.6 User Story Iterasi 1**

| Kode | As a | I Want | So That |
|------|------|--------|---------|
| US-01 | Pengguna | Saya ingin dapat melihat Company Profile Lab | Saya dapat mengetahui informasi laboratorium |
| US-02 | Pengguna | Saya ingin dapat melakukan login | Saya dapat mengakses fitur sesuai peran saya |
| US-03 | Pengguna | Saya ingin dapat masuk ke halaman dashboard | Saya dapat menggunakan fitur yang tersedia |
| US-04 | Pengguna | Saya ingin dapat melakukan logout | Saya dapat keluar dari sistem dengan aman |
| US-05 | Admin | Saya ingin menambahkan informasi company profile | Saya dapat mengelola informasi yang ditampilkan ke publik |

**Tabel 3.7 Iteration Initialization 1**

| Kode | User Story | Task | Story Point |
|------|------------|------|-------------|
| US-01 | Melihat Company Profile Lab | Membuat halaman beranda, profil lab, dan berita yang dapat diakses publik tanpa login | 3 |
| US-02 | Melakukan login | Membuat halaman login dan logika autentikasi berbasis peran dengan validasi domain email | 1 |
| US-03 | Masuk ke halaman dashboard | Membuat dashboard untuk masing-masing peran: Admin, Kepala Lab, Mahasiswa, dan Dosen | 5 |
| US-04 | Melakukan logout | Mengimplementasikan fungsi logout dan penghapusan sesi pengguna | 3 |
| US-05 | Menambahkan informasi company profile | Membuat halaman CRUD company profile untuk admin (berita, profil lab, pengumuman) | 5 |
| **Total** | | | **17** |

### 3.4.1.2 Design 1

Tahap perancangan mencakup pembuatan ERD dan mockup antarmuka pengguna.

**A. Entity Relationship Diagram 1**

ERD iterasi pertama menggambarkan dua entitas inti: users dengan atribut id, name, email, password, role, nim, program_studi_id; dan site_settings dengan atribut id, key, value. Atribut 
ole membedakan hak akses: mahasiswa, dosen, admin, kepala_lab.

> *Gambar 3.1 ERD Iterasi 1*

**B. User Interface 1**

Rancangan antarmuka mencakup halaman beranda publik, halaman login, dan dashboard per peran pengguna.

> *Gambar 3.2 Mockup Antarmuka Halaman Beranda/Company Profile*

> *Gambar 3.3 Mockup Antarmuka Login*

> *Gambar 3.4 Mockup Antarmuka Dashboard Admin*

### 3.4.1.3 Implementation 1

Implementasi dimulai dengan unit testing sebelum penulisan kode utama (test-first development).

**A. Unit Testing 1**

Pengujian dilakukan terhadap AuthenticatedSessionController untuk memverifikasi: tampilan halaman login, penolakan kredensial salah, redirect ke dashboard sesuai peran, dan logout.

**Tabel 3.8 Kode Unit Testing 1 - Autentikasi**

`php
// tests/Unit/Controllers/Auth/AuthenticatedSessionControllerTest.php
<?php
namespace Tests\Unit\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_the_login_page()
    {
         = ->get(route('login'));
        ->assertStatus(200);
    }

    public function test_it_fails_login_with_wrong_credentials()
    {
         = ->post(route('login'), [
            'email'    => 'salah@stmik.ac.id',
            'password' => 'wrongpassword',
        ]);
        ->assertSessionHasErrors(['email']);
    }

    public function test_it_logs_in_as_admin_and_redirects_to_dashboard()
    {
         = User::factory()->create([
            'email'    => 'admin@stmik.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);
         = ->post(route('login'), [
            'email'    => 'admin@stmik.ac.id',
            'password' => 'password123',
        ]);
        ->assertRedirect('/dashboard');
        ->assertAuthenticatedAs();
        ->get('/dashboard')->assertRedirect('/admin/dashboard');
    }

    public function test_it_logs_out_and_redirects_to_login()
    {
         = User::factory()->create();
        ->actingAs();
         = ->post(route('logout'));
        ->assertRedirect('/');
        ->assertGuest();
    }
}
`

> *Gambar 3.5 Hasil Unit Testing 1 (4 tests, 4 passed)*

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

## 3.4.2 Iterasi 2 - Modul Sertifikasi Kompetensi

Iterasi kedua mengembangkan modul Sertifikasi Kompetensi secara lengkap: manajemen jadwal oleh admin, pendaftaran dan upload berkas oleh mahasiswa, verifikasi dan pemberian token ujian oleh admin, pemeriksaan hasil oleh Kepala Lab, serta pencetakan sertifikat oleh mahasiswa yang lulus.

### 3.4.2.1 Iteration Initialization 2

**Tabel 3.12 User Story Iterasi 2**

| Kode | As a | I Want | So That |
|------|------|--------|---------|
| US-06 | Admin | Saya ingin membuat jadwal sertifikasi | Saya dapat mengatur waktu dan kuota peserta ujian |
| US-07 | Mahasiswa | Saya ingin melihat dan mendaftar sertifikasi | Saya dapat mengikuti ujian sertifikasi yang tersedia |
| US-08 | Admin | Saya ingin memverifikasi pendaftaran sertifikasi | Saya dapat memeriksa kelengkapan berkas peserta |
| US-09 | Mahasiswa | Saya ingin mengikuti sertifikasi | Saya dapat menggunakan token ujian yang diberikan admin |
| US-10 | Kepala Lab | Saya ingin memeriksa hasil sertifikasi | Saya dapat menentukan kelulusan peserta ujian |
| US-11 | Mahasiswa | Saya ingin mencetak sertifikat | Saya dapat memiliki bukti kelulusan sertifikasi secara resmi |

**Tabel 3.13 Iteration Initialization 2**

| Kode | User Story | Task | Story Point |
|------|------------|------|-------------|
| US-06 | Membuat jadwal sertifikasi | Membuat halaman CRUD jadwal sertifikasi beserta manajemen kuota peserta | 5 |
| US-07 | Melihat dan mendaftar sertifikasi | Membuat halaman daftar jadwal, form pendaftaran dengan upload berkas, dan halaman riwayat | 3 |
| US-08 | Memverifikasi pendaftaran | Membuat halaman admin untuk verifikasi berkas dan generate token ujian otomatis | 3 |
| US-09 | Mengikuti sertifikasi | Mengimplementasikan alur penggunaan token ujian oleh mahasiswa pada hari ujian | 5 |
| US-10 | Memeriksa hasil sertifikasi | Membuat halaman Kepala Lab untuk pencatatan hasil lulus/tidak lulus | 3 |
| US-11 | Mencetak sertifikat | Membuat fitur cetak sertifikat berbasis Blade PDF untuk mahasiswa yang lulus | 3 |
| **Total** | | | **22** |

### 3.4.2.2 Design 2

**A. Entity Relationship Diagram 2**

ERD iterasi kedua menambahkan tiga entitas: `skema_sertifikasis` (id, nama_skema, deskripsi, logo), `jadwal_sertifikasis` (id, skema_id, tanggal, jam_mulai, jam_selesai, kuota, lokasi), dan `pendaftaran_sertifikasis` (id, user_id, jadwal_id, dokumen_berkas, status, token_ujian).

> *Gambar 3.8 ERD Iterasi 2*

**B. User Interface 2**

Antarmuka dirancang mencakup: halaman daftar jadwal sertifikasi (mahasiswa), form pendaftaran dengan upload berkas, halaman riwayat pendaftaran, dan halaman manajemen sertifikasi (admin dan Kepala Lab).

> *Gambar 3.9 Mockup Antarmuka Daftar Jadwal Sertifikasi*

> *Gambar 3.10 Mockup Antarmuka Manajemen Sertifikasi Admin*

### 3.4.2.3 Implementation 2

**A. Unit Testing 2**

Pengujian dilakukan terhadap `MahasiswaSertifikasiController` dan `AdminSertifikasiController` untuk memverifikasi alur pendaftaran, verifikasi berkas, dan pencatatan hasil.

**Tabel 3.14 Kode Unit Testing 2 - Sertifikasi**

```php
// tests/Unit/Controllers/MahasiswaSertifikasiControllerTest.php
<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\JadwalSertifikasi;
use App\Models\PendaftaranSertifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaSertifikasiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_jadwal_sertifikasi()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);
        $response = $this->get(route('mahasiswa.sertifikasi.index'));
        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_register_to_jadwal()
    {
        $user   = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $jadwal = JadwalSertifikasi::factory()->create(['kuota' => 10]);
        $this->actingAs($user);
        $response = $this->post(route('mahasiswa.sertifikasi.daftar.store', $jadwal), [
            'dokumen_berkas' => \Illuminate\Http\UploadedFile::fake()->create('berkas.pdf', 100),
        ]);
        $response->assertRedirect(route('mahasiswa.sertifikasi.riwayat'));
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'user_id'   => $user->id,
            'jadwal_id' => $jadwal->id,
            'status'    => 'menunggu',
        ]);
    }

    public function test_admin_can_verify_pendaftaran()
    {
        $admin       = User::factory()->create(['role' => 'admin']);
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);
        $response = $this->post(route('admin.sertifikasi.verifikasi', $pendaftaran));
        $response->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'diverifikasi',
        ]);
    }
}
```

> *Gambar 3.11 Hasil Unit Testing 2 (3 tests, 3 passed)*

**B. Code 2 - Pseudocode**

**Tabel 3.15 Pseudocode Modul Sertifikasi**

```
FUNCTION daftarStore(dokumen_berkas, jadwal_id):
  VALIDASI: dokumen_berkas wajib ada, format pdf/jpg/png, maks 2MB
  simpan file ke storage 'sertifikasi/public'
  buat record PendaftaranSertifikasi dengan:
    user_id        = user yang sedang login
    jadwal_id      = jadwal yang dipilih
    dokumen_berkas = path file yang disimpan
    status         = 'menunggu'
  redirect ke halaman riwayat dengan pesan sukses
END FUNCTION

FUNCTION verifikasi(pendaftaran_id):
  generate token ujian: 6 karakter acak (huruf kapital)
  update PendaftaranSertifikasi:
    status      = 'diverifikasi'
    token_ujian = token yang digenerate
  redirect dengan pesan "Diverifikasi. Token: [TOKEN]"
END FUNCTION

FUNCTION hasilLulus(pendaftaran_id):
  update PendaftaranSertifikasi: status = 'lulus'
  redirect dengan pesan konfirmasi
END FUNCTION

FUNCTION hasilTidakLulus(pendaftaran_id):
  update PendaftaranSertifikasi: status = 'tidak_lulus'
  redirect dengan pesan konfirmasi
END FUNCTION
```

**Tabel 3.16 Penjelasan Pseudocode Sertifikasi**

| Proses | Penjelasan (Output) |
|--------|---------------------|
| Pendaftaran oleh mahasiswa | File berkas disimpan ke storage dan record pendaftaran dibuat dengan status menunggu. |
| Verifikasi oleh admin | Token ujian unik 6 karakter digenerate otomatis; status berubah menjadi diverifikasi. |
| Pencatatan hasil | Admin/Kepala Lab menandai peserta sebagai lulus atau tidak_lulus setelah ujian berlangsung. |
| Cetak sertifikat | Mahasiswa dapat mencetak sertifikat jika status sudah lulus. |

> *Gambar 3.12 Halaman Daftar Jadwal Sertifikasi*

> *Gambar 3.13 Halaman Manajemen Sertifikasi Admin*

### 3.4.2.4 Retrospective 2

Seluruh user story (US-06 hingga US-11) berhasil diimplementasikan. Modul sertifikasi berjalan lengkap mulai pembuatan jadwal, pendaftaran, verifikasi, token ujian, pencatatan hasil, hingga cetak sertifikat.

**Tabel 3.17 Hasil Retrospective 2**

| Faktor Keberhasilan | Faktor Hambatan |
|---------------------|-----------------|
| Pola CRUD iterasi pertama dapat digunakan kembali untuk modul jadwal dan skema sertifikasi. | Pengecekan sisa kuota secara real-time memerlukan perhitungan relasi teliti antara jadwal dan pendaftaran. |
| Inertia.js memungkinkan tampilan diperbarui reaktif tanpa full page reload. | Upload berkas mahasiswa memerlukan konfigurasi storage yang cermat. |

---

## 3.4.3 Iterasi 3 - Modul Surat Bebas Laboratorium

Iterasi ketiga mengembangkan modul Surat Bebas Laboratorium. Modul ini memungkinkan mahasiswa mengajukan permohonan surat bebas lab secara daring sebagai syarat yudisium/kelulusan. Alur berjenjang: mahasiswa mengajukan, admin memvalidasi, Kepala Lab memberikan persetujuan akhir.

### 3.4.3.1 Iteration Initialization 3

**Tabel 3.18 User Story Iterasi 3**

| Kode | As a | I Want | So That |
|------|------|--------|---------|
| US-12 | Mahasiswa | Saya ingin mengajukan surat bebas lab | Saya dapat memenuhi syarat administratif yudisium |
| US-13 | Admin | Saya ingin memverifikasi pengajuan surat bebas lab | Saya dapat memastikan kelengkapan data sebelum ke Kepala Lab |
| US-14 | Kepala Lab | Saya ingin menyetujui pengajuan surat bebas lab | Mahasiswa dapat mencetak surat bebas laboratorium |

**Tabel 3.19 Iteration Initialization 3**

| Kode | User Story | Task | Story Point |
|------|------------|------|-------------|
| US-12 | Mengajukan surat bebas lab | Membuat halaman pengajuan dan riwayat surat bebas lab untuk mahasiswa | 5 |
| US-13 | Memverifikasi pengajuan | Membuat halaman daftar permohonan untuk admin beserta aksi validasi/tolak | 5 |
| US-14 | Menyetujui pengajuan | Membuat halaman persetujuan Kepala Lab beserta fitur cetak surat bebas | 5 |
| **Total** | | | **15** |

### 3.4.3.2 Design 3

**A. Entity Relationship Diagram 3**

ERD iterasi ketiga menambahkan entitas `surat_bebas_labs` dengan atribut: id, user_id, status, catatan, divalidasi_oleh, disetujui_oleh, dan timestamps. Berelasi dengan `users` melalui foreign key `user_id`.

> *Gambar 3.14 ERD Iterasi 3*

**B. User Interface 3**

Antarmuka mencakup halaman pengajuan surat bebas lab (mahasiswa), halaman daftar permohonan (admin), dan halaman persetujuan dengan tombol cetak surat (Kepala Lab).

> *Gambar 3.15 Mockup Antarmuka Pengajuan Surat Bebas Lab*

### 3.4.3.3 Implementation 3

**A. Unit Testing 3**

Pengujian dilakukan terhadap `MahasiswaSuratBebasLabController` untuk memverifikasi: tampilan daftar surat, pengajuan baru, dan pembatasan cetak jika status belum disetujui.

**Tabel 3.20 Kode Unit Testing 3 - Surat Bebas Lab**

```php
// tests/Unit/Controllers/MahasiswaSuratBebasLabControllerTest.php
<?php
namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\SuratBebasLab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaSuratBebasLabControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mahasiswa_can_view_surat_bebas_list()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);
        $response = $this->get(route('mahasiswa.surat-bebas-lab.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function mahasiswa_can_submit_surat_bebas_lab()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);
        $response = $this->post(route('mahasiswa.surat-bebas-lab.store'));
        $response->assertRedirect(route('mahasiswa.surat-bebas-lab.index'));
    }

    /** @test */
    public function cetak_blocked_if_status_not_disetujui()
    {
        $user  = User::factory()->create(['role' => 'mahasiswa']);
        $surat = SuratBebasLab::factory()->create([
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
        $this->actingAs($user);
        $response = $this->get(route('mahasiswa.surat-bebas-lab.cetak', $surat));
        $response->assertForbidden();
    }
}
```

> *Gambar 3.16 Hasil Unit Testing 3 (3 tests, 3 passed)*

**B. Code 3 - Pseudocode**

**Tabel 3.21 Pseudocode Modul Surat Bebas Laboratorium**

```
FUNCTION store(user_id):
  panggil SuratBebasLabService->cekTanggungan(user_id)
  IF mahasiswa masih memiliki tanggungan peminjaman aktif THEN
    RETURN error "Masih ada tanggungan peminjaman yang belum diselesaikan"
  END IF
  buat record SuratBebasLab dengan:
    user_id = user yang sedang login
    status  = 'menunggu'
  redirect ke halaman index dengan pesan sukses
END FUNCTION

FUNCTION validasi(surat_id, admin_id):
  update SuratBebasLab:
    status          = 'divalidasi'
    divalidasi_oleh = admin_id
  redirect dengan pesan "Permohonan berhasil divalidasi"
END FUNCTION

FUNCTION setujui(surat_id, kepala_lab_id):
  update SuratBebasLab:
    status          = 'disetujui'
    disetujui_oleh  = kepala_lab_id
  redirect dengan pesan "Permohonan berhasil disetujui"
END FUNCTION

FUNCTION cetak(surat_id):
  VALIDASI kepemilikan: surat.user_id harus == user yang login (403 jika tidak)
  VALIDASI status: surat.status harus 'disetujui' (403 jika belum)
  ambil data Kepala Lab dari tabel users (role = 'kepala_lab')
  tampilkan view cetak surat dengan data surat dan kepala_lab
END FUNCTION
```

**Tabel 3.22 Penjelasan Pseudocode Surat Bebas Lab**

| Proses | Penjelasan (Output) |
|--------|---------------------|
| Pengajuan oleh mahasiswa | Service memeriksa tanggungan. Jika ada, pengajuan ditolak dengan pesan spesifik. |
| Validasi oleh admin | Admin memeriksa data dan mengubah status menjadi divalidasi. |
| Persetujuan Kepala Lab | Kepala Lab memberikan keputusan akhir: disetujui atau ditolak. |
| Cetak surat | Mahasiswa hanya bisa cetak jika status sudah disetujui. Surat menampilkan tanda tangan Kepala Lab. |

> *Gambar 3.17 Halaman Pengajuan Surat Bebas Lab*

> *Gambar 3.18 Halaman Manajemen Surat Bebas Lab Admin*

### 3.4.3.4 Retrospective 3

Seluruh user story (US-12 hingga US-14) berhasil diimplementasikan. Fitur Surat Bebas Lab berjalan lengkap dengan alur berjenjang mahasiswa - admin - Kepala Lab.

**Tabel 3.23 Hasil Retrospective 3**

| Faktor Keberhasilan | Faktor Hambatan |
|---------------------|-----------------|
| Service Class (SuratBebasLabService) berhasil memisahkan logika bisnis dari controller. | Logika pengecekan tanggungan lintas modul memerlukan koordinasi cermat agar akurat. |
| Fitur cetak surat berbasis Blade menghasilkan dokumen rapi tanpa library PDF eksternal. | Validasi kepemilikan dokumen perlu diimplementasikan konsisten di setiap endpoint. |

---

## 3.4.4 Iterasi 4 - Modul Izin Penelitian

Iterasi keempat mengembangkan modul Izin Penelitian yang memfasilitasi mahasiswa dan dosen mengajukan permohonan izin penelitian di laboratorium secara daring. Alur: mahasiswa/dosen mengajukan, admin memverifikasi kelengkapan berkas, Kepala Lab memberikan persetujuan akhir dan menerbitkan surat izin resmi.

### 3.4.4.1 Iteration Initialization 4

**Tabel 3.24 User Story Iterasi 4**

| Kode | As a | I Want | So That |
|------|------|--------|---------|
| US-15 | Mahasiswa dan Dosen | Saya ingin mengajukan izin penelitian di laboratorium | Saya mendapat persetujuan resmi untuk melakukan penelitian di lab |
| US-16 | Admin | Saya ingin memverifikasi izin penelitian | Saya dapat memastikan data dan tujuan penelitian lengkap |
| US-17 | Kepala Lab | Saya ingin menyetujui izin penelitian | Mahasiswa/Dosen dapat mencetak surat izin penelitian |

**Tabel 3.25 Iteration Initialization 4**

| Kode | User Story | Task | Story Point |
|------|------------|------|-------------|
| US-15 | Mengajukan izin penelitian | Membuat form pengajuan (judul, tujuan, berkas opsional, data dibutuhkan) dan halaman riwayat | 5 |
| US-16 | Memverifikasi izin penelitian | Membuat halaman daftar permohonan admin dengan aksi verifikasi/tolak | 3 |
| US-17 | Menyetujui izin penelitian | Membuat halaman persetujuan Kepala Lab dengan fitur cetak surat izin | 5 |
| **Total** | | | **13** |

### 3.4.4.2 Design 4

**A. Entity Relationship Diagram 4**

ERD iterasi keempat menambahkan entitas `izin_penelitians` dengan atribut: id, user_id, judul_penelitian, tujuan, dokumen_berkas (opsional), data_dibutuhkan (JSON array), status, catatan, dan timestamps. Berelasi dengan `users` melalui foreign key `user_id`.

> *Gambar 3.19 ERD Iterasi 4*

**B. User Interface 4**

Antarmuka mencakup: form pengajuan izin penelitian (mahasiswa/dosen), halaman daftar permohonan (admin), dan halaman persetujuan dengan cetak surat (Kepala Lab).

> *Gambar 3.20 Mockup Antarmuka Pengajuan Izin Penelitian*

> *Gambar 3.21 Mockup Antarmuka Manajemen Izin Penelitian Admin*

### 3.4.4.3 Implementation 4

**A. Unit Testing 4**

Pengujian dilakukan terhadap `MahasiswaIzinPenelitianController` untuk memverifikasi: pengajuan izin, pembatasan akses antar mahasiswa, dan pembatasan cetak jika belum disetujui.

**Tabel 3.26 Kode Unit Testing 4 - Izin Penelitian**

```php
// tests/Unit/Controllers/MahasiswaIzinPenelitianControllerTest.php
<?php
namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\IzinPenelitian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaIzinPenelitianControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mahasiswa_can_submit_izin_penelitian()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);
        $response = $this->post(route('mahasiswa.izin-penelitian.store'), [
            'judul_penelitian' => 'Analisis Keamanan Jaringan Komputer',
            'tujuan'           => 'Menyelesaikan penelitian Tugas Akhir',
            'data_dibutuhkan'  => ['Router', 'Switch', 'PC'],
        ]);
        $response->assertRedirect(route('mahasiswa.izin-penelitian.index'));
        $this->assertDatabaseHas('izin_penelitians', [
            'user_id'          => $user->id,
            'judul_penelitian' => 'Analisis Keamanan Jaringan Komputer',
            'status'           => 'menunggu',
        ]);
    }

    /** @test */
    public function mahasiswa_cannot_view_other_izin()
    {
        $user1 = User::factory()->create(['role' => 'mahasiswa']);
        $user2 = User::factory()->create(['role' => 'mahasiswa']);
        $izin  = IzinPenelitian::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);
        $response = $this->get(route('mahasiswa.izin-penelitian.show', $izin));
        $response->assertForbidden();
    }

    /** @test */
    public function cetak_blocked_if_not_disetujui()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $izin = IzinPenelitian::factory()->create([
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
        $this->actingAs($user);
        $response = $this->get(route('mahasiswa.izin-penelitian.cetak', $izin));
        $response->assertForbidden();
    }
}
```

> *Gambar 3.22 Hasil Unit Testing 4 (3 tests, 3 passed)*

**B. Code 4 - Pseudocode**

**Tabel 3.27 Pseudocode Modul Izin Penelitian**

```
FUNCTION store(judul_penelitian, tujuan, dokumen_berkas, data_dibutuhkan):
  VALIDASI:
    judul_penelitian : wajib, maks 255 karakter
    tujuan           : wajib
    dokumen_berkas   : opsional, format pdf/jpg/png, maks 2MB
    data_dibutuhkan  : array of string
  IF ada dokumen_berkas THEN
    simpan file ke storage 'izin_penelitian/public'
  END IF
  buat record IzinPenelitian dengan:
    user_id          = user yang sedang login
    judul_penelitian = input judul
    tujuan           = input tujuan
    dokumen_berkas   = path file (atau null)
    data_dibutuhkan  = JSON array data yang dibutuhkan
    status           = 'menunggu'
  redirect ke halaman index dengan pesan sukses
END FUNCTION

FUNCTION cetak(izin_id):
  VALIDASI kepemilikan: izin.user_id harus == user login (403 jika tidak)
  VALIDASI status: izin.status harus 'disetujui' (403 jika belum)
  muat relasi user pada izin penelitian
  ambil data Kepala Lab dari users (role = 'kepala_lab')
  tampilkan view cetak surat izin dengan data izin dan kepala_lab
END FUNCTION
```

**Tabel 3.28 Penjelasan Pseudocode Izin Penelitian**

| Proses | Penjelasan (Output) |
|--------|---------------------|
| Pengajuan oleh mahasiswa | Data permohonan dan berkas opsional disimpan dengan status menunggu. |
| Validasi kepemilikan | Sistem mencegah mahasiswa melihat/mencetak izin milik mahasiswa lain (HTTP 403). |
| Validasi status cetak | Surat izin hanya dapat dicetak jika status sudah disetujui Kepala Lab. |
| Cetak surat izin | Surat ditampilkan via Blade view dengan data mahasiswa, detail penelitian, dan tanda tangan Kepala Lab. |

> *Gambar 3.23 Halaman Pengajuan Izin Penelitian*

> *Gambar 3.24 Halaman Manajemen Izin Penelitian Admin*

### 3.4.4.4 Retrospective 4

Seluruh user story (US-15 hingga US-17) berhasil diimplementasikan. Modul Izin Penelitian berjalan lengkap dengan alur pengajuan, verifikasi, persetujuan, dan cetak surat.

**Tabel 3.29 Hasil Retrospective 4**

| Faktor Keberhasilan | Faktor Hambatan |
|---------------------|-----------------|
| Pola alur berjenjang dari iterasi sebelumnya (Surat Bebas Lab) dapat digunakan kembali secara konsisten. | Penyimpanan field data_dibutuhkan sebagai JSON array memerlukan casting yang tepat pada model IzinPenelitian. |
| Struktur kode modular mempercepat pengembangan iterasi ini. | Pengujian skenario akses lintas pengguna perlu dilakukan cermat untuk keamanan data. |

---

## 3.4.5 Iterasi 5 - Modul Peminjaman Laboratorium

Iterasi kelima adalah iterasi terakhir pengembangan SIMLAB. Modul ini memungkinkan mahasiswa dan dosen mengajukan peminjaman ruangan laboratorium dengan pengecekan jadwal otomatis real-time untuk mencegah tumpang tindih. Alur: pengajuan oleh peminjam, verifikasi admin, persetujuan Kepala Lab, cetak surat, pelaporan pengembalian, verifikasi pengembalian, dan cetak bukti pengembalian.

### 3.4.5.1 Iteration Initialization 5

**Tabel 3.30 User Story Iterasi 5**

| Kode | As a | I Want | So That |
|------|------|--------|---------|
| US-18 | Mahasiswa dan Dosen | Saya ingin mengajukan peminjaman ruangan laboratorium | Saya dapat menggunakan fasilitas lab sesuai kebutuhan |
| US-19 | Admin | Saya ingin memverifikasi peminjaman laboratorium | Saya dapat memastikan jadwal dan fasilitas tersedia |
| US-20 | Kepala Lab | Saya ingin menyetujui peminjaman laboratorium | Peminjam mendapat izin penggunaan lab secara resmi |
| US-21 | Mahasiswa dan Dosen | Saya ingin mencetak surat persetujuan peminjaman | Saya mendapat bukti resmi peminjaman laboratorium |
| US-22 | Mahasiswa dan Dosen | Saya ingin mengembalikan peminjaman laboratorium | Saya dapat menginformasikan bahwa ruangan telah selesai digunakan |
| US-23 | Admin | Saya ingin memverifikasi pengembalian laboratorium | Saya dapat memastikan ruangan laboratorium telah dikembalikan dengan baik |
| US-24 | Mahasiswa dan Dosen | Saya ingin mencetak surat pengembalian ruangan | Saya mendapat bukti resmi pengembalian ruangan laboratorium |

**Tabel 3.31 Iteration Initialization 5**

| Kode | User Story | Task | Story Point |
|------|------------|------|-------------|
| US-18 | Mengajukan peminjaman | Membuat form peminjaman dengan custom rule AvailableSchedule dan upload surat keterangan | 3 |
| US-19 | Memverifikasi peminjaman | Membuat halaman manajemen peminjaman admin dengan aksi verifikasi/tolak | 3 |
| US-20 | Menyetujui peminjaman | Membuat halaman persetujuan Kepala Lab; jadwal tercatat di kalender lab | 3 |
| US-21 | Mencetak surat persetujuan | Membuat fitur cetak bukti peminjaman berbasis Blade PDF | 3 |
| US-22 | Mengembalikan peminjaman | Membuat fitur pelaporan pengembalian ruangan oleh peminjam | 3 |
| US-23 | Memverifikasi pengembalian | Membuat aksi konfirmasi pengembalian ruangan oleh admin | 3 |
| US-24 | Mencetak surat pengembalian | Membuat fitur cetak bukti pengembalian berbasis Blade PDF | 3 |
| **Total** | | | **21** |

### 3.4.5.2 Design 5

**A. Entity Relationship Diagram 5**

ERD iterasi kelima menambahkan entitas `peminjaman_labs` dengan atribut: id, user_id, ruangan, tanggal_pinjam, jam_mulai, jam_selesai, keperluan, surat_keterangan, status, waktu_dikembalikan, dan timestamps. Berelasi dengan `users` dan `ruangans`.

> *Gambar 3.25 ERD Iterasi 5*

**B. User Interface 5**

Antarmuka mencakup: form peminjaman dengan cek jadwal real-time (mahasiswa/dosen), halaman manajemen peminjaman (admin dan Kepala Lab), serta tampilan kalender ketersediaan ruangan.

> *Gambar 3.26 Mockup Antarmuka Form Peminjaman Laboratorium*

> *Gambar 3.27 Mockup Tampilan Cek Jadwal Ruangan Real-time*

### 3.4.5.3 Implementation 5

**A. Unit Testing 5**

Pengujian dilakukan terhadap `PeminjamanLabController` untuk memverifikasi: pengajuan peminjaman, query cek jadwal real-time, dan pelaporan pengembalian.

**Tabel 3.32 Kode Unit Testing 5 - Peminjaman Laboratorium**

```php
// tests/Unit/Controllers/PeminjamanLabControllerTest.php
<?php
namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\PeminjamanLab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanLabControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mahasiswa_can_submit_peminjaman()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);
        $response = $this->post(route('mahasiswa.peminjaman-lab.store'), [
            'ruangan'        => 'Lab Jaringan',
            'tanggal_pinjam' => now()->addDay()->format('Y-m-d'),
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '10:00',
            'keperluan'      => 'Praktikum Jaringan Komputer',
        ]);
        $response->assertRedirect(route('mahasiswa.peminjaman-lab.index'));
        $this->assertDatabaseHas('peminjaman_labs', [
            'user_id' => $user->id,
            'ruangan' => 'Lab Jaringan',
            'status'  => 'menunggu',
        ]);
    }

    /** @test */
    public function cek_jadwal_returns_booked_slots()
    {
        $user = User::factory()->create();
        PeminjamanLab::factory()->create([
            'ruangan'        => 'Lab Jaringan',
            'tanggal_pinjam' => '2025-06-01',
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '10:00',
            'status'         => 'disetujui',
        ]);
        $this->actingAs($user);
        $response = $this->getJson(route('mahasiswa.peminjaman-lab.cek-jadwal', [
            'ruangan' => 'Lab Jaringan',
            'tanggal' => '2025-06-01',
        ]));
        $response->assertStatus(200)->assertJsonCount(1);
    }

    /** @test */
    public function mahasiswa_can_report_pengembalian()
    {
        $user       = User::factory()->create(['role' => 'mahasiswa']);
        $peminjaman = PeminjamanLab::factory()->create([
            'user_id' => $user->id,
            'status'  => 'disetujui',
        ]);
        $this->actingAs($user);
        $response = $this->post(route('mahasiswa.peminjaman-lab.kembalikan', $peminjaman));
        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_labs', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu_kembali',
        ]);
    }
}
```

> *Gambar 3.28 Hasil Unit Testing 5 (3 tests, 3 passed)*

**B. Code 5 - Pseudocode**

**Tabel 3.33 Pseudocode Modul Peminjaman Laboratorium**

```
FUNCTION store(ruangan, tanggal_pinjam, jam_mulai, jam_selesai, keperluan, surat_keterangan):
  VALIDASI:
    ruangan        : wajib, maks 100 karakter
    ruangan        : gunakan custom rule AvailableSchedule untuk cek jadwal bentrok:
                     query PeminjamanLab berdasarkan ruangan dan tanggal
                     cek apakah ada jadwal aktif (menunggu/diverifikasi/disetujui)
                     yang jam-nya tumpang tindih dengan jam_mulai dan jam_selesai
                     IF bentrok THEN gagalkan validasi dengan pesan error
    tanggal_pinjam : wajib, format date, tidak boleh di masa lalu
    jam_mulai      : wajib
    jam_selesai    : wajib, harus setelah jam_mulai
    keperluan      : wajib
    surat_keterangan: opsional, format pdf/jpg/png, maks 2MB
  IF ada surat_keterangan THEN
    simpan file ke storage 'peminjaman/public'
  END IF
  buat record PeminjamanLab dengan semua data dan status = 'menunggu'
  redirect ke halaman index dengan pesan sukses
END FUNCTION

FUNCTION cekJadwal(ruangan, tanggal):
  IF ruangan atau tanggal kosong THEN
    RETURN JSON array kosong
  END IF
  query PeminjamanLab berdasarkan ruangan dan tanggal_pinjam
  filter status: menunggu, diverifikasi, disetujui, menunggu_kembali
  urutkan berdasarkan jam_mulai
  RETURN JSON: [id, jam_mulai, jam_selesai, status, user_id, keperluan]
END FUNCTION

FUNCTION kembalikan(peminjaman_id):
  VALIDASI kepemilikan: peminjaman.user_id == user login (403 jika tidak)
  VALIDASI status: peminjaman.status harus 'disetujui' (403 jika belum)
  update PeminjamanLab:
    status              = 'menunggu_kembali'
    waktu_dikembalikan  = waktu sekarang
  redirect dengan pesan "Pengembalian berhasil dilaporkan. Menunggu verifikasi admin."
END FUNCTION
```

**Tabel 3.34 Penjelasan Pseudocode Peminjaman Laboratorium**

| Proses | Penjelasan (Output) |
|--------|---------------------|
| Validasi jadwal bentrok | Custom rule AvailableSchedule memeriksa tumpang tindih jadwal otomatis. Jika bentrok, pengajuan gagal dengan pesan informatif. |
| Penyimpanan peminjaman | Data disimpan dengan status menunggu dan menunggu verifikasi Admin. |
| Cek jadwal real-time | Endpoint cekJadwal mengembalikan JSON slot terpakai sehingga peminjam dapat memilih slot kosong sebelum mengajukan. |
| Pelaporan pengembalian | Mahasiswa melaporkan pengembalian; status berubah ke menunggu_kembali hingga Admin mengonfirmasi. |
| Cetak bukti | Peminjam dapat mencetak bukti peminjaman dan bukti pengembalian sebagai dokumentasi resmi. |

> *Gambar 3.29 Halaman Form Peminjaman Laboratorium*

> *Gambar 3.30 Tampilan Cek Jadwal Ruangan Real-time*

### 3.4.5.4 Retrospective 5

Seluruh user story (US-18 hingga US-24) berhasil diimplementasikan. Modul Peminjaman Laboratorium merupakan modul paling kompleks dalam SIMLAB karena melibatkan validasi jadwal real-time, namun berhasil diselesaikan sesuai rencana.

**Tabel 3.35 Hasil Retrospective 5**

| Faktor Keberhasilan | Faktor Hambatan |
|---------------------|-----------------|
| Custom rule AvailableSchedule efektif mencegah tumpang tindih peminjaman tanpa logika kompleks di controller. | Overlap detection interval waktu memerlukan logika kondisional yang cermat agar tidak ada celah. |
| Endpoint cekJadwal memungkinkan Vue.js memperbarui ketersediaan ruangan secara dinamis tanpa full page reload. | Pengelolaan 6 status peminjaman memerlukan dokumentasi alur status yang jelas. |
| Arsitektur VILT yang sudah mapan dari iterasi sebelumnya memperlancar pengembangan iterasi ini. | - |

---

## Ringkasan Seluruh Iterasi

| Iterasi | Modul | Kode US | Story Point |
|---------|-------|---------|-------------|
| 1 | Core System dan Company Profile | US-01 s/d US-05 | 17 |
| 2 | Sertifikasi Kompetensi | US-06 s/d US-11 | 22 |
| 3 | Surat Bebas Laboratorium | US-12 s/d US-14 | 15 |
| 4 | Izin Penelitian | US-15 s/d US-17 | 13 |
| 5 | Peminjaman Laboratorium | US-18 s/d US-24 | 21 |
| **Total** | | **US-01 s/d US-24** | **88** |
