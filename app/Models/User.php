<?php
// WHAT IS A MODEL?
// A model is a PHP class that Laravel uses to work with one database table.
// Laravel says: Whenever I need to work with the users table, I'll use the User model.

// WHAT DOES A MODEL DO?
// Suppose you write: User::all();
// Who knows how to get data from the users table?
// The User model knows how to do it. It runs the SQL query: SELECT * FROM users;

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Model import: Link (used by the `links()` relationship below)
use App\Models\Link;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELATIONSHIPS BETWEEN MODELS
     * Suppose you are User #3.
     *
     * Question:
     * How many links do you own?
     * One? Five? Thirty? We do not know yet.
     *
     * That is why this method exists:
     *
     * public function links()
     * {
     *     return $this->hasMany(Link::class);
     * }
     *
     * Meaning:
     * One User can have many Links.
     *
     * This is telling Laravel:
     * "Find all links where links.user_id = users.id."
     * meaning, Give me  every row from the links table where the user_id column matches this user's id.
     * It does not run the query yet.
     * It just teaches Laravel the relationship.
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }
}
