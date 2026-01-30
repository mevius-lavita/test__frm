<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指定した内容でユーザーを作成
        User::create([
            'name' => 'テスト太郎',
            'email' => 'test1@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        User::create([
            'name' => 'テスト花子',
            'email' => 'test2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        User::create([
            'name' => 'テスト次郎',
            'email' => 'test3@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);
    }
}
