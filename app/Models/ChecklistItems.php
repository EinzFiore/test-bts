<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItems extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'checklist_id',
        'item_id',
        'status'
    ];

    public function scopeByChecklist($query, string $checkListId)
    {
        return $query->whereChecklistId($checkListId);
    }

    public function item()
    {
        return $this->belongsTo(MItems::class, 'item_id', 'id');
    }

}
