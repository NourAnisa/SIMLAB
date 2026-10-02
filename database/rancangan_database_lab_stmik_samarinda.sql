
-- Tabel: users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100),
    nim_nip VARCHAR(30),
    email VARCHAR(100),
    password VARCHAR(255),
    role ENUM('mahasiswa', 'admin', 'kepala_lab'),
    foto_profil VARCHAR(255)
);

-- Tabel: skema_sertifikasi
CREATE TABLE skema_sertifikasi (
    skema_id INT AUTO_INCREMENT PRIMARY KEY,
    nama_skema VARCHAR(100),
    deskripsi TEXT
);

-- Tabel: jadwal_sertifikasi
CREATE TABLE jadwal_sertifikasi (
    jadwal_id INT AUTO_INCREMENT PRIMARY KEY,
    skema_id INT,
    tanggal DATE,
    kuota INT,
    FOREIGN KEY (skema_id) REFERENCES skema_sertifikasi(skema_id)
);

-- Tabel: pendaftaran_sertifikasi
CREATE TABLE pendaftaran_sertifikasi (
    pendaftaran_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    jadwal_id INT,
    dokumen_berkas VARCHAR(255),
    status ENUM('menunggu', 'diverifikasi', 'lulus', 'tidak_lulus'),
    token_ujian VARCHAR(10),
    jawaban_ujian TEXT,
    hasil_validasi TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (jadwal_id) REFERENCES jadwal_sertifikasi(jadwal_id)
);

-- Tabel: izin_penelitian
CREATE TABLE izin_penelitian (
    izin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    judul_penelitian VARCHAR(255),
    tujuan TEXT,
    dokumen_berkas VARCHAR(255),
    status ENUM('menunggu', 'diverifikasi', 'disetujui', 'ditolak'),
    catatan_verifikasi TEXT,
    tanggal_disetujui DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Tabel: peminjaman_lab
CREATE TABLE peminjaman_lab (
    pinjam_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ruangan VARCHAR(100),
    tanggal_pinjam DATE,
    jam_mulai TIME,
    jam_selesai TIME,
    keperluan TEXT,
    status ENUM('menunggu', 'diverifikasi', 'disetujui', 'ditolak'),
    surat_keterangan VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Tabel: surat_bebas_lab
CREATE TABLE surat_bebas_lab (
    surat_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    tanggal_pengajuan DATE,
    status ENUM('menunggu', 'masih_tanggungan', 'diverifikasi', 'disetujui'),
    catatan TEXT,
    tanggal_disetujui DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Tabel: log_aktivitas
CREATE TABLE log_aktivitas (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    aktivitas TEXT,
    waktu DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
