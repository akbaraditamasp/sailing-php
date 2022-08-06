<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $data = [
            [
                "username" => "root",
                "password" => "$2a$12$3fmhp2m5LavctAziGUTgYuHghkTcNx5CNFKVJ.rks4diqr9Cn.Bte",
                "email" => "root@example.com",
            ],
        ];

        $posts = $this->table('users');
        $posts->insert($data)
            ->saveData();
    }
}
