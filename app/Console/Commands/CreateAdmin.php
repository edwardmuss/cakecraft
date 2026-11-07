<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Public registration is disabled, so admins are created here.
 *
 *   php artisan cakecraft:create-admin
 *   php artisan cakecraft:create-admin --name="Jane" --email=jane@example.com --phone=0712345678
 */
class CreateAdmin extends Command
{
    protected $signature = 'cakecraft:create-admin {--name=} {--email=} {--phone=} {--password=} {--branch=Cakecraft Pastry Academy}';
    protected $description = 'Create an admin user (and the branch if none exists)';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Full name');
        $email = $this->option('email') ?: $this->ask('Email');
        $phone = $this->option('phone') ?: $this->ask('Phone');
        $password = $this->option('password') ?: $this->secret('Password (min 8 characters)');

        if (strlen((string) $password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }
        if (User::where('email', $email)->exists()) {
            $this->error("A user with {$email} already exists.");
            return self::FAILURE;
        }

        $branch = Branch::query()->orderBy('id')->first() ?: Branch::create([
            'branch_name'    => $this->option('branch'),
            'branch_email'   => $email,
            'company_phone'  => $phone,
            'isActive'       => 1,
        ]);

        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $role->syncPermissions(Permission::all());

        $user = User::create([
            'name'              => $name,
            'email'             => $email,
            'phone'             => $phone,
            'password'          => Hash::make($password),
            'branch_id'         => $branch->id,
            'role'              => 'super-admin',
            'email_verified_at' => now(),
            'registration_verified_at' => now(),
            'isActive'          => true,
        ]);
        $user->assignRole($role);

        $this->info("Admin {$email} created on branch \"{$branch->branch_name}\". Log in at /login.");
        return self::SUCCESS;
    }
}
