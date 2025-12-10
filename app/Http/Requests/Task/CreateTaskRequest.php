<?php

namespace App\Http\Requests\Task;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',

            'status'       => 'required|string|in:not_started,in_progress,completed',
            'priority'     => 'required|string|in:high,medium,low',

            'deadline'     => 'nullable|date',
            'completed_at' => 'nullable|date_format:Y-m-d\TH:i',

            // 既存タグ
            'tag_ids'      => 'nullable|array',
            'tag_ids.*'    => 'integer|exists:tags,id',

            // 新規タグ
            'tags'         => 'nullable|string',
            // 'attachments'   => 'nullable|array',
            // 'attachments.*' => 'file|max:20480',
        ];
    }


    public function messages()
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'status.in'      => 'ステータスの値が不正です。',
            'priority.in'    => '優先度の値が不正です。',
        ];
    }

    /**
     * ヘルパー：tags を配列で取得（空なら空配列）
     */
    public function tagNames(): array
    {
        $raw = $this->input('tags', '');
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $names = array_map('trim', explode(',', $raw));
        // 空要素を削除し重複を排除
        $names = array_filter($names, fn($v) => $v !== '');
        $names = array_values(array_unique($names));

        return $names;
    }

    /**
     * 既存 tag_ids を安全に配列で返す
     */
    public function tagIds(): array
    {
        $ids = $this->input('tag_ids', []);
        if (is_null($ids)) return [];
        if (!is_array($ids)) {
            // まれに単体値で来る場合に備える
            return [(int)$ids];
        }
        return array_map('intval', $ids);
    }
}
