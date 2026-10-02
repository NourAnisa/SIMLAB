<?php
namespace App\Providers;

use App\Models\LogAktivitas;
use Illuminate\Database\Eloquent\Model;

class ServiceObserver
{
    protected function log(Model $model, string $action): void
    {
        $userId = $model->user_id ?? auth()->id();
        if (!$userId) return;

        $class = class_basename($model);
        LogAktivitas::create([
            'user_id'   => $userId,
            'aktivitas' => "{$action} {$class} #" . $model->getKey(),
            'waktu'     => now(),
        ]);
    }

    public function created(Model $model): void { $this->log($model, 'Membuat'); }
    public function updated(Model $model): void { $this->log($model, 'Memperbarui'); }
}
