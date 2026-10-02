<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat {{ $pendaftaran->jadwal->skema->nama_skema }} &mdash; {{ $pendaftaran->user->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,800;1,400;1,700&family=EB+Garamond:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #94a3b8;
            font-family: 'Lato', sans-serif;
            color: #1e293b;
        }

        /* ====== PRINT BAR ====== */
        .print-bar {
            position: fixed; top: 0; left: 0; right: 0;
            background: #0f172a; color: #fff;
            padding: 10px 24px;
            display: flex; align-items: center; gap: 12px;
            z-index: 999; font-family: 'Lato', sans-serif; font-size: 14px;
        }
        .print-bar span { flex: 1; }
        .btn-p { padding: 8px 22px; background: #4f46e5; color: #fff; border: none; border-radius: 7px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; }
        .btn-b { padding: 8px 16px; background: transparent; color: #94a3b8; border: 1px solid #475569; border-radius: 7px; font-size: 14px; cursor: pointer; font-family: inherit; text-decoration: none; }

        /* ====== PAGE A4 LANDSCAPE ====== */
        .page {
            width: 297mm;
            height: 210mm;
            margin: 0 auto;
            background: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 40px rgba(0,0,0,.3);
        }

        /* Double border frame */
        .frame-outer { position: absolute; inset: 6px; border: 2.5px solid #1e293b; pointer-events: none; z-index: 10; }
        .frame-inner { position: absolute; inset: 10px; border: 1px solid #1e293b; pointer-events: none; z-index: 10; }

        /* ====== WATERMARK ====== */
        .wm {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 64pt; font-weight: 900;
            color: rgba(30,41,59,.04);
            white-space: nowrap; pointer-events: none; z-index: 0;
            letter-spacing: 10px; font-family: 'Playfair Display', serif;
        }

        /* ====== MAIN WRAPPER ====== */
        .cert-wrap {
            position: relative; z-index: 2;
            padding: 9mm 14mm 7mm;
            height: 210mm;
            display: flex; flex-direction: column;
        }

        /* ====== KOP ====== */
        .kop {
            display: flex; align-items: center; gap: 14px;
            padding-bottom: 6px;
            border-bottom: 3px solid #1e293b;
        }
        .kop-logo { width: 88px; height: 88px; object-fit: contain; flex-shrink: 0; }
        .kop-text { flex: 1; text-align: center; line-height: 1.25; }
        .k1 { font-size: 8.5pt; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: #1e293b; font-family: 'Lato', sans-serif; }
        .k2 { font-size: 17pt; font-weight: 900; text-transform: uppercase; letter-spacing: .5px; font-family: 'Lato', sans-serif; color: #0f172a; line-height: 1.1; }
        .k3 { font-size: 11pt; font-weight: 700; color: #1e293b; font-family: 'Lato', sans-serif; }
        .k4 { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        .kop-sub-line {
            text-align: center; font-size: 7.5pt; color: #64748b;
            font-style: italic; padding: 3px 0; letter-spacing: .5px;
            border-bottom: 1px solid #cbd5e1; margin-bottom: 0;
        }

        /* ====== CERT TITLE ====== */
        .cert-title { text-align: center; padding: 3px 0 2px; }
        .cert-title .pre-title {
            font-size: 8.5pt; letter-spacing: 4px; text-transform: uppercase;
            color: #64748b; font-style: italic; font-family: 'EB Garamond', serif;
        }
        .cert-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 44pt; font-weight: 800; font-style: italic;
            color: #0f172a; line-height: 1; letter-spacing: 1px;
        }
        .cert-title .cert-no {
            font-size: 10pt; color: #4f46e5; letter-spacing: .5px;
            margin-top: 0; font-weight: 700; font-family: 'Lato', sans-serif;
        }
        .cert-divider {
            display: flex; align-items: center; gap: 0;
            margin: 3px 0 0;
        }
        .cert-divider-line { flex: 1; height: 1px; background: #1e293b; }
        .cert-divider-diamond { width: 7px; height: 7px; background: #1e293b; transform: rotate(45deg); margin: 0 8px; }

        /* ====== BODY ====== */
        .cert-body {
            display: flex; gap: 14px; align-items: flex-start;
            flex: 1; margin-top: 6px;
        }

        /* FOTO */
        .photo-col { flex-shrink: 0; text-align: center; width: 82px; }
        .photo-frame {
            width: 74px; height: 94px;
            border: 2px solid #1e293b;
            overflow: hidden; margin: 0 auto; position: relative;
        }
        .photo-frame img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .photo-init {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background: #f1f5f9; font-size: 22pt; font-weight: 700;
            color: #1e293b; font-family: 'Playfair Display', serif;
        }
        .photo-caption { font-size: 6.5pt; color: #94a3b8; margin-top: 4px; letter-spacing: 1px; text-transform: uppercase; font-weight: 700; }

        /* IDENTITAS */
        .iden-col { flex: 1; }
        .present-text { font-size: 9.5pt; color: #475569; font-style: italic; font-family: 'EB Garamond', serif; margin-bottom: 1px; }
        .recipient-name {
            font-family: 'Playfair Display', serif;
            font-size: 22pt; font-weight: 700; color: #0f172a;
            line-height: 1; margin-bottom: 6px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 3px; display: inline-block;
        }
        .iden-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; line-height: 1.55; font-family: 'EB Garamond', serif; }
        .iden-table td { padding: 0.5px 0; vertical-align: middle; }
        .iden-table td:first-child { width: 105px; color: #475569; font-style: italic; }
        .iden-table td:nth-child(2) { width: 14px; color: #475569; }
        .iden-table td:last-child { font-weight: 700; color: #0f172a; font-family: 'Lato', sans-serif; font-size: 9.5pt; }
        .skema-inline { display: inline-flex; align-items: center; gap: 6px; }
        .skema-inline-icon { font-size: 10pt; }

        /* STATUS */
        .status-area { margin: 6px 0 4px; display: flex; align-items: center; gap: 0; flex-wrap: nowrap; }
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 14px; font-size: 9.5pt; font-weight: 700;
            font-family: 'Lato', sans-serif; letter-spacing: .5px;
            border: 2px solid;
        }
        .pill-lulus { border-color: #15803d; color: #15803d; background: #f0fdf4; }
        .pill-tidak { border-color: #b91c1c; color: #b91c1c; background: #fef2f2; }
        .nilai-pill {
            display: inline-flex; align-items: stretch; gap: 0;
            border: 2px solid #1e293b; border-left: none; overflow: hidden;
        }
        .nilai-num { font-size: 11pt; font-weight: 900; color: #fff; font-family: 'Lato', sans-serif; line-height: 1; padding: 4px 10px; background: #1e293b; }
        .nilai-sep { width: 1px; background: #475569; }
        .nilai-lbl { font-size: 8pt; font-weight: 900; color: #1e293b; letter-spacing: .5px; padding: 4px 10px; font-family: 'Lato', sans-serif; background: #f8fafc; display: flex; align-items: center; }

        .cert-text-bottom { font-size: 9pt; color: #374151; font-style: italic; line-height: 1.4; font-family: 'EB Garamond', serif; }
        .cert-text-bottom strong { color: #0f172a; font-style: normal; font-weight: 700; font-family: 'Lato', sans-serif; }

        .grade-row { display: flex; gap: 5px; margin-top: 5px; flex-wrap: wrap; }
        .grade-tag {
            padding: 1.5px 8px; font-size: 7pt; font-weight: 400;
            border: 1px solid #cbd5e1; color: #475569;
            background: #fff; letter-spacing: .3px; font-family: 'EB Garamond', serif;
        }

        /* ====== SKEMA LOGO (RIGHT) ====== */
        .skema-col { flex-shrink: 0; width: 140px; text-align: center; display: flex; flex-direction: column; align-items: center; }
        .skema-logo-wrap {
            width: 136px; padding: 10px;
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 4px; margin-bottom: 0;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            min-height: 116px;
        }
        .skema-logo-wrap img { width: 100px; height: 100px; object-fit: contain; }
        .skema-logo-placeholder {
            width: 100px; height: 100px;
            border-radius: 50%; background: linear-gradient(135deg, #1e293b, #334155);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto;
        }
        .cert-exam-banner {
            width: 136px;
            background: #1e3a5f;
            color: #fff; text-align: center;
            padding: 5px 8px;
            font-size: 8.5pt; font-weight: 900;
            letter-spacing: 2px; text-transform: uppercase;
            font-family: 'Lato', sans-serif;
        }

        /* ====== TTD + QR FOOTER ====== */
        .cert-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 4px; }
        .qr-box { text-align: center; }
        .qr-box img { width: 72px; height: 72px; display: block; margin: 0 auto 3px; }
        .qr-box small { font-size: 6.5pt; color: #94a3b8; display: block; letter-spacing: .3px; }
        .ttd-box { text-align: center; min-width: 190px; }
        .ttd-city { font-size: 9.5pt; color: #475569; font-style: italic; margin-bottom: 1px; font-family: 'EB Garamond', serif; }
        .ttd-jabatan { font-size: 10pt; font-weight: 700; margin-bottom: 2px; color: #0f172a; font-family: 'Lato', sans-serif; }
        .ttd-space { height: 76px; position: relative; width: 190px; margin: 0 auto; }
        .img-stemple { width: 74px; height: 74px; object-fit: contain; position: absolute; opacity: .75; left: 8px; top: 0; transform: rotate(-10deg); }
        .img-ttd { max-width: 118px; max-height: 54px; object-fit: contain; position: absolute; left: 50%; top: 50%; transform: translate(-40%, -50%); }
        .ttd-line { width: 155px; height: 1.5px; background: #1e293b; margin: 2px auto 3px; }
        .ttd-name { font-size: 11.5pt; font-weight: 700; font-family: 'Playfair Display', serif; color: #0f172a; }
        .ttd-nip { font-size: 8.5pt; color: #64748b; margin-top: 1px; }

        /* ====== PRINT ====== */
        @media print {
            .print-bar { display: none !important; }
            body { background: #fff; }
            .page { margin: 0; box-shadow: none; }
            @page { size: A4 landscape; margin: 0; }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <span>&#127942; &nbsp;Sertifikat &mdash; {{ $pendaftaran->user->name }}</span>
    <a href="{{ route('mahasiswa.sertifikasi.riwayat') }}" class="btn-b">&larr; Kembali</a>
    <button class="btn-p" onclick="window.print()">&#128424; &nbsp;Cetak / Simpan PDF</button>
</div>

<div style="height:52px"></div>

@php
    $skema = $pendaftaran->jadwal->skema;
    $words = preg_split('/\s+/', trim($skema->nama_skema));
    $kodeSkema = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1), $words)));
    $hasSkemaLogo = $skema->logo && file_exists(storage_path('app/public/'.$skema->logo));
    $g = $pendaftaran->nilai !== null
        ? ($pendaftaran->nilai >= 86 ? 'Excellent'
            : ($pendaftaran->nilai >= 70 ? 'Good'
            : ($pendaftaran->nilai >= 55 ? 'Average' : 'Poor')))
        : null;
@endphp

<div class="page">
    <div class="frame-outer"></div>
    <div class="frame-inner"></div>

    @if($pendaftaran->status === 'tidak_lulus')
    <div class="wm">TIDAK LULUS</div>
    @endif

    <div class="cert-wrap">

        {{-- KOP --}}
        <div class="kop">
            <img src="{{ asset('images/stmik-logo.png') }}" alt="Logo STMIK" class="kop-logo">
            <div class="kop-text">
                <div class="k1">Sekolah Tinggi</div>
                <div class="k2">Manajemen Informatika dan Komputer Samarinda</div>
                <div class="k3">(STMIK Samarinda)</div>
                <div class="k4">Jl. Banggeris No.075 RT.03, Kel. Karang Anyar, Kec. Sungai Kunjang, Kota Samarinda &nbsp;|&nbsp; Telp (+62)82353026237 &nbsp;|&nbsp; stmik.smd75@gmail.com</div>
            </div>
        </div>
        <div class="kop-sub-line">Laboratorium Komputer &mdash; STMIK Samarinda</div>

        {{-- CERT TITLE --}}
        <div class="cert-title">
            <div class="pre-title">Laboratorium Komputer Menyatakan Bahwa</div>
            <h1>Sertifikat</h1>
            <div class="cert-no">No. CERT/LAB/{{ $kodeSkema }}/{{ str_pad($pendaftaran->id, 4, '0', STR_PAD_LEFT) }}/{{ $pendaftaran->updated_at->format('Y') }}</div>
        </div>
        <div class="cert-divider">
            <div class="cert-divider-line"></div>
            <div class="cert-divider-diamond"></div>
            <div class="cert-divider-line"></div>
        </div>

        {{-- BODY --}}
        <div class="cert-body">

            {{-- FOTO --}}
            <div class="photo-col">
                <div class="photo-frame">
                    @if($pendaftaran->user->foto_profil)
                    <img src="{{ route('files.serve', $pendaftaran->user->foto_profil) }}" alt="Foto">
                    @else
                    <div class="photo-init">{{ strtoupper(mb_substr($pendaftaran->user->name, 0, 1)) }}</div>
                    @endif
                </div>
                <div class="photo-caption">Pas Foto</div>
            </div>

            {{-- IDENTITAS --}}
            <div class="iden-col">
                <div class="present-text">Yang namanya tercantum di bawah ini:</div>
                <div class="recipient-name">{{ $pendaftaran->user->name }}</div>

                <table class="iden-table">
                    <tr>
                        <td>NIM</td><td>:</td>
                        <td>{{ $pendaftaran->user->nim_nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td><td>:</td>
                        <td>{{ $pendaftaran->user->jurusan ?? 'Manajemen Informatika' }}</td>
                    </tr>
                    <tr>
                        <td>Perguruan Tinggi</td><td>:</td>
                        <td>STMIK Samarinda</td>
                    </tr>
                    <tr>
                        <td>Skema Uji</td><td>:</td>
                        <td>{{ $skema->nama_skema }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Ujian</td><td>:</td>
                        <td>{{ $pendaftaran->jadwal->tanggal->isoFormat('D MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <td>Nilai</td><td>:</td>
                        <td>
                            @if($pendaftaran->nilai !== null)
                            <span style="display:inline-flex;align-items:stretch;overflow:hidden;border:1.5px solid #1e293b;margin-top:1px;">
                                <span style="background:#1e293b;color:#fff;font-size:10pt;font-weight:900;padding:2px 10px;font-family:'Lato',sans-serif;">{{ $pendaftaran->nilai }}</span>
                                <span style="background:#f8fafc;color:#1e293b;font-size:8pt;font-weight:700;padding:2px 9px;letter-spacing:.5px;font-family:'Lato',sans-serif;display:flex;align-items:center;">{{ $g ? strtoupper($g) : '' }}</span>
                            </span>
                            @else
                            <span>-</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="status-area">
                    @if($pendaftaran->status === 'lulus')
                    <span class="status-pill pill-lulus">&#10003; &nbsp;DINYATAKAN LULUS</span>
                    @else
                    <span class="status-pill pill-tidak">&#10007; &nbsp;TIDAK LULUS</span>
                    @endif
                </div>

                <div class="cert-text-bottom">
                    Telah mengikuti dan menyelesaikan uji kompetensi <strong>{{ $skema->nama_skema }}</strong> yang diselenggarakan oleh Laboratorium Komputer STMIK Samarinda.
                </div>

                <div class="grade-row">
                    <span class="grade-tag">&#9670; Excellent &nbsp;86 &ndash; 100</span>
                    <span class="grade-tag">&#9670; Good &nbsp;70 &ndash; 85</span>
                    <span class="grade-tag">&#9670; Average &nbsp;55 &ndash; 69</span>
                    <span class="grade-tag">&#9670; Poor &nbsp;0 &ndash; 54</span>
                </div>
            </div>

            {{-- SKEMA LOGO (RIGHT) --}}
            <div class="skema-col">
                <div class="skema-logo-wrap">
                    @if($hasSkemaLogo)
                    <img src="{{ route('storage.public', ['path' => $skema->logo]) }}" alt="{{ $skema->nama_skema }}">
                    @else
                    <div class="skema-logo-placeholder">
                        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M8 12l3 3 5-5"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="cert-exam-banner">Certification Exam</div>
            </div>

        </div>

        {{-- FOOTER TTD + QR --}}
        <div class="cert-footer">

            {{-- QR CODE --}}
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=144x144&data={{ urlencode(route('sertifikat.verifikasi', $pendaftaran->id)) }}" alt="QR Verifikasi">
                <small>Scan untuk verifikasi</small>
            </div>
            <div class="ttd-box">
                <div class="ttd-city">Samarinda, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</div>
                <div class="ttd-jabatan">Kepala Laboratorium Komputer,</div>
                <div class="ttd-space">
                    <img src="{{ asset('images/stmik-stemple.png') }}" alt="Stempel" class="img-stemple">
                    @if($kepalaLab && $kepalaLab->tanda_tangan)
                    <img src="{{ route('files.serve', $kepalaLab->tanda_tangan) }}" alt="Tanda Tangan" class="img-ttd">
                    @endif
                </div>
                <div class="ttd-line"></div>
                <div class="ttd-name">{{ $kepalaLab ? $kepalaLab->name : '...............................' }}</div>
                <div class="ttd-nip">NIP. {{ ($kepalaLab && $kepalaLab->nim_nip) ? $kepalaLab->nim_nip : '.......................' }}</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
