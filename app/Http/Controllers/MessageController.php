<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // =========================
    // DASHBOARD CHAT
    // =========================
    public function index(User $user = null)
    {
        // ambil semua user selain diri sendiri
        $users = User::where('id', '!=', Auth::id())->get();

        // ambil semua group user login
        $groups = Auth::user()->groups;

        $messages = collect();

        // kalau user dipilih
        if ($user) {

            // cari conversation
            $conversation = Conversation::where(function ($query) use ($user) {

                $query->where('user_one', Auth::id())
                      ->where('user_two', $user->id);

            })->orWhere(function ($query) use ($user) {

                $query->where('user_one', $user->id)
                      ->where('user_two', Auth::id());

            })->first();

            // kalau conversation ada
            if ($conversation) {

                $messages = Message::where('conversation_id', $conversation->id)
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return view('dashboard', compact(
            'users',
            'user',
            'messages',
            'groups'
        ));
    }

    // =========================
    // SEND MESSAGE
    // =========================
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $receiverId = $request->receiver_id;
        $authId = Auth::id();

        // ❌ cegah chat diri sendiri
        if ($authId == $receiverId) {
            return back()->with('error', 'Tidak bisa chat diri sendiri');
        }

        // 🔍 cari conversation
        $conversation = Conversation::where(function ($query) use ($authId, $receiverId) {

            $query->where('user_one', $authId)
                  ->where('user_two', $receiverId);

        })->orWhere(function ($query) use ($authId, $receiverId) {

            $query->where('user_one', $receiverId)
                  ->where('user_two', $authId);

        })->first();

        // 🆕 buat conversation baru
        if (!$conversation) {

            $conversation = Conversation::create([
                'user_one' => $authId,
                'user_two' => $receiverId,
            ]);
        }

        // 💬 simpan message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $authId,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    // =========================
    // REALTIME GET MESSAGES
    // =========================
    public function getMessages(User $user)
    {
        // cari conversation
        $conversation = Conversation::where(function ($query) use ($user) {

            $query->where('user_one', Auth::id())
                  ->where('user_two', $user->id);

        })->orWhere(function ($query) use ($user) {

            $query->where('user_one', $user->id)
                  ->where('user_two', Auth::id());

        })->first();

        // kalau belum ada conversation
        if (!$conversation) {
            return response()->json([]);
        }

        // ambil messages
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }
}