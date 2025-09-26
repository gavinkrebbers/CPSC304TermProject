<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GroupChatController extends Controller
{
    public function store(string $name)
    {
        DB::statement(
            'INSERT INTO group_chats (name, created_by, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)',
            [$name, Auth::id()]
        );

        $groupChatId = DB::getPdo()->lastInsertId();

        DB::statement(
            'INSERT INTO group_user (group_chat_id, user_id, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)',
            [$groupChatId, Auth::id()]
        );

        return redirect()->route("groupChat.show", ["id" => $groupChatId]);
    }

    public function show(int $id)
    {
        $groupChat = DB::select('SELECT * FROM group_chats WHERE id = ?', [$id]);

        if (empty($groupChat)) {
            abort(404, 'Group chat not found');
        }

        $messages = DB::select(
            'SELECT messages.id, messages.data, messages.group_chat_id, messages.user_id, messages.created_at,
                    users.name AS user_name
             FROM messages
             JOIN users ON messages.user_id = users.id
             WHERE messages.group_chat_id = ?
             ORDER BY messages.created_at ASC',
            [$id]
        );

        $groupChat[0]->messages = $messages;

        return Inertia::render('GroupChats/ShowGroupChat', ['groupChat' => $groupChat[0]]);
    }

    public function index()
    {
        $userId = Auth::id();

        $chatsIn = DB::select(
            'SELECT group_chats.id, group_chats.name, group_chats.created_by, group_chats.created_at, users.name AS creator_name
            FROM group_chats
            JOIN group_user ON group_chats.id = group_user.group_chat_id
            JOIN users ON group_chats.created_by = users.id
            WHERE group_user.user_id = ?
            ORDER BY group_chats.created_at DESC',
            [$userId]
        );

        $chatsNotIn = DB::select(
            'SELECT group_chats.id, group_chats.name, group_chats.created_by, group_chats.created_at, users.name AS creator_name
            FROM group_chats
            JOIN users ON group_chats.created_by = users.id
            WHERE group_chats.id NOT IN (
            SELECT group_chat_id FROM group_user WHERE user_id = ?
            )
            ORDER BY group_chats.created_at DESC',
            [$userId]
        );


        return Inertia::render("GroupChats/ListGroupChats", [
            'chatsIn' => $chatsIn,
            'chatsNotIn' => $chatsNotIn,
        ]);
    }

    public function join(int $id)
    {
        $groupChat = DB::select(
            'SELECT * FROM group_chats where id = ?',
            [$id]
        );
        $groupChatId = $groupChat[0]->id;
        DB::statement(
            'INSERT INTO group_user(group_chat_id, user_id, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)',
            [$groupChatId, Auth::id()]
        );
    }

    public function leave(int $id)
    {
        DB::statement(
            'DELETE FROM group_user WHERE group_chat_id = ? AND user_id = ?',
            [$id, Auth::id()]
        );
    }

    public function create()
    {
        return Inertia::render("GroupChats/CreateGroupChat");
    }
}
