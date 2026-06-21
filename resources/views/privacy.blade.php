@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-3xl font-bold mb-6">Privacy Policy</h1>
        
        <div class="prose max-w-none text-gray-600">
            <p class="mb-4">Last updated: {{ date('F j, Y') }}</p>
            
            <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">1. Information We Collect</h2>
            <p class="mb-4">When you use our application to connect to your Facebook account, we collect your public profile information, email address, and tokens required to perform actions on your behalf (such as posting to your page).</p>
            
            <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">2. How We Use Your Information</h2>
            <p class="mb-4">We use the collected information exclusively to provide the services offered by this application, specifically allowing you to schedule and publish videos to your connected social media accounts.</p>
            
            <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">3. Data Retention and Deletion</h2>
            <p class="mb-4">You can request deletion of your data at any time by disconnecting your social accounts within the application dashboard, or by contacting our support team. We do not sell your data to third parties.</p>

            <h2 class="text-xl font-bold text-gray-900 mt-8 mb-4">4. Facebook App Terms</h2>
            <p class="mb-4">This application complies with Facebook Platform Terms. If you wish to remove this application's access to your Facebook account, you may do so at any time via your Facebook account settings.</p>
        </div>
    </div>
</div>
@endsection
