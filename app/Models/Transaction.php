<?php

namespace App\Models;

use App\Traits\CreateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, CreateUuid, SoftDeletes;
    
    protected $guarded = [];

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $hidden = [
        'user_id',
        'id',
        'updated_at',
        'deleted_at'
    ];
}