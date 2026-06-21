<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- SEO Optimization -->
    <title>{{ $meta_title ?? 'DReaploaDR - Facebook Reels Automation & Scraper' }}</title>
    <meta name="description" content="{{ $meta_description ?? 'Automate your Facebook Reels with AI. Scrape, remix, and schedule content easily.' }}">
    <meta name="keywords" content="facebook reels, scraper, automation, auto post, AI, video remix">
    <meta property="og:title" content="{{ $meta_title ?? 'DReaploaDR - Facebook Reels Automation & Scraper' }}">
    <meta property="og:description" content="{{ $meta_description ?? 'Automate your Facebook Reels with AI. Scrape, remix, and schedule content easily.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .gradient-text { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .pricing-card.featured { transform: scale(1.05); }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036c-.886-.006-1.302.312-1.302 1.094v1.78h2.284l-.295 3.667h-1.989v7.98H9.101z"/></svg>
                    <span class="text-2xl font-bold text-gray-900">DReaploaDR</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-gray-600 hover:text-gray-900 transition">Features</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 transition">How It Works</a>
                    <a href="#pricing" class="text-gray-600 hover:text-gray-900 transition">Pricing</a>
                    <a href="#faq" class="text-gray-600 hover:text-gray-900 transition">FAQ</a>
                    <a href="/login" class="text-gray-600 hover:text-gray-900 transition">Log in</a>
                    <a href="/register" class="gradient-bg text-white px-6 py-2.5 rounded-lg font-semibold hover:opacity-90 transition">Get Started</a>
                </div>
                <button class="md:hidden p-2 rounded-lg hover:bg-gray-100" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 py-4">
            <div class="flex flex-col gap-4">
                <a href="#features" class="text-gray-600 hover:text-gray-900">Features</a>
                <a href="#how-it-works" class="text-gray-600 hover:text-gray-900">How It Works</a>
                <a href="#pricing" class="text-gray-600 hover:text-gray-900">Pricing</a>
                <a href="#faq" class="text-gray-600 hover:text-gray-900">FAQ</a>
                <a href="/login" class="text-gray-600 hover:text-gray-900">Log in</a>
                <a href="/register" class="gradient-bg text-white px-6 py-2.5 rounded-lg font-semibold text-center hover:opacity-90">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="min-h-screen flex items-center pt-20 pb-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full text-sm font-semibold mb-6">🚀 AI-Powered Facebook Reels Automation</div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Automate Your<br>
                        <span class="gradient-text">Facebook Reels</span><br>
                        with AI Magic
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Scrape, remix, and schedule Facebook Reels automatically. Our AI-powered platform helps you grow your audience with minimal effort.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/register" class="gradient-bg text-white px-8 py-4 rounded-xl font-bold text-lg hover:opacity-90 transition shadow-lg shadow-indigo-200">Start Free Trial</a>
                        <a href="#how-it-works" class="border-2 border-gray-200 text-gray-700 px-8 py-4 rounded-xl font-bold text-lg hover:border-gray-300 transition">See How It Works</a>
                    </div>
                    <div class="flex items-center gap-8 mt-12">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-200 border-2 border-white flex items-center justify-center text-xs font-bold text-indigo-600">JD</div>
                            <div class="w-10 h-10 rounded-full bg-purple-200 border-2 border-white flex items-center justify-center text-xs font-bold text-purple-600">AK</div>
                            <div class="w-10 h-10 rounded-full bg-pink-200 border-2 border-white flex items-center justify-center text-xs font-bold text-pink-600">SM</div>
                            <div class="w-10 h-10 rounded-full bg-green-200 border-2 border-white flex items-center justify-center text-xs font-bold text-green-600">+2k</div>
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-yellow-400">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="text-sm text-gray-500">Trusted by 2,000+ creators</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="gradient-bg rounded-2xl p-1">
                        <div class="bg-white rounded-2xl p-8">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Reels Scraped</p>
                                        <p class="text-2xl font-bold text-indigo-600">15,342</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">AI Remixes Created</p>
                                        <p class="text-2xl font-bold text-purple-600">8,791</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center"><svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Scheduled Posts</p>
                                        <p class="text-2xl font-bold text-pink-600">12,456</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 gradient-bg rounded-2xl -z-10 opacity-30"></div>
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-indigo-100 rounded-2xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Everything You Need</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Powerful tools to automate your Facebook Reels content creation and publishing workflow.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Scrape & List Reels</h3>
                    <p class="text-gray-600 leading-relaxed">Automatically scrape Facebook Reels from any page. Extract videos, captions, and metadata with one click.</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">AI Content Remaker</h3>
                    <p class="text-gray-600 leading-relaxed">Remix and rewrite your content using AI. Powered by Gemini and Naim Router for creative variations.</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Scheduled Posts</h3>
                    <p class="text-gray-600 leading-relaxed">Plan and schedule your reels posts in advance. Set the date and time, and we'll handle the rest.</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Multi-Account Dashboard</h3>
                    <p class="text-gray-600 leading-relaxed">Manage multiple Facebook pages from one dashboard. Switch accounts seamlessly without logging in/out.</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Facebook Login</h3>
                    <p class="text-gray-600 leading-relaxed">One-click Facebook login integration. Connect your Facebook account and pages instantly.</p>
                </div>
                <div class="feature-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100 transition-all duration-300">
                    <div class="w-14 h-14 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Affiliate System</h3>
                    <p class="text-gray-600 leading-relaxed">Built-in affiliate program with referral tracking, commissions, and payouts. Earn while you create.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-24 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Three simple steps to automate your Facebook Reels content.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-200">
                        <span class="text-3xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Connect Facebook</h3>
                    <p class="text-gray-600">Login with Facebook and connect your pages. Grant permissions to access your reels and pages.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-200">
                        <span class="text-3xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Scrape & Remix</h3>
                    <p class="text-gray-600">Scrape reels from any source, then let AI remix the content with fresh variations and captions.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-200">
                        <span class="text-3xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Schedule & Publish</h3>
                    <p class="text-gray-600">Schedule your remixed reels to post at optimal times. Watch your engagement grow automatically.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-24 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Simple Pricing</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Choose the plan that fits your needs. No hidden fees.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="pricing-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                    <p class="text-gray-500 mb-6">Perfect for beginners</p>
                    <div class="mb-8">
                        <span class="text-5xl font-extrabold text-gray-900">$19</span>
                        <span class="text-gray-500 text-lg">/month</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">100 reels scraped/month</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">50 AI remixes</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">1 Facebook page</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Basic scheduling</span></li>
                    </ul>
                    <a href="/register" class="block text-center border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold hover:border-gray-300 transition">Get Started</a>
                </div>
                <div class="pricing-card featured bg-white rounded-2xl p-8 shadow-xl border-2 border-indigo-500 relative">
                    <div class="absolute top-0 right-0 gradient-bg text-white px-4 py-1 rounded-bl-xl rounded-tr-xl text-sm font-semibold">Popular</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro</h3>
                    <p class="text-gray-500 mb-6">For serious creators</p>
                    <div class="mb-8">
                        <span class="text-5xl font-extrabold text-gray-900">$49</span>
                        <span class="text-gray-500 text-lg">/month</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">1,000 reels scraped/month</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Unlimited AI remixes</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">5 Facebook pages</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Advanced scheduling</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Priority support</span></li>
                    </ul>
                    <a href="/register" class="block text-center gradient-bg text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg shadow-indigo-200">Get Started</a>
                </div>
                <div class="pricing-card bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                    <p class="text-gray-500 mb-6">For agencies & teams</p>
                    <div class="mb-8">
                        <span class="text-5xl font-extrabold text-gray-900">$149</span>
                        <span class="text-gray-500 text-lg">/month</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">10,000 reels scraped/month</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Unlimited AI remixes</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Unlimited pages</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">API access</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> <span class="text-gray-600">Dedicated support</span></li>
                    </ul>
                    <a href="/register" class="block text-center border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold hover:border-gray-300 transition">Contact Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    @if(isset($posts) && $posts->count() > 0)
    <section id="blog" class="py-24 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Latest Articles</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Insights, updates, and tutorials about Facebook Reels automation.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                    @if($post->image)
                        <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-r from-indigo-100 to-purple-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                    @endif
                    <div class="p-6">
                        @if($post->category)
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">{{ $post->category->name }}</span>
                        @endif
                        <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3 leading-tight"><a href="/blog/{{ $post->slug }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a></h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $post->meta_description ?? Str::limit(strip_tags($post->content), 120) }}</p>
                        <div class="flex items-center text-xs text-gray-500">
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ -->
    <section id="faq" class="py-24 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-xl text-gray-600">Everything you need to know about DReaploaDR.</p>
            </div>
            <div class="space-y-4">
                <details class="bg-white rounded-xl border border-gray-200 p-6 group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-lg text-gray-900">
                        What is DReaploaDR?
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">DReaploaDR is an AI-powered automation platform that helps you scrape, remix, and schedule Facebook Reels. It uses AI to generate creative variations of your content and post them automatically.</p>
                </details>
                <details class="bg-white rounded-xl border border-gray-200 p-6 group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-lg text-gray-900">
                        How does the AI remixing work?
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">Our AI analyzes your scraped reels and generates fresh versions with different captions, descriptions, and creative angles. You can choose between Gemini or Naim Router AI providers.</p>
                </details>
                <details class="bg-white rounded-xl border border-gray-200 p-6 group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-lg text-gray-900">
                        Can I use multiple Facebook accounts?
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">Yes! Pro and Enterprise plans support multiple Facebook pages. You can manage all your pages from a single dashboard without logging in and out.</p>
                </details>
                <details class="bg-white rounded-xl border border-gray-200 p-6 group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-lg text-gray-900">
                        Is there a free trial?
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">Yes! We offer a 7-day free trial on all plans. No credit card required to get started.</p>
                </details>
                <details class="bg-white rounded-xl border border-gray-200 p-6 group cursor-pointer">
                    <summary class="flex items-center justify-between font-semibold text-lg text-gray-900">
                        What payment methods do you accept?
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">We accept all major credit cards, debit cards, and various local payment methods through Midtrans.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 px-4 gradient-bg">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-extrabold text-white mb-6">Ready to Automate Your Reels?</h2>
            <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">Join thousands of creators who are already using DReaploaDR to grow their audience with AI-powered automation.</p>
            <a href="/register" class="inline-block bg-white text-indigo-600 px-10 py-4 rounded-xl font-bold text-lg hover:bg-indigo-50 transition shadow-xl">Start Your Free Trial</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-8 h-8 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 011.141.195v3.325a8.623 8.623 0 00-.653-.036c-.886-.006-1.302.312-1.302 1.094v1.78h2.284l-.295 3.667h-1.989v7.98H9.101z"/></svg>
                        <span class="text-xl font-bold text-white">DReaploaDR</span>
                    </div>
                    <p class="text-sm">AI-powered Facebook Reels automation platform.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#faq" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-sm text-center">
                <p>&copy; {{ date('Y') }} DReaploaDR. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
