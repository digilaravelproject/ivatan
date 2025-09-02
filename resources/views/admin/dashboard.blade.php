<x-app-layout>

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
    <div class="grid grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded shadow">
            <h3 class="text-gray-500">Total Users</h3>
            <p class="text-3xl font-semibold">{{ $usersCount }}</p>
        </div>
    </div>
</div>
</x-app-layout>

