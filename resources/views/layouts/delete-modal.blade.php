<div id="delete-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white w-full max-w-md p-6 rounded shadow">
        <h2 class="text-lg font-bold mb-4">削除確認</h2>

        <p class="mb-6 text-gray-700">
            本当に削除しますか？この操作は取り消せません。
        </p>

        <div class="flex justify-end gap-3">
            <button
                type="button"
                id="delete-cancel"
                class="px-4 py-2 bg-gray-200 rounded">
                キャンセル
            </button>

            <form method="POST" id="delete-form">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded">
                    削除する
                </button>
            </form>
        </div>
    </div>
</div>
