<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalAttendance;
use Illuminate\Http\Request;

class AdminApprovalAttendanceController extends Controller
{
    public function index()
    {
        $approvals = ApprovalAttendance::with(['user', 'reviewer', 'approvalStatus'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->responseJson($approvals);
    }

    public function update(Request $request, ApprovalAttendance $approvalAttendance)
    {
        $request->validate([
            'status_code' => [
                'required',
                'string',
                'exists:approval_statuses,status_code',
            ],
        ]);
        $approvalAttendance->status_code = $request->status_code;
        $approvalAttendance->save();

        return $this->responseJson($approvalAttendance);
    }
}
