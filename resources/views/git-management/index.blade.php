@section('title', 'Git Management - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Git Management') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('git-management.add') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150" onclick="showNavigationLoading(event)">
                    {{ __('Add Server') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @forelse ($servers as $server)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $server->name }}</h3>
                            <p class="text-sm text-gray-500">Container target: <span class="font-mono">{{ $server->docker_exec_target }}</span></p>
                            <p class="text-sm text-gray-500">Repository path: <span class="font-mono">{{ $server->repository_path }}</span></p>
                            @if ($server->repository_url)
                                <p class="text-sm text-gray-500">Repository URL: <span class="font-mono">{{ $server->repository_url }}</span></p>
                            @endif
                            <p class="text-sm text-gray-500">Default remote: <span class="font-mono">{{ $server->remote_name ?? $defaults['remote'] }}</span> · Default branch: <span class="font-mono">{{ $server->default_branch ?? $defaults['branch'] }}</span></p>
                            <p class="text-sm text-gray-500">Current branch: <span class="font-mono">{{ $server->current_branch ?? 'Unknown' }}</span>@if ($server->branch_cache_timestamp) <span class="text-xs text-gray-400"> (cached {{ \Carbon\Carbon::parse($server->branch_cache_timestamp)->diffForHumans() }})</span>@endif</p>
                            <!--<p class="text-sm text-gray-500">SSH target: <span class="font-mono">{{ sprintf('%s@%s:%s', $server->ssh_username ?? $defaults['ssh_user'], $server->ssh_host ?? 'localhost', $server->ssh_port ?? $defaults['ssh_port']) }}</span></p>-->
                        </div>
                        <div class="flex items-center space-x-3">
                            <form method="POST" action="{{ route('git-management.destroy', $server) }}" onsubmit="return confirm('Remove this server from Git Management?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-200 text-red-600 text-xs font-semibold uppercase rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    {{ __('Remove') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="px-6 py-5 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-5">
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ __('Branch Sync') }}</h4>
                            @php
                                $defaultRemote = $server->remote_name ?? $defaults['remote'];
                                $defaultBranch = $server->default_branch ?? $defaults['branch'];
                                $selectedBranch = $server->current_branch && $server->current_branch !== 'HEAD'
                                    ? $server->current_branch
                                    : $defaultBranch;

                                $branchOptions = ($server->available_branches ?? collect())
                                    ->filter()
                                    ->unique()
                                    ->values();

                                if ($selectedBranch && ! $branchOptions->contains($selectedBranch)) {
                                    $branchOptions = $branchOptions->prepend($selectedBranch);
                                }

                                if ($defaultBranch && ! $branchOptions->contains($defaultBranch)) {
                                    $branchOptions = $branchOptions->push($defaultBranch);
                                }

                                $branchOptions = $branchOptions->whenEmpty(fn ($collection) => $collection->push($defaultBranch ?: 'main'));
                            @endphp

                            <form method="POST" action="{{ route('git-management.command', $server) }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="action" value="sync">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label for="sync-remote-{{ $server->id }}" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">{{ __('Remote') }}</label>
                                        <input type="text" id="sync-remote-{{ $server->id }}" name="remote" value="{{ $defaultRemote }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label for="sync-branch-{{ $server->id }}" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">{{ __('Branch') }}</label>
                                        <input type="text" id="sync-branch-{{ $server->id }}" name="branch" value="{{ $selectedBranch }}" list="branch-options-{{ $server->id }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                        <datalist id="branch-options-{{ $server->id }}">
                                            @foreach ($branchOptions as $branchOption)
                                                <option value="{{ $branchOption }}"></option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-gray-500">{{ __('Runs fetch, checkout, and pull in one step.') }}</p>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        {{ __('Sync Branch') }}
                                    </button>
                                </div>
                            </form>

                            <div class="border-t border-gray-100 pt-4">
                                <form method="POST" action="{{ route('git-management.command', $server) }}" class="flex items-center justify-between">
                                    @csrf
                                    <input type="hidden" name="action" value="status">
                                    <p class="text-xs text-gray-500">{{ __('Check current git status inside the container.') }}</p>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-900 focus:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        {{ __('Show Status') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ __('Recent Activity') }}</h4>
                            <div class="mt-3 space-y-3">
                                @forelse ($server->logs as $log)
                                    <details class="bg-gray-50 rounded-lg border border-gray-200">
                                        <summary class="px-4 py-3 cursor-pointer flex items-center justify-between">
                                            <div>
                                                <span class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</span>
                                                <span class="ml-2 text-sm font-medium text-gray-700">{{ strtoupper($log->action) }}</span>
                                            </div>
                                            <span class="text-xs font-semibold {{ $log->status === 'succeeded' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </summary>
                                        <div class="px-4 pb-4">
                                            <p class="text-xs text-gray-500 mb-2">{{ $log->command }}</p>
                                            @if ($log->output)
                                                <pre class="bg-black text-green-400 text-xs rounded-md p-3 overflow-x-auto">{{ trim($log->output) }}</pre>
                                            @endif
                                            @if ($log->error_output && $log->status === 'failed')
                                                <pre class="bg-black text-red-400 text-xs rounded-md p-3 overflow-x-auto mt-2">{{ trim($log->error_output) }}</pre>
                                            @endif
                                        </div>
                                    </details>
                                @empty
                                    <p class="text-sm text-gray-500">{{ __('No git activity recorded yet.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('No servers added yet') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Connect your first Pterodactyl server to manage git operations directly from the portal.') }}</p>
                        <a href="{{ route('git-management.add') }}" class="inline-flex mt-4 items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="showNavigationLoading(event)">
                            {{ __('Add a server') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
