<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('token:set-expiry {userId} {--d|day=0} {--m|minute=0} {--s|second=0}')]
#[Description('Set the expiration time for the latest token of a specific user')]
class SetTokenExpiryCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->argument('userId');
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

        $expiresAt = now()->addDays((int) $this->option('day'))
            ->addMinutes((int) $this->option('minute'))
            ->addSeconds((int) $this->option('second'));

        $token->update(['expires_at' => $expiresAt]);

        $this->info("Token for user {$userId} set to expire at: {$expiresAt->toDateTimeString()}");

        return self::SUCCESS;
    }
}
