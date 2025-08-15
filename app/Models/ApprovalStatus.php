<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalStatus extends Model
{
    public function approvalAttendances()
    {
        return $this->hasMany(ApprovalAttendance::class, 'status_code', 'code');
    }

    public function approvalExpenses()
    {
        return $this->hasMany(ApprovalExpense::class, 'status_code', 'code');
    }
}
