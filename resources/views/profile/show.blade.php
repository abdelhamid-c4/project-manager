@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div class="mx-auto max-w-5xl pt-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-950">Profile</h1>
        <p class="mt-2 text-sm text-slate-500">Manage your personal information and workspace details.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
        <aside class="card h-fit">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-2xl font-semibold text-slate-700">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="mt-4 text-lg font-semibold text-slate-950">{{ $user->name }}</h2>
                <p class="mt-1 break-all text-sm text-slate-500">{{ $user->email }}</p>
                <span class="badge mt-4 bg-slate-100 text-slate-700">
                    {{ ucwords(str_replace('_', ' ', $user->role ?? 'user')) }}
                </span>
            </div>

            <dl class="mt-6 space-y-4 border-t border-slate-100 pt-5 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Service</dt>
                    <dd class="font-medium text-slate-900">{{ ucwords($user->service ?? 'Other') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-slate-500">Member since</dt>
                    <dd class="font-medium text-slate-900">{{ $user->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>

            <div class="mt-6 grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <div class="rounded-lg border border-slate-100 p-3">
                    <p class="text-xs font-medium uppercase text-slate-500">Projects</p>
                    <p class="mt-1 text-xl font-semibold text-slate-950">{{ $stats['accessible_projects'] }}</p>
                </div>
                <div class="rounded-lg border border-slate-100 p-3">
                    <p class="text-xs font-medium uppercase text-slate-500">Tasks</p>
                    <p class="mt-1 text-xl font-semibold text-slate-950">{{ $stats['assigned_tasks'] }}</p>
                </div>
            </div>
        </aside>

        <section class="card">
            <div class="border-b border-slate-100 pb-5">
                <h2 class="text-lg font-semibold text-slate-950">Personal information</h2>
                <p class="mt-1 text-sm text-slate-500">Update the information shown to your team.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="form-label">Name</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           class="form-input @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           class="form-input @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="service" class="form-label">Service</label>
                        <select id="service"
                                name="service"
                                class="form-input @error('service') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                required>
                            @foreach(['development' => 'Development', 'design' => 'Design', 'marketing' => 'Marketing', 'support' => 'Support', 'sales' => 'Sales', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" {{ old('service', $user->service ?? 'other') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('service')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Role</label>
                        <input type="text"
                               value="{{ ucwords(str_replace('_', ' ', $user->role ?? 'user')) }}"
                               class="form-input bg-slate-50 text-slate-600"
                               readonly>
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save changes</button>
                </div>
            </form>
        </section>

    </div>
</div>
@endsection
