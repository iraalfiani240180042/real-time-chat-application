<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // =========================
    // GROUP CHAT PAGE
    // =========================
    public function show(Group $group)
    {
        // semua user selain diri sendiri
        $users = User::where('id', '!=', Auth::id())->get();

        // semua group milik user login
        $groups = Auth::user()->groups;

        // ambil semua pesan group
        $messages = GroupMessage::where('group_id', $group->id)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('dashboard', compact(
            'users',
            'groups',
            'group',
            'messages'
        ));
    }

    // =========================
    // SEND GROUP MESSAGE
    // =========================
    public function send(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'message' => 'required|string'
        ]);

        GroupMessage::create([
            'group_id' => $request->group_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return back();
    }
}