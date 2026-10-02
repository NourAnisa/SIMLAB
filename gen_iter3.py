
import textwrap

section = textwrap.dedent("""\
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
namespace Tests\\Unit\\Controllers;

use App\\Models\\User;
use App\\Models\\SuratBebasLab;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

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

""")

with open(r'c:\xampp\htdocs\SIMLAB\bab3_4_metode_implementasi.md', 'a', encoding='utf-8') as f:
    f.write(section)

print("OK - Iterasi 3 done")
