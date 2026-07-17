<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Branch;
use App\Models\Generation;
use App\Services\GenerationVisibilityService;
use Illuminate\View\View;

class GenerationController extends Controller
{
    public function __construct(private GenerationVisibilityService $visibility) {}

    public function show(): View
    {
        $generations = $this->visibility->visibleGenerations();

        $cards = $generations->map(function (Generation $g) {
            $branchIds = $g->active_branch_ids ?? [];
            $branches = Branch::query()
                ->where('status', 'active')
                ->when($branchIds, fn ($q) => $q->whereIn('id', $branchIds))
                ->orderBy('name')
                ->get(['id', 'name']);

            $counts = [];
            foreach ($branches as $branch) {
                $counts[$branch->id] = Application::query()
                    ->where('generation_id', $g->id)
                    ->where('branch_id', $branch->id)
                    ->count();
            }

            return [
                'generation'  => $g,
                'branches'    => $branches,
                'counts'      => $counts,
                'recruiting'  => $this->visibility->isRecruiting($g),
                'main_races'  => $g->mainRacesList(),
            ];
        });

        return view('generation.show', [
            'cards' => $cards,
            'empty' => $cards->isEmpty(),
        ]);
    }
}
