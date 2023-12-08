<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body class="bg-gray-100">

<nav class="bg-gray-800 p-4">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="/admin" class="text-white font-bold text-2xl">Basic Panel</a>
                <div class="space-x-4 ms-16">
                    <a href="/admin/apps" class="text-violet-100">Apps</a>
                    <a href="/admin/categories" class="text-violet-100">Categories</a>
                    <a href="/admin/cards" class="text-violet-100">Cards</a>
                    <a href="/admin/users" class="text-violet-100">Users</a>
                </div>
            </div>
            <div class="flex space-x-4">
                <form method="post" action="{{ route('logout')  }}">
                    @csrf
                    <button type="submit" class="text-white">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@include('components.flash-message')


<div class="container mx-auto mt-16">
    @yield('content')
</div>

</body>
</html>
