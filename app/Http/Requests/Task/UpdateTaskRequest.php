<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'status' => 'required|string|in:not_started,in_progress,completed',
            'priority'     => 'required|string|in:high,medium,low',
            'deadline'     => 'nullable|date',
            'completed_at' => 'nullable|date_format:Y-m-d\TH:i',
            // タグは後で作成する
            // 'tags'         => 'nullable|array',
            // 'tags.*'       => 'string|max:50',
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
}
