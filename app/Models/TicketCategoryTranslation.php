<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ticket_category_id',
        'locale',
        'name',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(TicketCategory::class);
    }
}
