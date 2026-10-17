<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('cms:admin {email : Email address of the administrator} {--name= : Display name}')]
#[Description('Create an administrator account (or give an existing account admin access)')]
class CmsAdmin extends Command
{
    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $user = User::query()->firstWhere('email', $email);

        $name = $this->option('name') ?: ($user?->name ?? $this->ask('Name'));
        $password = $this->secret($user ? 'New password (leave empty to keep the current one)' : 'Password (at least 10 characters, letters and numbers)');

        $validator = Validator::make(compact('email', 'name', 'password'), [
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:120'],
            'password' => [$user ? 'nullable' : 'required', Password::min(10)->letters()->numbers()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user ??= new User(['email' => $email]);
        $user->name = $name;
        if ($password) {
            $user->password = $password;
        }
        $user->is_admin = true;
        $user->save();

        $this->info("{$email} can now sign in at ".url('/admin/login'));

        return self::SUCCESS;
    }
}
