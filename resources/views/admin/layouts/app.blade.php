<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Admin Panel')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans text-gray-800 flex flex-col min-h-screen">
    <!-- Sidebar -->
    @include('components.admin.topbar')

    <div class="flex-1 flex">
        @include('components.admin.sidebar')
        <!-- Topbar -->

        <!-- Main content -->
        <main class="p-6 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>


    @vite('resources/js/app.js')
</body>
</html>
