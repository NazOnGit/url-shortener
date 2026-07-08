<?php
// WHAT IS A MODEL?
// A model is a PHP class that Laravel uses to work with one database table.
// Laravel says: Whenever I need to work with the clicks table, I'll use the Click model.

// WHAT DOES A MODEL DO?
// Suppose you write: Click::all();
// Who knows how to get data from the clicks table?
// The Click model knows how to do it. It runs the SQL query: SELECT * FROM clicks;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Link;

class Click extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'link_id',
        'ip_address',
        'created_at',
    ];

    /**
     * RELATIONSHIPS BETWEEN MODELS
     * Why does Click know about Link? Because a click belongs to a link.
    
     * Imagine this row in the links table:
     *   id | link_id
     *   8  |   3 
     *
     * The link only stores link_id = 3.
     * It does not store anything else about Link #3.
     * The Link data is in the links table.
     *
     * So this relation tells Laravel:
     *   "Go find the link record where links.id = clicks.link_id."
     *
     * It does not run a database query by itself.
     * It just teaches Laravel how the models are connected.
     * After that, you can do:
     *   $click->link
     * and Laravel will fetch the correct Link data.
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
