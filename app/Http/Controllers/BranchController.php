<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Branch;
use App\Models\Generation;
use App\Services\ApplicationService;
use App\Services\GenerationVisibilityService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(private GenerationVisibilityService $visibility) {}

    public function index()
    {
        $branches    = Branch::where('status', 'active')->with(['admin', 'operator'])->orderBy('name')->get();
        $branchCount = $branches->count();

        return view('branch.index', compact('branches', 'branchCount'));
    }

    public function show(Request $request, Branch $branch)
    {
        abort_unless($branch->status === 'active', 404);

        $branch->load(['admin', 'operator']);

        $visible = $this->visibility->visibleGenerations();
        $pastGenerations = Generation::query()
            ->whereNotIn('id', $visible->pluck('id'))
            ->orderByDesc('number')
            ->get(['id', 'number', 'alias', 'status']);

        $filterGenId = $request->query('generation');
        $selectedGeneration = null;
        $usingPastFilter = false;

        if (filled($filterGenId)) {
            $selectedGeneration = Generation::find((int) $filterGenId);
            if ($selectedGeneration && ! $visible->contains('id', $selectedGeneration->id)) {
                $usingPastFilter = true;
            }
        }

        $generationIds = $usingPastFilter && $selectedGeneration
            ? collect([$selectedGeneration->id])
            : $visible->pluck('id');

        $memberCount = 0;
        $displayNames = collect();

        if ($generationIds->isNotEmpty()) {
            // 원장 = applications (회원 가입 여부 무관)
            $apps = Application::query()
                ->with('matchedUser')
                ->where('branch_id', $branch->id)
                ->whereIn('generation_id', $generationIds)
                ->whereNotNull('generation_id')
                ->orderBy('id')
                ->get();

            $memberCount = $apps->count();

            if (auth()->check() && $memberCount > 0) {
                $cryptoService = app(ApplicationService::class);
                $displayNames = $apps->map(function (Application $app) use ($cryptoService) {
                    if ($app->matchedUser?->nickname) {
                        return $app->matchedUser->nickname;
                    }
                    $pii = $cryptoService->decryptPii($app);

                    return ($pii['name'] !== '-' ? $pii['name'] : null);
                })->filter()->unique()->sort()->values();
            }
        }

        return view('branch.show', [
            'branch'              => $branch,
            'memberCount'         => $memberCount,
            'nicknames'           => $displayNames,
            'showRoster'          => auth()->check(),
            'visibleGenerations'  => $visible,
            'pastGenerations'     => $pastGenerations,
            'selectedGeneration'  => $usingPastFilter ? $selectedGeneration : null,
            'usingPastFilter'     => $usingPastFilter,
        ]);
    }
}
