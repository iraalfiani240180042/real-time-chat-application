<x-app-layout>

    <div class="h-screen flex overflow-hidden bg-gray-200">

        <!-- SIDEBAR -->
        <div class="w-80 bg-[#071427] text-white flex flex-col">

            <div class="p-6 border-b border-gray-800">
                <h1 class="text-5xl font-bold">Chats</h1>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                @isset($users)
                    @foreach($users as $chatUser)

                        <a href="{{ route('dashboard', $chatUser->id) }}"
                           class="block bg-[#1B273B] hover:bg-[#24344d] transition p-5 rounded-2xl">

                            <div class="text-2xl font-medium">
                                {{ $chatUser->name }}
                            </div>

                        </a>

                    @endforeach
                @endisset

                <div class="pt-6 pb-2 px-2 text-gray-400 text-sm uppercase tracking-widest">
                    Groups
                </div>

                @isset($groups)
                    @foreach($groups as $group)

                        <a href="{{ route('dashboard.group', $group->id) }}"
                           class="block bg-blue-400 hover:bg-blue-500 transition p-5 rounded-2xl">

                            <div class="text-2xl font-medium text-white">
                                {{ $group->name }}
                            </div>

                        </a>

                    @endforeach
                @endisset

            </div>

            <!-- ========================= -->
            <!-- LOGOUT BUTTON -->
            <!-- ========================= -->
            <div class="p-4 border-t border-gray-700">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-semibold">
                        Logout
                    </button>

                </form>

            </div>

        </div>

        <!-- CHAT AREA -->
        <div class="flex-1 flex flex-col">

            <!-- TOP HEADER -->
            <div class="bg-white border-b px-8 py-5 shadow-sm">

                @if(!empty($user))

                    <h2 class="text-3xl font-bold">
                        {{ $user->name }}
                    </h2>

                    <p class="text-sm mt-1">

                        @if(!empty($user) && $user->is_online)

                            <span class="text-green-500 font-medium">
                                ● Online
                            </span>

                        @else

                            <span class="text-gray-500 font-medium">
                                ● Offline
                            </span>

                        @endif

                    </p>

                @elseif(!empty($group))

                    <h2 class="text-3xl font-bold">
                        {{ $group->name }}
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Group Chat
                    </p>

                @else

                    <h2 class="text-3xl font-bold">
                        Select Chat
                    </h2>

                @endif

            </div>

            <!-- CHAT BOX -->
            <div id="messages"
                 class="flex-1 overflow-y-auto p-8 bg-gray-100 space-y-6">

                @forelse($messages ?? [] as $message)

                    @if(isset($user))

                        @if($message->sender_id == auth()->id())

                            <div class="flex justify-end">
                                <div class="bg-blue-500 text-white px-6 py-4 rounded-2xl rounded-br-md max-w-[400px]">
                                    {{ $message->message }}
                                </div>
                            </div>

                        @else

                            <div class="flex justify-start">
                                <div class="bg-white text-black px-6 py-4 rounded-2xl rounded-bl-md max-w-[400px]">
                                    {{ $message->message }}
                                </div>
                            </div>

                        @endif

                    @elseif(isset($group))

                        @if($message->user_id == auth()->id())

                            <div class="flex justify-end">
                                <div class="bg-blue-400 text-white px-6 py-4 rounded-2xl rounded-br-md max-w-[400px]">
                                    <div class="font-bold text-sm mb-1">You</div>
                                    {{ $message->message }}
                                </div>
                            </div>

                        @else

                            <div class="flex justify-start">
                                <div class="bg-white text-black px-6 py-4 rounded-2xl rounded-bl-md max-w-[400px]">
                                    <div class="font-bold text-sm mb-1">
                                        {{ $message->user->name }}
                                    </div>
                                    {{ $message->message }}
                                </div>
                            </div>

                        @endif

                    @endif

                @empty
                    <div class="text-gray-500 text-lg">No messages yet</div>
                @endforelse

            </div>

            <!-- INPUT -->
            @if(!empty($user))

                <form id="chat-form"
                      class="bg-white border-t p-5 flex items-center gap-4">

                    @csrf

                    <input type="hidden"
                           id="receiver_id"
                           value="{{ $user->id }}">

                    <input type="text"
                           id="message-input"
                           placeholder="Type message..."
                           class="flex-1 bg-gray-100 rounded-full px-6 py-4">

                    <button class="bg-blue-500 text-white px-8 py-4 rounded-full">
                        Send
                    </button>

                </form>

            @elseif(!empty($group))

                <form action="{{ route('groups.send') }}"
                      method="POST"
                      class="bg-white border-t p-5 flex items-center gap-4">

                    @csrf

                    <input type="hidden"
                           name="group_id"
                           value="{{ $group->id }}">

                    <input type="text"
                           name="message"
                           placeholder="Type group message..."
                           class="flex-1 bg-gray-100 rounded-full px-6 py-4">

                    <button class="bg-blue-400 text-white px-8 py-4 rounded-full">
                        Send
                    </button>

                </form>

            @endif

        </div>

    </div>

    <!-- ONLINE / OFFLINE SYSTEM -->
    <script>

        fetch('/set-online', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        window.addEventListener('beforeunload', function () {
            navigator.sendBeacon('/set-offline');
        });

    </script>

    <!-- CHAT SCRIPT -->
    <script>

        const receiverId = document.getElementById('receiver_id')?.value;

        function loadMessages() {

            if (!receiverId) return;

            fetch(`/messages/${receiverId}`)
                .then(res => res.json())
                .then(messages => {

                    let html = '';

                    messages.forEach(message => {

                        let isMine = message.sender_id == {{ auth()->id() }};

                        if (isMine) {
                            html += `
                                <div class="flex justify-end">
                                    <div class="bg-blue-500 text-white px-6 py-4 rounded-2xl">
                                        ${message.message}
                                    </div>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="flex justify-start">
                                    <div class="bg-white text-black px-6 py-4 rounded-2xl">
                                        ${message.message}
                                    </div>
                                </div>
                            `;
                        }

                    });

                    document.getElementById('messages').innerHTML = html;
                    document.getElementById('messages').scrollTop =
                        document.getElementById('messages').scrollHeight;

                });

        }

        if (receiverId) {
            setInterval(loadMessages, 1000);
            loadMessages();
        }

        document.getElementById('chat-form')?.addEventListener('submit', function (e) {

            e.preventDefault();

            fetch('/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: document.getElementById('message-input').value
                })
            }).then(() => {

                document.getElementById('message-input').value = '';
                loadMessages();

            });

        });

    </script>

</x-app-layout>