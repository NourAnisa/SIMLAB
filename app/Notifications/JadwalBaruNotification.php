<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\JadwalSertifikasi;
use Illuminate\Contracts\Queue\ShouldQueue;

class JadwalBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $jadwal;

    public function __construct(JadwalSertifikasi $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    public function via(object $notifiable): array 
    { 
        return ['database', 'mail']; 
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/mahasiswa/sertifikasi');
        $namaSkema = $this->jadwal->skema ? $this->jadwal->skema->nama_skema : 'Sertifikasi';
        $tanggal = $this->jadwal->tanggal ? $this->jadwal->tanggal->format('d M Y') : '-';

        return (new MailMessage)
                    ->subject('Jadwal Sertifikasi Baru Tersedia')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line("Terdapat jadwal baru untuk skema sertifikasi: **{$namaSkema}**.")
                    ->line("Tanggal Pelaksanaan: **{$tanggal}**")
                    ->line("Kuota Tersedia: **{$this->jadwal->kuota} peserta**")
                    ->action('Daftar Sekarang', $url)
                    ->line('Segera daftarkan diri Anda sebelum kuota penuh!');
    }

    public function toDatabase(object $notifiable): array
    {
        $namaSkema = $this->jadwal->skema ? $this->jadwal->skema->nama_skema : 'Sertifikasi';
        return [
            'model'   => 'JadwalSertifikasi',
            'id'      => $this->jadwal->id,
            'status'  => 'baru',
            'message' => "Jadwal sertifikasi baru telah dibuka untuk {$namaSkema}.",
        ];
    }

    public function toArray(object $notifiable): array { return $this->toDatabase($notifiable); }
}
