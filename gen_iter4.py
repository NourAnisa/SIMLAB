
import textwrap

section = textwrap.dedent("""\
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
namespace Tests\\Unit\\Controllers;

use App\\Models\\User;
use App\\Models\\IzinPenelitian;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

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

""")

with open(r'c:\xampp\htdocs\SIMLAB\bab3_4_metode_implementasi.md', 'a', encoding='utf-8') as f:
    f.write(section)

print("OK - Iterasi 4 done")
