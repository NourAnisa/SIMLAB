<?php

namespace App;

use App\Models\User;

class ProfileValidationService
{
    /**
     * Cek apakah profil mahasiswa sudah lengkap
     * 
     * @param User $user
     * @return array ['is_complete' => bool, 'missing_fields' => array]
     */
    public function validateProfileCompletion(User $user): array
    {
        $missing_fields = [];

        // Check Nama Lengkap
        if (empty($user->name)) {
            $missing_fields[] = 'Nama Lengkap';
        }

        // Check NIM
        if (empty($user->nim_nip)) {
            $missing_fields[] = 'NIM (Nomor Induk Mahasiswa)';
        }

        // Check Program Studi / Jurusan
        if (empty($user->jurusan)) {
            $missing_fields[] = 'Program Studi / Jurusan';
        }

        // Check Email
        if (empty($user->email)) {
            $missing_fields[] = 'Email';
        }

        // Check Foto Profil
        if (empty($user->foto_profil)) {
            $missing_fields[] = 'Foto Profil';
        }

        $is_complete = empty($missing_fields);

        return [
            'is_complete' => $is_complete,
            'missing_fields' => $missing_fields,
        ];
    }

    /**
     * Check dan auto-update flag profile_completed
     * 
     * @param User $user
     * @return bool
     */
    public function checkAndUpdateProfileCompletion(User $user): bool
    {
        $validation = $this->validateProfileCompletion($user);

        if ($validation['is_complete']) {
            $user->update(['profile_completed' => true]);
            return true;
        }

        $user->update(['profile_completed' => false]);
        return false;
    }

    /**
     * Cek apakah user bisa akses fitur (untuk mahasiswa)
     * 
     * @param User $user
     * @return array ['can_access' => bool, 'message' => string]
     */
    public function canAccessFeatures(User $user): array
    {
        // Hanya apply ke mahasiswa
        if (!$user->isMahasiswa()) {
            return [
                'can_access' => true,
                'message' => '',
                'missing_fields' => [],
            ];
        }

        // Jika profile sudah lengkap
        if ($user->profile_completed) {
            return [
                'can_access' => true,
                'message' => '',
                'missing_fields' => [],
            ];
        }

        $validation = $this->validateProfileCompletion($user);

        return [
            'can_access' => false,
            'message' => 'Anda harus melengkapi profil terlebih dahulu. Data yang masih kosong: ' . implode(', ', $validation['missing_fields']),
            'missing_fields' => $validation['missing_fields'],
        ];
    }
}
