<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // ou o namespace correto do seu model User
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@exemplo.com'], // evita duplicar
            [
                'name' => 'Admin',
                'email' => 'ralmeida575@gmail.com',
                'password' => Hash::make('2411@Rapha'), // troque para a senha que quiser
                'is_admin' => true, // se você tiver esse campo na tabela users
            ]
        );
    }
}
