@extends('layouts.app')
@section('title', 'Audit Log')

@section('content')
<div class="pt-8 space-y-6">

    <div class="page-hero-panel p-5 lg:p-7">
        <span class="badge bg-cyan-100 text-cyan-800">System history</span>
        <h1 class="mt-3 text-2xl font-bold text-slate-950 lg:text-3xl">Audit Log</h1>
        <p class="mt-2 text-sm text-slate-500">All tracked actions across the system, presented in the same workspace surface.</p>
    </div>

    <div class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">When</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                            {{ $log->created_at->format('M d, Y H:i') }}
                            <br><span class="text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                            {{ $log->user->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $actionColors = [
                                    'project.created' => 'bg-green-100 text-green-700',
                                    'project.updated' => 'bg-blue-100 text-blue-700',
                                    'project.deleted' => 'bg-red-100 text-red-700',
                                    'task.created'    => 'bg-green-100 text-green-700',
                                    'task.updated'    => 'bg-blue-100 text-blue-700',
                                    'task.deleted'    => 'bg-red-100 text-red-700',
                                ];
                                $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="badge {{ $color }}">{{ $log->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            {{ $log->modelLabel() }} #{{ $log->model_id }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs">{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 text-sm">
                            No activity recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
