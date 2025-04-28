<?php

namespace Alisons\Caller\Http\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    protected $fillable = [
        'call_started',
        'user_id',
        'lead',
        'call_type',
        'call_time',
        'start_time',
        'duration',
        'call_terminated_by'
    ];


    public function scopeSearch($query, $filters)
    {
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['lead'])) {
            $query->where('lead', 'like', '%' . $filters['lead'] . '%');
        }

        if (!empty($filters['call_type'])) {
            $query->where('call_type', 'like', '%' . $filters['call_type'] . '%');
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('call_time', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('call_time', '<=', $filters['end_date']);
        }

        return $query;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
