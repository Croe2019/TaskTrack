<form method="POST"
      action="{{ route('invites.accept', $invite->id) }}"
      class="flex gap-3 items-center">
    @csrf
    @method('PATCH')

    <select name="role"
            class="border rounded px-2 py-1">
        <option value="member">メンバー</option>
        <option value="admin">管理者</option>
    </select>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded">
        承認
    </button>
</form>
