<?php

namespace App\Http\Controllers;

use App\Models\PimActivation;
use App\Models\User;
use App\Services\Pim\Exceptions\PimException;
use App\Services\Pim\PimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PimController extends Controller
{
    private PimService $pimService;

    public function __construct(PimService $pimService)
    {
        $this->pimService = $pimService;
    }

    public function activate(Request $request, string $authentikId): RedirectResponse
    {
        $request->validate([
            'role' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'User must be synced locally before activating PIM roles.');
        }

        $roleKey = $request->string('role')->toString();
        $duration = (int) $request->integer('duration_minutes');
        $reason = (string) $request->string('reason');

        try {
            $this->pimService->activate($localUser, $roleKey, $duration, $reason, Auth::user());
        } catch (PimException $exception) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
            ->with('success', 'Privileged access activated successfully.');
    }

    public function deactivate(Request $request, string $authentikId, PimActivation $activation): RedirectResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $localUser = User::where('authentik_id', $authentikId)->first();

        if (!$localUser || $activation->user_id !== $localUser->id) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', 'Activation record does not match the selected user.');
        }

        $reason = $request->filled('reason')
            ? 'Manual revoke: ' . $request->string('reason')->toString()
            : 'Manually revoked';

        try {
            $this->pimService->deactivate($activation, $reason, Auth::user());
        } catch (PimException $exception) {
            return redirect()
                ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('users.show', ['id' => $authentikId, 'tab' => 'pim'])
            ->with('success', 'Privileged access revoked successfully.');
    }
}
