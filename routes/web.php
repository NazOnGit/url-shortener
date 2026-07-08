<?php
// WHAT IS THE RESPONSIBILITY OF A ROUTE?
//
// Receive HTTP requests from the browser and direct them
// to the appropriate controller method.
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Dashboard route: When the user visits /dashboard, Laravel calls LinkController@index to show the dashboard where the user can see all their links sent from index() method in LinkController. The index() method fetches all links for the currently logged-in user and passes them to the dashboard view.
// GET /dashboard → LinkController@index → dashboard.blade.php receives $links
Route::get('/dashboard', [LinkController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


// Require the user to be logged in before accessing any route in this group.
Route::middleware('auth')->group(function () {
    // When the user submits the "Create Link" form,
    // Laravel sends the request to LinkController::store().
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');

    // Match the route to show the analytics page for a specific link.
    // What the user types in the browser:
    // https://yoursite.com/links/5/stats
    // Where number 5 comes from: the user clicked a link, or typed it manually. It's the ID of a link they want to see stats for.
    // Route parameters: {link} is a placeholder for the link's ID.
    // Laravel looks at the {link} placeholder in the URL (/links/5/stats), sees the number 5, and looks for a row in the links table where id = 5.
    // If a matching row is found, Laravel passes that row to LinkController::stats() as the $link parameter.
    // That row becomes the $link object (an instance of the
    // Link model), which is passed into this method.
    // public function stats(Link $link) in LinkController.php receives the $link object and can access its properties, such as $link->original_url, $link->short_code, etc.
    Route::get('/links/{link}/stats', [LinkController::class, 'stats'])
        ->name('links.stats');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Delete Link Route
    //
    // When the logged-in user submits a delete request for /links/{link},
    // Laravel finds the row where links.id = {link}
    // and passes that row to LinkController::destroy().
    Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
});


// Load Breeze authentication routes first: /login, /register, /logout, etc.
require __DIR__ . '/auth.php';



// Public short-link redirect route must stay last.

// Redirect Route
//
// Visitor opens: http://127.0.0.1:8000/eR7aM8

// {shortCode} is dynamic and can be anything.
// {}: It is a placeholder for any string that comes after the domain name or  forward slash / in the URL. The {shortCode} is a route parameter that captures the value from the URL and passes it to the controller method as an argument. In this case, it will capture "eR7aM8" and pass it to LinkController::redirect($shortCode).
// Laravel assigns: {shortCode} = "eR7aM8"
// Laravel calls: LinkController::redirect($shortCode)
// The `$shortCode` parameter now contains: "eR7aM8" and is passed to the redirect() method in LinkController.
// LinkController asks the Link model to find the row in the `links` table
// where the `short_code` column matches "eR7aM8".
// If a matching row is found, Laravel redirects the visitor to the URL
// stored in the `original_url` column of that row.
// This route is placed outside the `auth` middleware group because
// shortened links must be accessible to everyone, not only logged-in users.
Route::get('/{shortCode}', [LinkController::class, 'redirect'])->name('links.redirect');
