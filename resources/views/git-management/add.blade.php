@section('title', 'Add Pterodactyl Server - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Pterodactyl Server') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('git-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-xs font-semibold uppercase text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" onclick="showNavigationLoading(event)">
                    {{ __('Back to Git Management') }}
                </a>
                <a href="{{ route('git-management.add', ['refresh' => 1]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="showNavigationLoading(event)">
                    {{ __('Refresh from Panel') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('Select a server to manage') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Servers already added to the portal are highlighted below. Configure repository details and SSH access to enable git operations inside the container.') }}</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($servers as $server)
                        @php
                            $attributes = $server['attributes'] ?? [];
                            $relationships = $server['relationships']['node']['attributes'] ?? null;
                            $uuid = $attributes['uuid'] ?? null;
                            $identifier = $attributes['identifier'] ?? null;
                            $alreadyAdded = in_array($uuid, $existing, true);
                            $nodeName = $relationships['name'] ?? ($attributes['name'] ?? '');
                            $defaultContainer = $uuid ?: ($identifier ? 'pterodactyl_' . $identifier : '');
                        @endphp
                        <div class="px-6 py-6 @if($alreadyAdded) bg-gray-50 @endif">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $attributes['name'] ?? 'Unknown Server' }}</h4>
                                        @if ($alreadyAdded)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Already added') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500">UUID: <span class="font-mono">{{ $uuid }}</span></p>
                                    <p class="text-sm text-gray-500">Identifier: <span class="font-mono">{{ $identifier }}</span></p>
                                    <p class="text-sm text-gray-500">Node: {{ $nodeName }} @if($relationships && ($relationships['fqdn'] ?? false))<span class="font-mono">({{ $relationships['fqdn'] }})</span>@endif</p>
                                    @if (!empty($attributes['description']))
                                        <p class="text-sm text-gray-500">{{ $attributes['description'] }}</p>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    @if ($alreadyAdded)
                                        <p class="text-sm text-gray-500">{{ __('This server is already managed. You can update its settings from the main Git Management page.') }}</p>
                                    @else
                                        <form method="POST" action="{{ route('git-management.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @csrf
                                            <input type="hidden" name="pterodactyl_server_id" value="{{ $attributes['id'] ?? '' }}">
                                            <input type="hidden" name="pterodactyl_server_uuid" value="{{ $uuid }}">
                                            <input type="hidden" name="pterodactyl_server_identifier" value="{{ $identifier }}">
                                            <input type="hidden" name="name" value="{{ $attributes['name'] ?? '' }}">
                                            <input type="hidden" name="description" value="{{ $attributes['description'] ?? '' }}">
                                            <input type="hidden" name="pterodactyl_node_id" value="{{ $attributes['node'] ?? '' }}">
                                            <input type="hidden" name="pterodactyl_node_name" value="{{ $nodeName }}">

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('Repository Path') }}</label>
                                                <input type="text" name="repository_path" value="{{ old('repository_path', $defaults['repository_path']) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('Repository URL') }}</label>
                                                <input type="text" name="repository_url" value="{{ old('repository_url') }}" placeholder="https://github.com/org/project.git" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('Container Name') }}</label>
                                                <input type="text" name="container_name" value="{{ $defaultContainer }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('Default Remote') }}</label>
                                                    <input type="text" name="remote_name" value="{{ $defaults['remote'] }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('Default Branch') }}</label>
                                                    <input type="text" name="default_branch" value="{{ $defaults['branch'] }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                </div>
                                            </div>

                                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('SSH Host (Node)') }}</label>
                                                    <input type="text" name="ssh_host" value="{{ $relationships['fqdn'] ?? old('ssh_host') }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('SSH Port') }}</label>
                                                    <input type="number" name="ssh_port" value="{{ $defaults['ssh_port'] }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('SSH Username') }}</label>
                                                    <input type="text" name="ssh_username" value="{{ $defaults['ssh_user'] }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                </div>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">{{ __('SSH Private Key Path (Portal host)') }}</label>
                                                <input type="text" name="ssh_private_key_path" value="{{ $defaults['ssh_key_path'] }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                                                <p class="text-xs text-gray-500 mt-1">{{ __('Provide an absolute path to the private key file accessible by the portal. Ensure the key grants SSH access to the node so docker exec commands can run.') }}</p>
                                            </div>

                                            <div class="md:col-span-2 flex justify-end">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    {{ __('Add Server') }}
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-6">
                            <p class="text-sm text-gray-500">{{ __('No servers were returned by the Pterodactyl API. Check your credentials or ensure the API key has access to the application endpoints.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
