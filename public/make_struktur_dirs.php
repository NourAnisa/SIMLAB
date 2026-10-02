<?php
// Script to create directories and run DB migration for struktur organisasi
// Run once at: http://127.0.0.1:8000/make_struktur_dirs.php

$dirs = [
    __DIR__ . '/../resources/views/kepala_lab/struktur',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created: $dir\n";
    } else {
        echo "Exists: $dir\n";
    }
}

// DB migration
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=lab_management', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS `struktur_organisasi` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `nama` VARCHAR(255) NOT NULL,
        `jabatan` VARCHAR(255) NOT NULL,
        `deskripsi` TEXT NULL,
        `foto` VARCHAR(255) NULL,
        `urutan` INT NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "Table struktur_organisasi: OK\n";

    // Seed sample data if empty
    $count = $pdo->query("SELECT COUNT(*) FROM struktur_organisasi")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO struktur_organisasi (nama, jabatan, deskripsi, urutan, created_at, updated_at) VALUES
            ('Dr. Ahmad Fauzi, M.Kom', 'Kepala Laboratorium', 'Bertanggung jawab atas pengelolaan dan pengembangan laboratorium komputer.', 1, NOW(), NOW()),
            ('Budi Santoso, S.Kom', 'Teknisi Laboratorium', 'Mengelola perangkat keras dan lunak di laboratorium.', 2, NOW(), NOW()),
            ('Siti Rahayu, S.T.', 'Asisten Laboratorium', 'Membantu pelaksanaan praktikum mahasiswa.', 3, NOW(), NOW()),
            ('Rizki Pratama', 'Asisten Laboratorium', 'Membantu pelaksanaan praktikum mahasiswa.', 4, NOW(), NOW())
        ");
        echo "Seeded sample struktur data.\n";
    }

    echo "\nDB: All done!\n";
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}

// Write blade view
$viewPath = __DIR__ . '/../resources/views/kepala_lab/struktur/index.blade.php';
if (!file_exists($viewPath)) {
    $viewContent = <<<'BLADE'
<x-app-layout>
    <x-slot name="header">Struktur Organisasi</x-slot>

    @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 18px;border-radius:10px;margin-bottom:18px;font-size:14px;font-weight:600">
        ✅ {{ session('success') }}
    </div>
    @endif

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <div>
            <p class="page-title">Struktur Organisasi Lab</p>
            <p class="page-sub">Kelola susunan organisasi laboratorium yang tampil di halaman publik.</p>
        </div>
        <button onclick="document.getElementById('addModal').style.display='flex'"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
            <i class="fa fa-plus"></i> Tambah Anggota
        </button>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px">
        @forelse($strukturs as $s)
        <div style="background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;border:1px solid #f1f5f9">
            <div style="height:6px;background:linear-gradient(90deg,#4f46e5,#7c3aed)"></div>
            <div style="padding:20px;text-align:center">
                @if($s->foto && file_exists(public_path('storage/'.$s->foto)))
                <img src="{{ asset('storage/'.$s->foto) }}" alt="{{ $s->nama }}"
                    style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e0e7ff;margin-bottom:12px">
                @else
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px;color:#fff;font-weight:700">
                    {{ strtoupper(substr($s->nama, 0, 1)) }}
                </div>
                @endif
                <div style="font-size:13px;font-weight:600;color:#4f46e5;background:#ede9fe;padding:3px 10px;border-radius:20px;display:inline-block;margin-bottom:8px">
                    {{ $s->jabatan }}
                </div>
                <h4 style="margin:0 0 6px;font-size:15px;color:#1e293b;font-weight:700">{{ $s->nama }}</h4>
                @if($s->deskripsi)
                <p style="margin:0;font-size:12px;color:#64748b;line-height:1.5">{{ $s->deskripsi }}</p>
                @endif
            </div>
            <div style="padding:12px 20px;border-top:1px solid #f1f5f9;display:flex;gap:8px">
                <button onclick="openEdit({{ $s->id }}, '{{ e($s->nama) }}', '{{ e($s->jabatan) }}', '{{ e($s->deskripsi ?? '') }}', {{ $s->urutan }})"
                    style="flex:1;padding:7px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;color:#4f46e5">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <form method="POST" action="{{ route('kepala_lab.struktur.destroy', $s) }}" onsubmit="return confirm('Hapus anggota ini?')" style="flex:1">
                    @csrf @method('DELETE')
                    <button type="submit" style="width:100%;padding:7px;background:#fff5f5;border:1px solid #fecaca;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;color:#dc2626">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px;color:#94a3b8">
            <div style="font-size:48px;margin-bottom:12px">👥</div>
            <p style="font-size:16px;font-weight:600">Belum ada data struktur organisasi</p>
        </div>
        @endforelse
    </div>

    {{-- ADD MODAL --}}
    <div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px">
        <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b">Tambah Anggota</h3>
                <button onclick="document.getElementById('addModal').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
            </div>
            <form method="POST" action="{{ route('kepala_lab.struktur.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap *</label>
                    <input name="nama" required placeholder="cth: Dr. Ahmad, M.Kom" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
                </div>
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jabatan *</label>
                    <input name="jabatan" required placeholder="cth: Kepala Laboratorium" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
                </div>
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Uraian singkat tugas/peran..." style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;resize:none;box-sizing:border-box"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Foto</label>
                        <input type="file" name="foto" accept="image/*" style="width:100%;padding:8px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;box-sizing:border-box">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Urutan</label>
                        <input name="urutan" type="number" value="0" min="0" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:20px">
                    <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                        style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
                    <button type="submit"
                        style="flex:2;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px">
        <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.2)">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                <h3 style="margin:0;font-size:18px;font-weight:700;color:#1e293b">Edit Anggota</h3>
                <button onclick="document.getElementById('editModal').style.display='none'" style="background:none;border:none;font-size:20px;cursor:pointer;color:#94a3b8">✕</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Nama Lengkap *</label>
                    <input id="edit-nama" name="nama" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
                </div>
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Jabatan *</label>
                    <input id="edit-jabatan" name="jabatan" required style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
                </div>
                <div style="margin-bottom:14px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Deskripsi</label>
                    <textarea id="edit-deskripsi" name="deskripsi" rows="2" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;resize:none;box-sizing:border-box"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Ganti Foto</label>
                        <input type="file" name="foto" accept="image/*" style="width:100%;padding:8px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;box-sizing:border-box">
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">Urutan</label>
                        <input id="edit-urutan" name="urutan" type="number" min="0" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;box-sizing:border-box">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:20px">
                    <button type="button" onclick="document.getElementById('editModal').style.display='none'"
                        style="flex:1;padding:11px;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">Batal</button>
                    <button type="submit"
                        style="flex:2;padding:11px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
                        <i class="fa fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openEdit(id, nama, jabatan, deskripsi, urutan) {
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-jabatan').value = jabatan;
        document.getElementById('edit-deskripsi').value = deskripsi;
        document.getElementById('edit-urutan').value = urutan;
        document.getElementById('editForm').action = '/kepala_lab/struktur/' + id;
        document.getElementById('editModal').style.display = 'flex';
    }
    </script>
</x-app-layout>
BLADE;
    file_put_contents($viewPath, $viewContent);
    echo "Created view: resources/views/kepala_lab/struktur/index.blade.php\n";
} else {
    echo "View already exists.\n";
}

echo "\n✅ All done! You can delete this file.";

