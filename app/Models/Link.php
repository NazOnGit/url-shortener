<?php
// WHAT IS AN ELOQUENT MODEL?
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
     * The link table only stores user_id = 3.
     * The link object $link is an instance of the Link model, which represents a single row in the links table.
     * The links table is the actual database table that stores all the link records. The link object corresponds to a specific row in that table, and it has properties that represent the columns of that row, such as id, user_id, original_url, and short_code.
     * The user_id property in the Link model represents the FOREIGN KEY that connects the link to its owner in the users table. It indicates which user created or owns that specific link.
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
     * Meaning Look at this link's table user_id column, find the user whose id matches it, and return that user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATIONSHIPS BETWEEN LINK AND CLICK MODELS
     * Why does Link know about Click? Because a link can have many clicks.
     * 
     */
    public function clicks(): HasMany
    {
        // a link can have many clicks from different users, but a click belongs to one link or points back to a specific link.
        // the purpose of clicks table is to record every time a visitor clicks a shortened link, so we can grab data from the clicks table to see how many clicks each link has received by the number of rows created in the clicks table in which link_id column from clicks table is the same as the link's id from links table being the main row holding the link information such as the original URL and short_code.
        // different users can click the same link, and each click will be recorded in the clicks table as a new row with the same link_id but different ip_address and created_at timestamp.
        // if we dont do this, we still can count the number of clicks for a link but we would loose Which IP clicked? When did they click? The complete history of clicks.
        return $this->hasMany(Click::class);
    }
}
