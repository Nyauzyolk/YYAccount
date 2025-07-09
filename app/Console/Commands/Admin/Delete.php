<?php

namespace App\Console\Commands\Admin;

use App\Models\Admin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an admin by ID';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 获取管理员ID
        $id = $this->ask('Please enter the admin ID to delete');

        // 搜索
        $admin = Admin::find($id);
        if (is_null($admin)) {
            $this->error('Admin not found with ID: ' . $id);

            return CommandAlias::FAILURE;
        }

        // 输出信息
        $this->table(['ID', 'Name', 'Email'], [
            [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);

        // 确认
        if (! $this->confirm('Are you confirm to delete the admin？')) {
            return CommandAlias::FAILURE;
        }

        // 删除管理员
        Admin::destroy($id);

        // 输出信息
        $this->info('Admin deleted successfully');

        return CommandAlias::SUCCESS;
    }
}
