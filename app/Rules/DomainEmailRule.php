<?php

namespace App\Rules;

use Closure;
use App\Models\SiteSetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DomainEmailRule implements ValidationRule
{
    protected $role;

    public function __construct(string $role = 'mahasiswa')
    {
        $this->role = $role;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $setting = SiteSetting::first();
        if (!$setting) return;

        $domain = null;
        if ($this->role === 'mahasiswa') {
            $domain = $setting->domain_email_mahasiswa;
        } else {
            $domain = $setting->domain_email_pegawai;
        }

        if (empty($domain)) return;

        // Clean up the domain (e.g. remove '@' if user typed it in settings)
        $domain = ltrim($domain, '@');

        if (!str_ends_with(strtolower($value), '@' . strtolower($domain))) {
            $fail("Domain email harus @{$domain}");
        }
    }
}
