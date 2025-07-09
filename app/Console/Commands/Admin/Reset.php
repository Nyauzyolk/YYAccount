<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Reset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset an admin password by ID';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get admin ID
        $id = $this->ask('Please enter the admin ID');

        // 获取管理员
        $admin = (new Admin)->find($id);

        // 验证管理员
        if (is_null($admin)) {
            $this->error('Admin not found with ID: ' . $id);

            return CommandAlias::FAILURE;
        }

        // 密码
        $password = $this->secret('Please enter the new password');

        $admin->password = bcrypt($password);
        $admin->save();

        // 输出信息
        $this->info('Admin password reset successfully.');

        return CommandAlias::SUCCESS;
    }
}
