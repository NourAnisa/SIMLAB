
import textwrap

section = textwrap.dedent("""\
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
namespace Tests\\Unit\\Controllers;

use App\\Models\\User;
use App\\Models\\PeminjamanLab;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

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
""")

with open(r'c:\xampp\htdocs\SIMLAB\bab3_4_metode_implementasi.md', 'a', encoding='utf-8') as f:
    f.write(section)

print("OK - Iterasi 5 + Ringkasan done")
