<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - DReaploaDR</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:'Inter',sans-serif;}.gradient-bg{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);}</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-md">
<div class="text-center mb-8">
<a href="/" class="inline-flex items-center gap-2 mb-6">
<svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036c-.886-.006-1.302.312-1.302 1.094v1.78h2.284l-.295 3.667h-1.989v7.98H9.101z"/></svg>
<span class="text-2xl font-bold text-gray-900">DReaploaDR</span>
</a>
<h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
<p class="text-gray-600 mt-1">Sign in to your account</p>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
<form method="POST" action="/login" class="space-y-5">
@csrf
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
<input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" placeholder="you@example.com">
@error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
</div>
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
<input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition" placeholder="••••••••">
@error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
</div>
<div class="flex items-center justify-between">
<label class="flex items-center gap-2 text-sm text-gray-600">
<input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
Remember me
</label>
<a href="#" class="text-sm text-indigo-600 hover:text-indigo-500 font-semibold">Forgot password?</a>
</div>
<button type="submit" class="w-full gradient-bg text-white py-3.5 rounded-xl font-bold text-lg hover:opacity-90 transition shadow-lg shadow-indigo-200">Sign In</button>
</form>
<div class="relative my-8">
<div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
<div class="relative flex justify-center"><span class="bg-white px-4 text-sm text-gray-500">Or continue with</span></div>
</div>
<a href="/auth/facebook/redirect" class="w-full flex items-center justify-center gap-3 bg-blue-600 text-white py-3.5 rounded-xl font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
Continue with Facebook
</a>
<p class="text-center mt-6 text-sm text-gray-600">
Don't have an account?
<a href="/register" class="text-indigo-600 hover:text-indigo-500 font-semibold">Sign up</a>
</p>
</div>
</div>
</body>
</html>
