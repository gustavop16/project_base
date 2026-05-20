<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function prepareData(array $data, ?string $plainPassword = null): array
    {
        $data['password'] = Hash::make($plainPassword ?? $data['password'] ?? Str::random(10));
        return $data;
    }

    public function generatePassword(): string
    {
        return Str::random(10);
    }

    public function prepareDataPassword(array $data): array
    {
        if (isset($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        }
        return $data;
    }
}
