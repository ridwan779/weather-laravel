<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Jobs\SendEmail;

use App\Models\User;

#[Signature('email:send {user : email user}')]
#[Description('Sending email welcome to user')]
class SendEmailDispatch extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->argument('user');

        $user = User::where('email', $input)->first();

        if (!$user) {
            $this->error("Email not found");
            return self::FAILURE;
        }

        try {
            SendEmail::dispatchSync($user);
        } catch (\Throwable $e) {
            $this->error('Failed job: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Done');
    }
}
