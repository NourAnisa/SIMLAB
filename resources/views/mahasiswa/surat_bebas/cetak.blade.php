<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Bebas Lab - {{ $surat->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #e2e8f0;
        }

        .page {
            width: 210mm;
            margin: 0 auto;
            padding: 12mm 18mm 12mm 22mm;
            background: #fff;
        }

        .kop {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 6px;
            border-bottom: 4px double #000;
        }

        .kop-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .kop-teks {
            flex: 1;
            text-align: center;
            line-height: 1.25;
        }

        .kop-teks .k1 {
            font-size: 10pt;
            font-weight: bold;
        }

        .kop-teks .k2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-teks .k3 {
            font-size: 12pt;
            font-weight: bold;
        }

        .kop-teks .k4 {
            font-size: 8.5pt;
            margin-top: 2px;
        }

        .judul-surat {
            text-align: center;
            margin: 12px 0 10px;
        }

        .judul-surat .j1 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .judul-surat .j2 {
            font-size: 10.5pt;
            margin-top: 3px;
        }

        .teks {
            text-align: justify;
            line-height: 1.7;
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .tb {
            width: 100%;
            margin: 5px 0 5px 8px;
            border-collapse: collapse;
        }

        .tb td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11pt;
        }

        .tb td:first-child {
            width: 46mm;
        }

        .tb td:nth-child(2) {
            width: 7mm;
            text-align: center;
        }

        .ttd-wrap {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            text-align: center;
            min-width: 190px;
        }

        .ttd-box .tgl {
            font-size: 11pt;
            margin-bottom: 3px;
        }

        .ttd-box .jbt {
            font-weight: bold;
            font-size: 11pt;
        }

        .ttd-box .space {
            height: 90px;
            position: relative;
            width: 200px;
            margin: 0 auto;
        }

        .ttd-box .img-stemple {
            width: 90px;
            height: 90px;
            object-fit: contain;
            position: absolute;
            opacity: 0.75;
            left: 10px;
            top: 0;
            transform: rotate(-10deg);
        }

        .ttd-box .img-ttd {
            max-width: 130px;
            max-height: 60px;
            object-fit: contain;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-40%, -50%);
        }

        .ttd-box .nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
            margin-top: 3px;
        }

        .ttd-box .nip {
            font-size: 10.5pt;
            margin-top: 2px;
        }

        .tembusan {
            margin-top: 14px;
            font-size: 10.5pt;
        }

        .wm {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 72pt;
            font-weight: bold;
            color: rgba(14, 165, 233, .06);
            text-transform: uppercase;
            letter-spacing: 8px;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #fff;
            padding: 9px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 999;
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
        }

        .print-bar span {
            flex: 1;
        }

        .btn-p {
            padding: 7px 20px;
            background: #0ea5e9;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-b {
            padding: 7px 14px;
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
        }

        @media print {
            .print-bar {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .page {
                padding: 10mm 16mm 10mm 20mm;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="print-bar">
        <span>📄 Surat Bebas Lab — {{ $surat->user->name }}</span>
        <a href="{{ route('mahasiswa.surat-bebas-lab.show', $surat) }}" class="btn-b">← Kembali</a>
        <button class="btn-p" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="wm">BEBAS LAB</div>

    <div class="page">
        <div class="content">

            <!-- KOP SURAT -->
            <div class="kop">
                <img src="{{ asset('images/stmik-logo.png') }}" alt="Logo STMIK" class="kop-logo">
                <div class="kop-teks">
                    <div class="k1">SEKOLAH TINGGI</div>
                    <div class="k2">MANAJEMEN INFORMATIKA DAN KOMPUTER SAMARINDA</div>
                    <div class="k3">(STMIK SAMARINDA)</div>
                    <div class="k4">
                        Jl. Banggeris No.075 RT.03, Kel. Karang Anyar, Kec. Sungai Kunjang, Kota Samarinda,<br>
                        Provinsi Kalimantan Timur &nbsp;|&nbsp; Telp (+62)82353026237 &nbsp;|&nbsp; stmik.smd75@gmail.com
                    </div>
                </div>
            </div>

            <!-- JUDUL -->
            <div class="judul-surat">
                <div class="j1">Surat Keterangan Bebas Laboratorium</div>
                <div class="j2">Nomor: {{ sprintf('STMIK/%04d/Lab/Bebas/%d', $surat->id, now()->year) }}</div>
            </div>

            <p class="teks">
                Yang bertanda tangan di bawah ini, Kepala Laboratorium Komputer STMIK Samarinda,
                dengan ini menerangkan bahwa mahasiswa berikut:
            </p>

            <table class="tb">
                <tr>
                    <td>Nama Lengkap</td>
                    <td>:</td>
                    <td><strong>{{ $surat->user->name }}</strong></td>
                </tr>
                @if($surat->user->nim_nip)
                <tr>
                    <td>NIM</td>
                    <td>:</td>
                    <td><strong>{{ $surat->user->nim_nip }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td>Program Studi</td>
                    <td>:</td>
                    <td>{{ $surat->user->jurusan ?? 'Manajemen Informatika / Teknik Informatika' }}</td>
                </tr>
                <tr>
                    <td>Perguruan Tinggi</td>
                    <td>:</td>
                    <td>STMIK Samarinda</td>
                </tr>
            </table>

            <p class="teks" style="margin-top:10px">
                Mahasiswa yang tersebut di atas <strong>dinyatakan BEBAS</strong> dari segala tanggungan
                peminjaman peralatan dan ruangan Laboratorium Komputer STMIK Samarinda.
            </p>

            <p class="teks">
                Surat keterangan ini diberikan untuk dapat dipergunakan sebagaimana mestinya.
            </p>

            <!-- TANDA TANGAN -->
            <div class="ttd-wrap">
                <div class="ttd-box">
                    <div class="tgl">Samarinda, {{ ($surat->tanggal_disetujui ?? now())->isoFormat('D MMMM YYYY') }}</div>
                    <div class="jbt">Kepala Laboratorium Komputer,</div>
                    <div class="space">
                        <img src="{{ asset('images/stmik-stemple.png') }}" alt="Stempel" class="img-stemple">
                        @if($kepalaLab && $kepalaLab->tanda_tangan)
                        <img src="{{ route('files.serve', $kepalaLab->tanda_tangan) }}" alt="Tanda Tangan" class="img-ttd">
                        @endif
                    </div>
                    <div class="nama">{{ $kepalaLab ? $kepalaLab->name : '...............................' }}</div>
                    <div class="nip">NIP. {{ ($kepalaLab && $kepalaLab->nim_nip) ? $kepalaLab->nim_nip : '..............................' }}</div>
                </div>
            </div>

            <!-- TEMBUSAN -->
            <div class="tembusan">
                <strong>Tembusan:</strong>
                <ol style="margin-left:18px;margin-top:3px">
                    <li>Ketua STMIK Samarinda</li>
                    <li>Arsip</li>
                </ol>
            </div>

        </div>
    </div>

</body>

</html>