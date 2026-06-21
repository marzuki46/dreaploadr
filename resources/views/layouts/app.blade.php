<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'DReaploaDR') - DReaploaDR</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="//unpkg.com/alpinejs" defer></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    *{font-family:'Inter',sans-serif;}
    .gradient-bg{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);}
    .sidebar-gradient{background:linear-gradient(180deg,#1e1b4b 0%,#312e81 100%);}
</style>
</head>
<body class="bg-gray-50 min-h-screen">
    {{-- Top Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="/dashboard" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036c-.886-.006-1.302.312-1.302 1.094v1.78h2.284l-.295 3.667h-1.989v7.98H9.101z"/></svg>
                        <span class="text-xl font-bold text-gray-900">DReaploaDR</span>
                    </a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="/dashboard" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">Dashboard</a>
                        <a href="/scraper" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">Scraper</a>
                        <a href="/ai-content" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">AI Content</a>
                        <a href="/scheduled-posts" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">Schedule</a>
                        <a href="/affiliates" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">Affiliates</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition">Logout</button>
                            </form>
                        </div>
                    @else
                        <a href="/login" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Sign In</a>
                        <a href="/register" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 pt-4">
                <div class="bg-green-50 text-green-700 px-6 py-3 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 pt-4">
                <div class="bg-red-50 text-red-700 px-6 py-3 rounded-xl text-sm font-medium">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- Scripts --}}
    @stack('scripts')
</body>
</html>
