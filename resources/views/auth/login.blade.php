<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">

    <h1 class="text-3xl font-bold mb-6">Login</h1>

    @if(session('error'))
        <div class="bg-red-200 p-2 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <input type="email"
               name="email"
               placeholder="Email"
               class="w-full border p-3 mb-4 rounded">

        <input type="password"
               name="password"
               placeholder="Password"
               class="w-full border p-3 mb-4 rounded">

        <button class="w-full bg-blue-500 text-white p-3 rounded">
            Login
        </button>

    </form>

    <a href="/register" class="text-blue-500 mt-4 block">
        Register
    </a>

</div>

</body>
</html>