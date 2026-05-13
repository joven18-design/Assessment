<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Issue Tracker') - Smart Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('issues.index') }}" class="text-xl font-bold text-indigo-600">
                    Issue Tracker
                </a>
                <a href="{{ route('issues.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                    + New Issue
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-gray-400 text-sm py-4">
        Issue Intake &amp; Smart Summary System
    </footer>
</body>
</html>
