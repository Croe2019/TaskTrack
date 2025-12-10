<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            '急ぎ', '重要', 'バグ修正', '調査', 'デザイン',
            'フロント', 'バックエンド', '資料作成', 'ミーティング', 'コードレビュー'
        ];

        foreach ($tags as $name) {
            Tag::create(['name' => $name]);
        }
    }
}
