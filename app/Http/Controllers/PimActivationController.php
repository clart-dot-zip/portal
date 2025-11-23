<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pim\SelfActivatePimRequest;
use App\Http\Requests\Pim\SelfDeactivatePimRequest;
use App\Models\PimActivation;
use App\Services\Pim\Exceptions\PimException;
use App\Services\Pim\PimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PimActivationController extends Controller
{
    private PimService $pimService;

    public function __construct(PimService $pimService)
    {
        $this->pimService = $pimService;
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $groups = collect();
        $recentActivations = collect();

        if ($user && $this->pimService->isEnabled() && $this->pimService->isOperational()) {
            $groups = $this->pimService->groupsForUser($user);
            $recentActivations = $user->pimActivations()
                ->with('pimGroup')
                ->latest('activated_at')
                ->limit(15)
                ->get();
        }

        return view('pim.activation', [
            'currentUser' => $user,
            'groups' => $groups,
            'recentActivations' => $recentActivations,
            'pimEnabled' => $this->pimService->isEnabled(),
            'pimOperational' => $this->pimService->isOperational(),
        ]);
    }

    public function store(SelfActivatePimRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!$this->pimService->isEnabled() || !$this->pimService->isOperational()) {
            return back()->with('error', 'Privileged access is not available right now.');
        }

        $group = $this->pimService->getGroupById($request->integer('pim_group_id'));

        $hasAssignment = $user->pimGroups()->where('pim_group_id', $group->id)->exists();

        if (!$hasAssignment) {
            return back()->with('error', 'You are not assigned to that PIM group.');
        }

        $duration = (int) $request->integer('duration_minutes');
        $reason = (string) $request->string('reason');

        try {
            $this->pimService->activate($user, $group, $duration, $reason, $user);
        } catch (PimException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'PIM access activated.');
    }

    public function deactivate(SelfDeactivatePimRequest $request, PimActivation $activation): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if ($activation->user_id !== $user->id) {
            abort(403);
        }

        $reason = $request->filled('reason')
            ? 'User ended session: ' . $request->string('reason')->toString()
            : 'User ended session';

        try {
            $this->pimService->deactivate($activation, $reason, $user);
        } catch (PimException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'PIM access revoked.');
    }
}
