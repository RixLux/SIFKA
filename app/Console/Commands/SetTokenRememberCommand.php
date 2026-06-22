<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('token:set-remember {userId} {value=true : Whether to set remember (true/false)}')]
#[Description('Set token expiration to 3 days (true) or null (false) for a user')]
class SetTokenRememberCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->argument('userId');
        $value = filter_var($this->argument('value'), FILTER_VALIDATE_BOOLEAN);

        $user = User::find($userId);

        if (! $user) {
            $this->error("User with ID {$userId} not found.");

            return self::FAILURE;
        }

        $token = $user->tokens()->latest()->first();

        if (! $token) {
            $this->error("No tokens found for user {$userId}.");

            return self::FAILURE;
        }

        $expiresAt = $value ? now()->addDays(3) : null;

        $token->update(['expires_at' => $expiresAt]);

        $status = $expiresAt ? $expiresAt->toDateTimeString() : 'Never (null)';
        $this->info("Token for user {$userId} set to expire at: {$status}");

        return self::SUCCESS;
    }
}
