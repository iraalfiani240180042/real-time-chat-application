<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-8 rounded shadow w-96">

    <h1 class="text-3xl font-bold mb-6">Register</h1>

    <form method="POST" action="/register">
        @csrf

        <input type="text"
               name="name"
               placeholder="Name"
               class="w-full border p-3 mb-4 rounded">

        <input type="email"
               name="email"
               placeholder="Email"
               class="w-full border p-3 mb-4 rounded">

        <input type="password"
               name="password"
               placeholder="Password"
               class="w-full border p-3 mb-4 rounded">

        <button class="w-full bg-green-500 text-white p-3 rounded">
            Register
        </button>

    </form>

    <a href="/login" class="text-blue-500 mt-4 block">
        Login
    </a>

</div>

</body>
</html>