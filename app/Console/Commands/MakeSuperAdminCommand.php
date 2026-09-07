<?php

namespace App\Console\Commands;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MakeSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topnews:make-super-admin {--name= : The name of the super administrator} {--username= : The username for the super administrator} {--email= : The email of the super administrator} {--password= : The password for the super administrator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a new Super Administrator account for TopNews';

    /**
     * Execute the console command.
     */
    public function handle(AuditLogService $auditLogger): int
    {
        $this->info('=== TopNews Super Admin Provisioning ===');

        $name = $this->option('name') ?: $this->ask('Full Name');
        $username = $this->option('username') ?: $this->ask('Username (e.g. zakky.mubaraq)');
        $email = $this->option('email') ?: $this->ask('Email Address');
        $password = $this->option('password') ?: $this->secret('Password (min 8 characters)');

        $existingUser = User::where('username', Str::lower(trim($username)))
            ->orWhere('email', Str::lower(trim($email)))
            ->first();

        $usernameRule = ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/'];
        if (! $existingUser || $existingUser->username !== Str::lower(trim($username))) {
            $usernameRule[] = 'unique:users,username';
        }

        $emailRule = ['required', 'email', 'max:150'];
        if (! $existingUser || $existingUser->email !== Str::lower(trim($email))) {
            $emailRule[] = 'unique:users,email';
        }

        $validator = Validator::make([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:100'],
            'username' => $usernameRule,
            'email' => $emailRule,
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if (! $superAdminRole) {
            $this->error('The super_admin role does not exist. Run php artisan db:seed first.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['username' => Str::lower(trim($username))],
            [
                'uuid' => (string) Str::uuid(),
                'name' => trim($name),
                'email' => Str::lower(trim($email)),
                'password' => Hash::make($password),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($superAdminRole);

        $auditLogger->log(
            action: 'user.provisioned_super_admin',
            description: "Super Admin [{$user->email}] provisioned via CLI",
            entityType: User::class,
            entityId: $user->id,
            user: $user
        );

        $this->info("✓ Super Admin [{$user->name} <{$user->email}>] has been created and activated successfully.");

        return self::SUCCESS;
    }
}
