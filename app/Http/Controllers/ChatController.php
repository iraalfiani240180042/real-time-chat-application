<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Group;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // LIST USER
    public function index()
    {
        $users = User::where(
            'id',
            '!=',
            Auth::id()
        )->get();

        $groups = auth()->user()->groups;

        return view('chat.index', compact(
            'users',
            'groups'
        ));
    }

    // PRIVATE CHAT PAGE
    public function chat($id)
    {
        $receiver = User::findOrFail($id);

        $users = User::where(
            'id',
            '!=',
            Auth::id()
        )->get();

        $groups = auth()->user()->groups;

        $messages = Message::where(function ($query) use ($id) {

            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $id);

        })->orWhere(function ($query) use ($id) {

            $query->where('sender_id', $id)
                  ->where('receiver_id', Auth::id());

        })->get();

        return view('chat.index', compact(
            'users',
            'groups',
            'receiver',
            'messages'
        ));
    }

    // GROUP CHAT PAGE
    public function groupChat($id)
    {
        $users = User::where(
            'id',
            '!=',
            Auth::id()
        )->get();

        $groups = auth()->user()->groups;

        $groupReceiver = Group::findOrFail($id);

        $groupMessages = GroupMessage::with('user')
            ->where('group_id', $id)
            ->get();

        return view('chat.index', compact(
            'users',
            'groups',
            'groupReceiver',
            'groupMessages'
        ));
    }

    // SEND PRIVATE MESSAGE
    public function send(Request $request)
    {
        Message::create([

            'sender_id' => Auth::id(),

            'receiver_id' => $request->receiver_id,

            'message' => $request->message,

        ]);

        return response()->json([
            'success' => true
        ]);
    }

    // GET PRIVATE MESSAGES
    public function getMessages($id)
    {
        $messages = Message::where(function ($query) use ($id) {

            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $id);

        })->orWhere(function ($query) use ($id) {

            $query->where('sender_id', $id)
                  ->where('receiver_id', Auth::id());

        })->get();

        return response()->json($messages);
    }

    // SEND GROUP MESSAGE
    public function sendGroupMessage(Request $request)
    {
        GroupMessage::create([

            'group_id' => $request->group_id,

            'user_id' => Auth::id(),

            'message' => $request->message

        ]);

        return response()->json([
            'success' => true
        ]);
    }

    // GET GROUP MESSAGES
    public function getGroupMessages($id)
    {
        $messages = GroupMessage::with('user')
            ->where('group_id', $id)
            ->get();

        return response()->json($messages);
    }
}