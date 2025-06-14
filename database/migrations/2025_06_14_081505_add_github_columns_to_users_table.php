<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGithubColumnsToUsersTable extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // 添加 GitHub 认证所需字段
            $table->string('github_id')->nullable()->unique()->after('id');
            $table->text('github_token')->nullable()->after('password');
            $table->text('github_refresh_token')->nullable()->after('github_token');
            $table->timestamp('github_token_expires_at')->nullable()->after('github_refresh_token');
            $table->timestamp('last_login_at')->nullable()->after('github_token_expires_at');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // 回滚时删除添加的字段
            $table->dropColumn([
                'github_id',
                'github_token',
                'github_refresh_token',
                'github_token_expires_at',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};
