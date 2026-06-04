<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersDetail;

class SkinController extends Controller
{
    public function change(Request $request)
    {
        $request->validate([
            'skin' => 'required|in:_skin_v1,_skin_v2',
        ]);

        $userDetail = UsersDetail::where('user_id', auth()->id())->first();

        if (!$userDetail) {
            UsersDetail::create([
                'user_id'     => auth()->id(),
                'skin_select' => $request->skin,
            ]);
        } else {
            $userDetail->update([
                'skin_select' => $request->skin,
            ]);
        }

        return response()->json([
            'success' => true,
            'skin'    => $request->skin,
            'message' => '스킨이 변경되었습니다.',
        ]);
    }
}
