<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(int $groupChatId)
    {
        $messages = DB::select(
            'SELECT messages.id, messages.data, messages.user_id, messages.created_at, users.name AS user_name
         FROM messages
         JOIN users ON messages.user_id = users.id
         WHERE messages.group_chat_id = ?
         ORDER BY messages.created_at ASC',
            [$groupChatId]
        );

        return response()->json($messages);
    }
    public function store(Request $request, int $groupChatId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $groupChat = DB::select('SELECT * FROM group_chats WHERE id = ?', [$groupChatId]);

        if (empty($groupChat)) {
            abort(404, 'Group chat not found');
        }

        DB::statement(
            'INSERT INTO messages (group_chat_id, user_id, data, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)',
            [$groupChatId, Auth::id(), $request->input('message')]
        );
        return redirect()->route("groupChat.show", [$groupChat[0]->id]);
    }
}
