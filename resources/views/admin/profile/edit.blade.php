@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-6">
    <h1 class="text-2xl font-bold mb-8">Edit Profile</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block font-medium">Name</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Password (Leave blank if not changing)</label>
            <input type="password" name="password" class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <label class="block font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" class="border w-full px-3 py-2 rounded">
        </div>

        <div>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Update Profile
            </button>
        </div>
    </form>
</div>
@endsection
