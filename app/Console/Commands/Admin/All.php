<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class All extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all admins';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admins = Admin::all();

        $this->table(['ID', 'Name', 'Email'], $admins->map(function ($admin) {
            return [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ];
        })->toArray());

        return CommandAlias::SUCCESS;
    }
}
