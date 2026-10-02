
import textwrap

section = textwrap.dedent("""\
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
namespace Tests\\Feature;

use App\\Models\\User;
use App\\Models\\JadwalSertifikasi;
use App\\Models\\PendaftaranSertifikasi;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

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
            'dokumen_berkas' => \\Illuminate\\Http\\UploadedFile::fake()->create('berkas.pdf', 100),
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

""")

with open(r'c:\xampp\htdocs\SIMLAB\bab3_4_metode_implementasi.md', 'a', encoding='utf-8') as f:
    f.write(section)

print("OK - Iterasi 2 done")
