<?php
// 1. Truncate cetak.blade.php – keep only first 398 lines (removes duplicate old designs)
$cetak = __DIR__ . '/../resources/views/mahasiswa/sertifikasi/cetak.blade.php';
$lines = file($cetak);
echo 'cetak.blade.php total lines: ' . count($lines) . '<br>';
file_put_contents($cetak, implode('', array_slice($lines, 0, 398)));
echo 'Trimmed to 398 lines.<br>';

// 2. Create sertifikat views directory + verifikasi view
$dir = __DIR__ . '/../resources/views/sertifikat';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$view = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Lato', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }
        .card {
            background: #fff;
            max-width: 520px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.12);
            overflow: hidden;
        }
        .card-header {
            background: #0f172a;
            color: #fff;
            padding: 24px 28px 20px;
            text-align: center;
        }
        .card-header img { width: 64px; margin-bottom: 10px; }
        .card-header h3 { font-size: 11pt; letter-spacing: 2px; text-transform: uppercase; color: #94a3b8; font-weight: 400; }
        .card-header h2 { font-size: 15pt; font-weight: 700; margin-top: 2px; }
        .status-banner {
            padding: 14px 28px;
            text-align: center;
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .status-valid   { background: #dcfce7; color: #15803d; }
        .status-invalid { background: #fee2e2; color: #b91c1c; }
        .card-body { padding: 22px 28px 28px; }
        .recipient { text-align: center; margin-bottom: 18px; }
        .recipient h1 { font-family: 'Playfair Display', serif; font-size: 22pt; color: #0f172a; }
        .recipient p { color: #64748b; margin-top: 3px; font-size: 10pt; }
        table.info { width: 100%; border-collapse: collapse; font-size: 10.5pt; }
        table.info tr { border-bottom: 1px solid #f1f5f9; }
        table.info td { padding: 9px 4px; vertical-align: top; }
        table.info td:first-child { color: #64748b; width: 140px; }
        table.info td:last-child { font-weight: 700; color: #0f172a; }
        .badge { display: inline-block; padding: 4px 16px; border-radius: 4px; font-weight: 700; font-size: 10pt; }
        .badge-lulus { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-tidak { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .cert-no {
            margin-top: 18px; padding: 10px 14px;
            background: #f8fafc; border-left: 4px solid #0f172a;
            font-size: 9pt; color: #475569;
        }
        .cert-no strong { color: #0f172a; }
        .card-footer {
            background: #f8fafc; border-top: 1px solid #e2e8f0;
            padding: 12px 28px; text-align: center;
            font-size: 8.5pt; color: #94a3b8;
        }
    </style>
</head>
<body>
@php
$skema = $data->jadwal->skema;
$kode  = strtoupper(substr($skema->nama_skema, 0, 3));
$noSertifikat = 'BNSP/STMIK/' . $kode . '/' . $data->updated_at->format('Y') . '/' . str_pad($data->id, 5, '0', STR_PAD_LEFT);
$isLulus = $data->status === 'lulus';
@endphp
<div class="card">
    <div class="card-header">
        <img src="{{ asset('images/stmik-logo.png') }}" alt="Logo STMIK">
        <h3>Laboratorium Komputer</h3>
        <h2>STMIK Samarinda</h2>
    </div>
    <div class="status-banner {{ $isLulus ? 'status-valid' : 'status-invalid' }}">
        {{ $isLulus ? '✓ SERTIFIKAT VALID' : '✗ SERTIFIKAT TIDAK LULUS' }}
    </div>
    <div class="card-body">
        <div class="recipient">
            <h1>{{ $data->user->name }}</h1>
            <p>{{ $data->user->nim_nip ?? '' }} &bull; {{ $data->user->jurusan ?? 'STMIK Samarinda' }}</p>
        </div>
        <table class="info">
            <tr>
                <td>Skema Uji</td>
                <td>{{ $skema->nama_skema }}</td>
            </tr>
            <tr>
                <td>Tanggal Ujian</td>
                <td>{{ $data->jadwal->tanggal->isoFormat('D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <td>Nilai</td>
                <td>{{ $data->nilai ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <span class="badge {{ $isLulus ? 'badge-lulus' : 'badge-tidak' }}">
                        {{ $isLulus ? 'LULUS' : 'TIDAK LULUS' }}
                    </span>
                </td>
            </tr>
        </table>
        <div class="cert-no">
            <strong>No. Sertifikat:</strong> {{ $noSertifikat }}
        </div>
    </div>
    <div class="card-footer">
        Verifikasi otomatis &mdash; Sistem Manajemen Laboratorium STMIK Samarinda &bull; {{ now()->isoFormat('D MMMM YYYY') }}
    </div>
</div>
</body>
</html>
BLADE;

if (!file_exists($dir . '/verifikasi.blade.php')) {
    file_put_contents($dir . '/verifikasi.blade.php', $view);
    echo 'verifikasi.blade.php created.<br>';
} else {
    echo 'verifikasi.blade.php already exists.<br>';
}

echo '<strong>All done!</strong>';
unlink(__FILE__);

$view = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Lato', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }
        .card {
            background: #fff;
            max-width: 520px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.12);
            overflow: hidden;
        }
        .card-header {
            background: #0f172a;
            color: #fff;
            padding: 24px 28px 20px;
            text-align: center;
        }
        .card-header img { width: 64px; margin-bottom: 10px; }
        .card-header h3 { font-size: 11pt; letter-spacing: 2px; text-transform: uppercase; color: #94a3b8; font-weight: 400; }
        .card-header h2 { font-size: 15pt; font-weight: 700; margin-top: 2px; }
        .status-banner {
            padding: 14px 28px;
            text-align: center;
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .status-valid   { background: #dcfce7; color: #15803d; }
        .status-invalid { background: #fee2e2; color: #b91c1c; }
        .card-body { padding: 22px 28px 28px; }
        .recipient { text-align: center; margin-bottom: 18px; }
        .recipient h1 { font-family: 'Playfair Display', serif; font-size: 22pt; color: #0f172a; }
        .recipient p { color: #64748b; margin-top: 3px; font-size: 10pt; }
        table.info { width: 100%; border-collapse: collapse; font-size: 10.5pt; }
        table.info tr { border-bottom: 1px solid #f1f5f9; }
        table.info td { padding: 9px 4px; vertical-align: top; }
        table.info td:first-child { color: #64748b; width: 140px; }
        table.info td:last-child { font-weight: 700; color: #0f172a; }
        .badge { display: inline-block; padding: 4px 16px; border-radius: 4px; font-weight: 700; font-size: 10pt; }
        .badge-lulus { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-tidak { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .cert-no {
            margin-top: 18px; padding: 10px 14px;
            background: #f8fafc; border-left: 4px solid #0f172a;
            font-size: 9pt; color: #475569;
        }
        .cert-no strong { color: #0f172a; }
        .card-footer {
            background: #f8fafc; border-top: 1px solid #e2e8f0;
            padding: 12px 28px; text-align: center;
            font-size: 8.5pt; color: #94a3b8;
        }
    </style>
</head>
<body>
@php
$skema = $data->jadwal->skema;
$kode  = strtoupper(substr($skema->nama_skema, 0, 3));
$noSertifikat = 'BNSP/STMIK/' . $kode . '/' . $data->updated_at->format('Y') . '/' . str_pad($data->id, 5, '0', STR_PAD_LEFT);
$isLulus = $data->status === 'lulus';
@endphp
<div class="card">
    <div class="card-header">
        <img src="{{ asset('images/stmik-logo.png') }}" alt="Logo STMIK">
        <h3>Laboratorium Komputer</h3>
        <h2>STMIK Samarinda</h2>
    </div>
    <div class="status-banner {{ $isLulus ? 'status-valid' : 'status-invalid' }}">
        {{ $isLulus ? '✓ SERTIFIKAT VALID' : '✗ SERTIFIKAT TIDAK LULUS' }}
    </div>
    <div class="card-body">
        <div class="recipient">
            <h1>{{ $data->user->name }}</h1>
            <p>{{ $data->user->nim_nip ?? '' }} &bull; {{ $data->user->jurusan ?? 'STMIK Samarinda' }}</p>
        </div>
        <table class="info">
            <tr>
                <td>Skema Uji</td>
                <td>{{ $skema->nama_skema }}</td>
            </tr>
            <tr>
                <td>Tanggal Ujian</td>
                <td>{{ $data->jadwal->tanggal->isoFormat('D MMMM YYYY') }}</td>
            </tr>
            <tr>
                <td>Nilai</td>
                <td>{{ $data->nilai ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <span class="badge {{ $isLulus ? 'badge-lulus' : 'badge-tidak' }}">
                        {{ $isLulus ? 'LULUS' : 'TIDAK LULUS' }}
                    </span>
                </td>
            </tr>
        </table>
        <div class="cert-no">
            <strong>No. Sertifikat:</strong> {{ $noSertifikat }}
        </div>
    </div>
    <div class="card-footer">
        Verifikasi otomatis &mdash; Sistem Manajemen Laboratorium STMIK Samarinda &bull; {{ now()->isoFormat('D MMMM YYYY') }}
    </div>
</div>
</body>
</html>
BLADE;

file_put_contents($dir . '/verifikasi.blade.php', $view);
echo 'Done! View created at: ' . $dir . '/verifikasi.blade.php';
unlink(__FILE__);

