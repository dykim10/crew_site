<?php

namespace App\Http\Controllers;

use App\Models\PlanningFeedback;
use Illuminate\Http\Request;

class PlanningFeedbackController extends Controller
{
    public function showEventAType()
    {
        $feedbacks = PlanningFeedback::where('document_key', 'event_a_type')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('planning.event_a_type', compact('feedbacks'));
    }

    public function storeEventAType(Request $request)
    {
        $data = $request->validate([
            'reviewer_name' => ['nullable', 'string', 'max:100'],
            'good_points'   => ['nullable', 'string', 'max:2000'],
            'bad_points'    => ['nullable', 'string', 'max:2000'],
            'questions'     => ['nullable', 'string', 'max:2000'],
        ], [
            'reviewer_name.max' => '이름은 100자 이내로 입력해 주세요.',
            'good_points.max'   => '2000자 이내로 입력해 주세요.',
            'bad_points.max'    => '2000자 이내로 입력해 주세요.',
            'questions.max'     => '2000자 이내로 입력해 주세요.',
        ]);

        if (empty($data['good_points']) && empty($data['bad_points']) && empty($data['questions'])) {
            return back()->withErrors(['general' => '의견을 하나 이상 입력해 주세요.'])->withInput();
        }

        PlanningFeedback::create(array_merge($data, ['document_key' => 'event_a_type']));

        return redirect()->route('testevent_a_type')->with('success', '피드백이 저장되었습니다. 감사합니다!');
    }
}
