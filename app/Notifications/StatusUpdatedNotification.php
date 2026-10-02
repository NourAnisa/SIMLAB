<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class StatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $modelName, public string $status, public int $modelId) {}

    public function via(object $notifiable): array 
    { 
        return ['database', 'mail']; 
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/mahasiswa/dashboard'); // Bisa disesuaikan dengan rute yang tepat

        return (new MailMessage)
                    ->subject('Pembaruan Status SIMLAB')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line("Terdapat pembaruan pada pengajuan {$this->modelName} Anda.")
                    ->line("Status pengajuan Anda saat ini adalah: **" . strtoupper($this->status) . "**")
                    ->action('Cek Dashboard', $url)
                    ->line('Terima kasih telah menggunakan layanan SIMLAB STMIK.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'model'   => $this->modelName,
            'id'      => $this->modelId,
            'status'  => $this->status,
            'message' => "Status {$this->modelName} #{$this->modelId} berubah menjadi {$this->status}.",
        ];
    }

    public function toArray(object $notifiable): array { return $this->toDatabase($notifiable); }
}
