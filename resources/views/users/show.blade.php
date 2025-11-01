@section('title', 'User Details: ' . $authentikUser['username'] . ' - ' . config('app.name'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}: {{ $authentikUser['username'] }}
            </h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('users.edit', $authentikUser['pk']) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User
                </a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 send-recovery-btn"
                        data-user-id="{{ $authentikUser['pk'] }}"
                        data-username="{{ $authentikUser['username'] }}"
                        data-email="{{ $authentikUser['email'] }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send Password Recovery
                </button>
                <button class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 delete-user-btn"
                        data-user-id="{{ $authentikUser['pk'] }}"
                        data-username="{{ $authentikUser['username'] }}">
                    Delete User
                </button>
                <a href="{{ route('users.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @php
                $activeTab = request()->get('tab', 'overview');
                $tabs = [
                    'overview' => 'Overview',
                    'groups' => 'Groups',
                    'pim' => 'PIM',
                ];
                if (!empty($authentikUser['attributes']) && count($authentikUser['attributes']) > 0) {
                    $tabs['attributes'] = 'Attributes';
                }
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <nav class="flex flex-wrap gap-2" aria-label="Tabs">
                        @foreach($tabs as $tabKey => $tabLabel)
                            @php $isActive = $activeTab === $tabKey; @endphp
                            <a href="{{ route('users.show', array_filter(['id' => $authentikUser['pk'], 'tab' => $tabKey !== 'overview' ? $tabKey : null])) }}"
                               class="px-3 py-2 text-sm font-medium rounded-md {{ $isActive ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100' }}">
                                {{ $tabLabel }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            @if($activeTab === 'overview')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $authentikUser['username'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $authentikUser['name'] ?: 'Not set' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $authentikUser['email'] ?: 'Not set' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">User ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $authentikUser['pk'] }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $authentikUser['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $authentikUser['is_active'] ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Superuser</dt>
                                        <dd class="mt-1">
                                            @if($authentikUser['is_superuser'])
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Yes
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">No</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($authentikUser['last_login'])
                                                {{ \Carbon\Carbon::parse($authentikUser['last_login'])->format('M j, Y g:i A') }}
                                                <span class="text-gray-500">({{ \Carbon\Carbon::parse($authentikUser['last_login'])->diffForHumans() }})</span>
                                            @else
                                                Never
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Date Joined</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($authentikUser['date_joined'])
                                                {{ \Carbon\Carbon::parse($authentikUser['date_joined'])->format('M j, Y g:i A') }}
                                            @else
                                                Unknown
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Local Sync Status</h3>
                        @if($localUser)
                            <div class="flex items-center mb-4">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-600 font-medium">User is synced locally</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Local ID</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $localUser->id }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Local Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $localUser->name ?: 'Not set' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Server Username</dt>
                                            <dd class="mt-1 text-sm {{ $localUser->server_username ? 'text-gray-900' : 'text-red-600 font-medium' }}">
                                                {{ $localUser->server_username ?: 'Not configured' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                                <div>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Local Email</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $localUser->email ?: 'Not set' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Last Synced</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $localUser->updated_at?->diffForHumans() }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center mb-4">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-sm text-yellow-600 font-medium">User is not synced locally</span>
                            </div>
                            <p class="text-sm text-gray-600">This user exists in Authentik but has not been synced to the local database. Run a sync before enabling PIM features.</p>
                        @endif
                    </div>
                </div>
            @elseif($activeTab === 'groups')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Groups</h3>
                        @if(count($groups) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($groups as $group)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900">{{ $group['name'] }}</h4>
                                        @if(isset($group['parent_name']) && $group['parent_name'])
                                            <p class="text-sm text-gray-500 mt-1">Parent: {{ $group['parent_name'] }}</p>
                                        @endif
                                        @if($group['is_superuser'])
                                            <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Superuser Group
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">This user is not a member of any groups.</p>
                        @endif
                    </div>
                </div>
            @elseif($activeTab === 'attributes')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Attributes</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($authentikUser['attributes'], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            @elseif($activeTab === 'pim')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Privileged Identity Management</h3>
                                <p class="text-sm text-gray-600">Grant time-bound elevated roles on the dedicated server. Actions are fully audited.</p>
                            </div>
                            @if($localUser && $localUser->server_username)
                                <span class="text-sm text-gray-500">Server account: <span class="font-mono text-gray-800">{{ $localUser->server_username }}</span></span>
                            @endif
                        </div>

                        @if(!$pimEnabled)
                            <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">
                                PIM is disabled. Set <code>PIM_ENABLED=true</code> in your environment to allow privileged activations.
                            </div>
                        @elseif(!$localUser)
                            <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">
                                Sync this user locally before activating roles.
                            </div>
                        @elseif(!$pimOperational)
                            <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded space-y-2">
                                <p>PIM server connectivity is incomplete. Configure the SSH details via environment variables:</p>
                                <ul class="list-disc list-inside text-sm">
                                    <li><code>PIM_SERVER_HOST</code></li>
                                    <li><code>PIM_SERVER_USER</code></li>
                                    <li><code>PIM_SERVER_IDENTITY_FILE</code> or equivalent authentication</li>
                                </ul>
                            </div>
                        @elseif($serverUsernameMissing)
                            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                                Provide the server username on the <a href="{{ route('users.edit', $authentikUser['pk']) }}" class="underline font-medium">Edit User</a> page before requesting privileged access.
                            </div>
                        @else
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                @forelse($pimRoles as $role)
                                    @php $activation = $role['active_activation']; @endphp
                                    <div class="border border-gray-200 rounded-lg p-5 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="text-base font-semibold text-gray-900">{{ $role['label'] }}</h4>
                                                @if($activation)
                                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Available</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $role['description'] }}</p>
                                            <div class="mt-3 text-xs text-gray-500 space-y-1">
                                                <p>Group: <span class="font-mono text-gray-800">{{ $role['group'] }}</span></p>
                                                <p>Duration window: {{ $role['minimum_duration_minutes'] }}-{{ $role['max_duration_minutes'] }} minutes</p>
                                            </div>
                                        </div>

                                        @if($activation)
                                            <div class="mt-4 space-y-3">
                                                <div class="bg-green-50 border border-green-200 rounded p-3 text-sm text-green-800 space-y-1">
                                                    <p><strong>Reason:</strong> {{ $activation->reason }}</p>
                                                    <p><strong>Activated:</strong> {{ $activation->activated_at?->format('M j, Y g:i A') }} ({{ $activation->activated_at?->diffForHumans() }})</p>
                                                    <p><strong>Expires:</strong> {{ $activation->expires_at?->format('M j, Y g:i A') }} ({{ $activation->expires_at?->diffForHumans() }})</p>
                                                    <p><strong>Server account at activation:</strong> {{ $activation->server_username_snapshot }}</p>
                                                </div>
                                                <form method="POST" action="{{ route('users.pim.deactivate', ['id' => $authentikUser['pk'], 'activation' => $activation->id]) }}" class="space-y-3">
                                                    @csrf
                                                    <div>
                                                        <label for="deactivate_reason_{{ $activation->id }}" class="block text-sm font-medium text-gray-700">Reason (optional)</label>
                                                        <input type="text" name="reason" id="deactivate_reason_{{ $activation->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="e.g., Access no longer required">
                                                    </div>
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                        Revoke Access
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="mt-4">
                                                <button type="button"
                                                        class="activate-role-btn bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                                                        data-role="{{ $role['key'] }}">
                                                    Activate Access
                                                </button>
                                                <div id="activate-form-{{ $role['key'] }}" class="activate-form mt-4 hidden">
                                                    <form method="POST" action="{{ route('users.pim.activate', ['id' => $authentikUser['pk']]) }}" class="space-y-4">
                                                        @csrf
                                                        <input type="hidden" name="role" value="{{ $role['key'] }}">
                                                        <div>
                                                            <label for="duration_{{ $role['key'] }}" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                                                            <input type="number"
                                                                   name="duration_minutes"
                                                                   id="duration_{{ $role['key'] }}"
                                                                   min="{{ $role['minimum_duration_minutes'] }}"
                                                                   max="{{ $role['max_duration_minutes'] }}"
                                                                   value="{{ $role['default_duration_minutes'] }}"
                                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                                   required>
                                                            <p class="mt-1 text-xs text-gray-500">Allowed range: {{ $role['minimum_duration_minutes'] }} to {{ $role['max_duration_minutes'] }} minutes.</p>
                                                        </div>
                                                        <div>
                                                            <label for="reason_{{ $role['key'] }}" class="block text-sm font-medium text-gray-700">Reason</label>
                                                            <textarea name="reason" id="reason_{{ $role['key'] }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required placeholder="Explain why elevated access is required."></textarea>
                                                        </div>
                                                        <div class="flex justify-end gap-3">
                                                            <button type="button" class="cancel-activate text-sm text-gray-600 hover:text-gray-800 px-3 py-2">Cancel</button>
                                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200 inline-flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Submit Request
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="col-span-full bg-gray-50 border border-dashed border-gray-200 rounded-lg p-6 text-center text-sm text-gray-500">
                                        No privileged roles are configured.
                                    </div>
                                @endforelse
                            </div>
                        @endif

                        <div>
                            <h4 class="text-base font-medium text-gray-900 mb-3">Activation History</h4>
                            @if($localUser && $pimActivations->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activated</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deactivated</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Initiated By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($pimActivations as $activation)
                                                <tr>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($activation->role) }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap">
                                                        @php
                                                            $statusClasses = [
                                                                'active' => 'bg-green-100 text-green-800',
                                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                                'revoked' => 'bg-gray-100 text-gray-800',
                                                                'expired' => 'bg-blue-100 text-blue-800',
                                                                'failed' => 'bg-red-100 text-red-800',
                                                            ];
                                                        @endphp
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$activation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                            {{ ucfirst($activation->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2 text-sm text-gray-600 max-w-xs">{{ $activation->reason }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $activation->activated_at?->format('M j, Y g:i A') }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $activation->expires_at?->format('M j, Y g:i A') }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        @if($activation->deactivated_at)
                                                            {{ $activation->deactivated_at->format('M j, Y g:i A') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                        {{ optional($activation->initiatedBy)->name ?? 'System' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No PIM activations recorded yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.querySelector('.delete-user-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    deleteUser(userId, username);
                });
            }

            const recoveryBtn = document.querySelector('.send-recovery-btn');
            if (recoveryBtn) {
                recoveryBtn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const username = this.dataset.username;
                    const email = this.dataset.email;
                    sendPasswordRecovery(userId, username, email);
                });
            }

            document.querySelectorAll('.activate-role-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const role = this.dataset.role;
                    const target = document.getElementById(`activate-form-${role}`);
                    if (target) {
                        target.classList.toggle('hidden');
                        if (!target.classList.contains('hidden')) {
                            const textarea = target.querySelector('textarea[name="reason"]');
                            if (textarea) {
                                textarea.focus();
                            }
                        }
                    }
                });
            });

            document.querySelectorAll('.activate-form .cancel-activate').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.activate-form');
                    if (container) {
                        container.classList.add('hidden');
                    }
                });
            });

            function deleteUser(userId, username) {
                if (!confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
                    return;
                }

                fetch(`{{ url('users') }}/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("users.index") }}';
                        }, 1000);
                    } else {
                        showMessage(data.message || 'Failed to delete user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete user error:', error);
                    showMessage('Failed to delete user: ' + error.message, 'error');
                });
            }

            function sendPasswordRecovery(userId, username, email) {
                if (!confirm(`Send password recovery link to ${username} (${email})?`)) {
                    return;
                }

                const recoveryBtn = document.querySelector('.send-recovery-btn');
                const originalText = recoveryBtn.innerHTML;
                recoveryBtn.disabled = true;
                recoveryBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                `;

                fetch(`{{ url('users') }}/${userId}/send-recovery`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                    } else {
                        showMessage(data.message || 'Failed to send password recovery email', 'error');
                    }
                })
                .catch(error => {
                    console.error('Send recovery error:', error);
                    showMessage('Failed to send password recovery email: ' + error.message, 'error');
                })
                .finally(() => {
                    recoveryBtn.disabled = false;
                    recoveryBtn.innerHTML = originalText;
                });
            }

            function showMessage(message, type) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-4 px-4 py-3 rounded relative ${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'}`;
                messageDiv.innerHTML = `<span class="block sm:inline">${message}</span>`;

                const mainContent = document.querySelector('.py-12 .max-w-7xl');
                if (mainContent) {
                    mainContent.insertBefore(messageDiv, mainContent.firstChild);
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 5000);
                }
            }
        });
    </script>
</x-app-layout>
