<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature = 'app:make-admin {username} {--password=} {--email=}';
    protected $description = 'Create or promote a user to admin';

    public function handle(): int
    {
        $username = $this->argument('username');
        $email = $this->option('email') ?: null;
        $password = $this->option('password');
        if (!$password) {
            $password = bin2hex(random_bytes(6));
            $this->info('Generated password: '.$password);
        }
        $user = User::whereRaw('LOWER(username) = ?', [strtolower($username)])->first();
        if (!$user) {
            $user = User::create([
                'name' => $username,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'pin_hash' => Hash::make('0000'),
            ]);
            $this->info('User created.');
        }
        $user->is_admin = true;
        $user->save();
        $this->info('User "'.$username.'" is now an admin.');
        return self::SUCCESS;
    }
}



