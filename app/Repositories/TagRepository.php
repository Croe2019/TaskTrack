<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class TagRepository
{
    /**
     * タグ一覧を取得（ユーザーごと or 全体）
     */
    public function getAll()
    {
        return Tag::orderBy('name')->get();
    }

    public function findByName(string $name)
    {
        return Tag::where('name', $name)->first();
    }

     public function getOrCreateTags(array $names): array
    {
        $tagIds = [];
        foreach ($names as $name) {
            $name = trim($name);
            if (!$name) continue;

            $tag = Tag::firstOrCreate(
                ['name' => $name],
                ['user_id' => Auth::id()]
            );

            $tagIds[] = $tag->id;
        }
        return $tagIds;
    }

}
