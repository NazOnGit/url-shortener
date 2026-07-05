<?php
// WHAT IS A MODEL?
// A model is a PHP class that Laravel uses to work with one database table.
// Laravel says: Whenever I need to work with the links table, I'll use the Link model.

// WHAT DOES A MODEL DO?
// Suppose you write: Link::all();
// Who knows how to get data from the links table?
// The Link model knows how to do it. It runs the SQL query: SELECT * FROM links;



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model import: User (used by the `user()` relationship below)
use App\Models\User;
// Model import: Click (used by the `clicks()` relationship below)
use App\Models\Click;

class Link extends Model
{
    /**
     * Allow Laravel to assign values to these columns when creating a new link.
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
    ];

    /**
     * RELATIONSHIPS BETWEEN LINK AND USER MODELS
     * Why does Link know about User? Because a link belongs to a user.
    
     * Imagine this row in the links table:
     *   id | user_id | original_url
     *   8  |   3     | google.com
     *
     * The link only stores user_id = 3.
     * It does not store anything else about User #3.
     * The User data is in the users table.
     *
     * So this relation tells Laravel:
     *   "Go find the user record where users.id = links.user_id."
     *
     * It does not run a database query by itself.
     * It just teaches Laravel how the models are connected.
     * After that, you can do:
     *   $link->user
     * and Laravel will fetch the correct User.
     * Meaning Look at this link's user_id column, find the user whose id matches it, and return that user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATIONSHIPS BETWEEN LINK AND CLICK MODELS
     * Why does Link know about Click? Because a link can have many clicks.
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }
}
