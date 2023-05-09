<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklists extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        'name'
    ];

    public function items()
    {
        return $this->belongsToMany(MItems::class, 'checklist_items', 'checklist_id', 'item_id');
    }
}
