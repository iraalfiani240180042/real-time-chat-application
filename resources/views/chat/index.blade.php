<!DOCTYPE html>
<html>
<head>
    <title>Chat App</title>
    @vite('resources/css/app.css')
</head>
<body>

<div class="flex h-screen">

    <!-- SIDEBAR -->
    <div class="w-1/4 bg-gray-900 text-white p-4 overflow-y-auto">

        <h1 class="text-3xl font-bold mb-6">
            Chats
        </h1>

        <!-- USERS -->
        <div id="users-list">

            @foreach($users as $user)

                <a href="/chat/{{ $user->id }}"
                   class="block p-3 bg-gray-800 rounded mb-2">

                    <div class="font-semibold text-lg">

                        {{ $user->name }}

                    </div>

                </a>

            @endforeach

        </div>

        <!-- GROUPS -->
        <div class="mt-6">

            <h2 class="text-xl font-bold mb-3">
                Groups
            </h2>

            @foreach($groups as $group)

                <a href="/chat/group/{{ $group->id }}"
                   class="block p-3 bg-blue-800 rounded mb-2">

                    <div class="font-semibold text-lg">

                        {{ $group->name }}

                    </div>

                </a>

            @endforeach

        </div>

        <a href="/logout"
           class="block mt-5 bg-red-500 text-center p-2 rounded">

            Logout

        </a>

    </div>

    <!-- CHAT -->
    <div class="flex-1 flex flex-col">

        <!-- PRIVATE CHAT -->
        @isset($receiver)

            <!-- HEADER -->
            <div class="bg-gray-200 p-4 border-b">

                <h1 class="text-2xl font-bold">
                    {{ $receiver->name }}
                </h1>

                <p id="receiver-status"
                   class="text-sm text-gray-600">

                    {{ $receiver->is_online
                        ? 'Online'
                        : 'Offline' }}

                </p>

            </div>

            <!-- MESSAGE AREA -->
            <div id="chat-box"
                 class="flex-1 overflow-y-auto p-4 bg-gray-100">

                @foreach($messages as $message)

                    <div class="mb-3
                        {{ $message->sender_id == auth()->id()
                            ? 'text-right'
                            : 'text-left' }}">

                        <div class="inline-block px-4 py-2 rounded-lg
                            {{ $message->sender_id == auth()->id()
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-300' }}">

                            {{ $message->message }}

                        </div>

                    </div>

                @endforeach

            </div>

            <!-- SEND FORM -->
            <form id="chat-form"
                  action="/send-message"
                  method="POST"
                  class="p-4 border-t flex">

                @csrf

                <input type="hidden"
                       name="receiver_id"
                       value="{{ $receiver->id }}">

                <input type="text"
                       id="message-input"
                       name="message"
                       placeholder="Type message..."
                       class="flex-1 border rounded-lg px-4 py-2">

                <button class="bg-blue-500 text-white px-6 ml-2 rounded-lg">
                    Send
                </button>

            </form>

        @endisset

        <!-- GROUP CHAT -->
        @isset($groupReceiver)

            <!-- HEADER -->
            <div class="bg-gray-200 p-4 border-b">

                <h1 class="text-2xl font-bold">

                    {{ $groupReceiver->name }}

                </h1>

                <p class="text-sm text-gray-600">

                    Group Chat

                </p>

            </div>

            <!-- MESSAGE AREA -->
            <div id="group-chat-box"
                 class="flex-1 overflow-y-auto p-4 bg-gray-100">

                @foreach($groupMessages as $message)

                    <div class="mb-3">

                        <div class="text-sm font-bold mb-1">

                            {{ $message->user->name }}

                        </div>

                        <div class="inline-block px-4 py-2 rounded-lg
                            {{ $message->user_id == auth()->id()
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-300' }}">

                            {{ $message->message }}

                        </div>

                    </div>

                @endforeach

            </div>

            <!-- SEND GROUP FORM -->
            <form id="group-chat-form"
                  action="/send-group-message"
                  method="POST"
                  class="p-4 border-t flex">

                @csrf

                <input type="hidden"
                       name="group_id"
                       value="{{ $groupReceiver->id }}">

                <input type="text"
                       id="group-message-input"
                       name="message"
                       placeholder="Type message..."
                       class="flex-1 border rounded-lg px-4 py-2">

                <button class="bg-blue-500 text-white px-6 ml-2 rounded-lg">
                    Send
                </button>

            </form>

        @endisset

        <!-- EMPTY -->
        @if(!isset($receiver) && !isset($groupReceiver))

            <div class="flex-1 flex items-center justify-center">

                <h1 class="text-3xl text-gray-400">

                    Select chat

                </h1>

            </div>

        @endif

    </div>

</div>

<script>

@if(isset($receiver))

const receiverId = {{ $receiver->id }};

// PRIVATE CHAT
function loadMessages()
{
    fetch(`/messages/${receiverId}`)

        .then(res => res.json())

        .then(data => {

            let chatBox =
                document.getElementById(
                    'chat-box'
                );

            let html = '';

            data.forEach(message => {

                let isMe =
                    message.sender_id ==
                    {{ auth()->id() }};

                html += `
                    <div class="mb-3 ${isMe
                        ? 'text-right'
                        : 'text-left'}">

                        <div class="inline-block px-4 py-2 rounded-lg
                            ${isMe
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-300'}">

                            ${message.message}

                        </div>

                    </div>
                `;
            });

            if(chatBox.innerHTML !== html)
            {
                chatBox.innerHTML = html;

                chatBox.scrollTo({

                    top: chatBox.scrollHeight,

                    behavior: 'smooth'

                });
            }

        });
}

// ONLINE STATUS
function loadUsersStatus()
{
    fetch('/users-status')

    .then(res => res.json())

    .then(users => {

        users.forEach(user => {

            if(user.id == receiverId)
            {
                document.getElementById(
                    'receiver-status'
                ).innerText =

                    user.is_online
                    ? 'Online'
                    : 'Offline';
            }

        });

    });
}

// AUTO REFRESH
setInterval(loadMessages, 2000);

setInterval(() => {

    fetch('/online-status')
    .then(() => {

        loadUsersStatus();

    });

}, 2000);

// SEND MESSAGE
const chatForm =
    document.getElementById(
        'chat-form'
    );

if(chatForm)
{
    chatForm.addEventListener(
        'submit',
        function(e)
    {

        e.preventDefault();

        let formData =
            new FormData(this);

        fetch('/send-message', {

            method: 'POST',

            body: formData,

            headers: {

                'X-CSRF-TOKEN':
                '{{ csrf_token() }}'

            }

        })
        .then(() => {

            document.getElementById(
                'message-input'
            ).value = '';

            loadMessages();

        });

    });
}

// LOAD PERTAMA
loadMessages();

loadUsersStatus();

@endif

@if(isset($groupReceiver))

const groupId = {{ $groupReceiver->id }};

// GROUP CHAT
function loadGroupMessages()
{
    fetch(`/group-messages/${groupId}`)

        .then(res => res.json())

        .then(data => {

            let chatBox =
                document.getElementById(
                    'group-chat-box'
                );

            let html = '';

            data.forEach(message => {

                let isMe =
                    message.user_id ==
                    {{ auth()->id() }};

                html += `
                    <div class="mb-3">

                        <div class="text-sm font-bold mb-1">

                            ${message.user.name}

                        </div>

                        <div class="inline-block px-4 py-2 rounded-lg
                            ${isMe
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-300'}">

                            ${message.message}

                        </div>

                    </div>
                `;
            });

            if(chatBox.innerHTML !== html)
            {
                chatBox.innerHTML = html;

                chatBox.scrollTo({

                    top: chatBox.scrollHeight,

                    behavior: 'smooth'

                });
            }

        });
}

// AUTO REFRESH GROUP
setInterval(loadGroupMessages, 2000);

// SEND GROUP MESSAGE
const groupForm =
    document.getElementById(
        'group-chat-form'
    );

if(groupForm)
{
    groupForm.addEventListener(
        'submit',
        function(e)
    {

        e.preventDefault();

        let formData =
            new FormData(this);

        fetch('/send-group-message', {

            method: 'POST',

            body: formData,

            headers: {

                'X-CSRF-TOKEN':
                '{{ csrf_token() }}'

            }

        })
        .then(() => {

            document.getElementById(
                'group-message-input'
            ).value = '';

            loadGroupMessages();

        });

    });
}

// LOAD PERTAMA
loadGroupMessages();

@endif

// GLOBAL ONLINE STATUS
setInterval(() => {

    fetch('/online-status');

}, 2000);

// OFFLINE SAAT TAB DITUTUP
window.addEventListener('beforeunload', () => {

    navigator.sendBeacon(
        '/logout'
    );

});

</script>

</body>
</html>