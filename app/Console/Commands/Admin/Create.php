<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 名称
        $name = $this->ask('Please enter the username.');

        // 邮箱
        $email = $this->ask('Please enter the e-mail.');

        // 密码
        $password = $this->secret('Please enter the password.');
        
        // 确认密码
        $password_confirmation = $this->secret('Please confirm the password.');

        // 验证密码
        if ($password !== $password_confirmation) {
            $this->error('The passwords do not match.');

            return CommandAlias::FAILURE;
        }

        // 创建管理员
        $admin = (new Admin)->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        // 输出信息
        $this->info('管理员创建成功，ID 为: '.$admin->id.'。');

        return CommandAlias::SUCCESS;
    }
}
