<x-app-layout>

    <div class="h-screen flex overflow-hidden bg-gray-200">

        <!-- SIDEBAR -->
        <div class="w-80 bg-[#071427] text-white flex flex-col">

            <div class="p-6 border-b border-gray-800">
                <h1 class="text-5xl font-bold">Groups</h1>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                @foreach(auth()->user()->groups as $sidebarGroup)

                    <a
                        href="{{ route('groups.show', $sidebarGroup->id) }}"
                        class="block bg-green-700 hover:bg-green-800 transition p-5 rounded-2xl"
                    >
                        <div class="text-2xl font-medium text-white">
                            👥 {{ $sidebarGroup->name }}
                        </div>
                    </a>

                @endforeach

            </div>

        </div>

        <!-- CHAT AREA -->
        <div class="flex-1 flex flex-col">

            <!-- TOP -->
            <div class="bg-white border-b px-8 py-5 shadow-sm">

                <h2 class="text-3xl font-bold">
                    👥 {{ $group->name }}
                </h2>

            </div>

            <!-- MESSAGES -->
            <div
                id="messages"
                class="flex-1 overflow-y-auto p-8 bg-gray-100 space-y-6"
            >

                @foreach($messages as $message)

                    @if($message->user_id == auth()->id())

                        <!-- MY MESSAGE -->
                        <div class="flex justify-end">

                            <div
                                class="bg-green-500 text-white px-6 py-4 rounded-2xl rounded-br-md shadow-md max-w-[400px]"
                            >

                                <div class="font-bold text-sm mb-1">
                                    You
                                </div>

                                {{ $message->message }}

                            </div>

                        </div>

                    @else

                        <!-- OTHER MESSAGE -->
                        <div class="flex justify-start">

                            <div
                                class="bg-white text-black px-6 py-4 rounded-2xl rounded-bl-md shadow-md max-w-[400px]"
                            >

                                <div class="font-bold text-sm mb-1">
                                    {{ $message->user->name }}
                                </div>

                                {{ $message->message }}

                            </div>

                        </div>

                    @endif

                @endforeach

            </div>

            <!-- INPUT -->
            <form
                action="{{ route('groups.send') }}"
                method="POST"
                class="bg-white border-t p-5 flex items-center gap-4"
            >

                @csrf

                <input
                    type="hidden"
                    name="group_id"
                    value="{{ $group->id }}"
                >

                <input
                    type="text"
                    name="message"
                    placeholder="Type group message..."
                    class="flex-1 bg-gray-100 rounded-full px-6 py-4 text-lg focus:outline-none"
                    required
                >

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-full text-lg font-semibold"
                >
                    Send
                </button>

            </form>

        </div>

    </div>

</x-app-layout>