<?php
// JOB OF THE CONTROLLER
//
// Laravel calls this controller after matching a request in routes/web.php.
// This controller handles everything related to creating and managing shortened links.
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Link;
// Import Click model to record analytics: when a visitor clicks a shortened link, we store that event in the clicks table.
use App\Models\Click;

class LinkController extends Controller
{


    /**
     * Route: GET /{shortCode} → LinkController::redirect($shortCode)
     * Redirect the visitor to the original URL for a given short code.
     * This method is called when the visitor opens a shortened link in their browser.
     * No Request object is needed because the browser is not submitting form data.
     */
    public function redirect($shortCode)
    {
        // Link:: represents the link table and tells laravel i wanna work with this table.
        // $shortCode comes from Route::get('/{shortCode}' which is the value that the visitor typed in the browser after the domain name and forward slash /.
        // Search the links table for the row where the short_code column equals "eR7aM8" from the url route parameter.
        // short_code = Which column should be searched?
        // $shortCode = What value should the column be compared against?
        // $link now holds the matching row from the links table as a Link model object.
        // $link now contains:
        //   id | user_id | original_url | short_code
        //  8  |   3     | google.com   | eR7aM8
        // THIS EXISTS ONLY INSIDE THE $link VARIABLE OBJECT. IT DOES NOT CHANGE THE DATABASE. IT IS NOT A COLUMN IN THE LINKS TABLE. IT IS A TEMPORARY OBJECT PROPERTY THAT LIVES ONLY IN MEMORY.
        $link = Link::where('short_code', $shortCode)->firstOrFail();

        // Record the click in the clicks table for analytics purposes.
        // Click:: represents the clicks table and tells laravel i wanna work with this table.
        // after a visitor clicks a shortened link, route is matched and controller is called, $link now holds the row from the links table that matches the short code.
        // Create a new row in the clicks table
        // from $link object row, use the column of link_id in clicks to store the id coming from the links table OR $link object row, and use the column of ip_address to store the visitor's IP address.
        Click::create([
            'link_id' => $link->id,
            'ip_address' => request()->ip(),
        ]);

        // Redirect the visitor to the original URL stored for this short code.
        return redirect($link->original_url);
    }


    /**
     * Display a listing of the resource from storage.
     * Only show links that belong to the currently logged-in user.
     * This method is called when the user visits the dashboard.
     * No Request object is needed because the browser is not submitting form data.
     */
    public function index()
    {
        // Get all links for the currently logged-in user from the session.
        $links = Link::where('user_id', auth()->id())
            // use withCount('clicks') to get the number of clicks for each link.
            // use the 'clicks()' relationship defined in the Link model to count the number of clicks for each link.
            // For every row retrieved from the `links` table,
            // Laravel searches the `clicks` table for rows where:
            // clicks.link_id = links.id
            // Laravel counts how many matching rows of link_id column that matches the link's id column in the links table, and adds 
            // Example: 
            // links.id = 5 (the link's id column in the links table)
            // clicks.link_id (the link_id column in the clicks table) = 5
            // 5 (number of rows in the clicks table where link_id = 5 clicked) = 1
            // 5 (number of rows in the clicks table where link_id = 5 clicked) = 2
            // 5 (number of rows in the clicks table where link_id = 5 clicked) = 3
            // Result: 
            // $link->clicks_count = 3
            // the total clicks are added as a new property called `clicks_count` to each $link object.
            // so $link now is:
            //  id | user_id | original_url | short_code | clicks_count
            //  8  |   3     | google.com   | eR7aM8     | 5
            // THIS EXISTS ONLY INSIDE THE $link VARIABLE OBJECT. IT DOES NOT CHANGE THE DATABASE. 
            //IT IS NOT A COLUMN IN THE LINKS TABLE. IT IS A TEMPORARY OBJECT PROPERTY THAT LIVES ONLY IN MEMORY.
            ->withCount('clicks')
            ->latest()
            ->get();

        // Pass the $link objects to the dashboard view so it can display them.
        return view('dashboard', ['links' => $links]);
    }


    /**
     * Show the analytics page for a specific link.
     *
     * This method is called when the user visits:
     * /links/{link}/stats
     *
     * No Request object is needed because the browser is not
     * submitting form data.
     *
     * Link (capital L) is the model class that Laravel uses
     * to work with the `links` table.
     *
     * Before this method starts, Laravel uses the `{link}`
     * route parameter from routes/web.php to search:
     *
     * links.id = {link}
     *
     * If a matching row is found, Laravel creates a Link model
     * object containing that row and passes it to `$link`.
     *
     * $link (lowercase l) now holds the retrieved row from the
     * `links` table, including columns such as:
     *
     * id
     * user_id
     * original_url
     * short_code
     *
     * This method retrieves all click rows that belong to
     * the current link, orders them by the latest click first,
     * and passes both the link and its clicks to the stats view.
     */

    public function stats(Link $link)
    {
        // `$link` already holds the matching row from the `links` table.
        // Call the `clicks()` relationship defined in Link.php.
        // The `clicks()` relationship uses:
        // return $this->hasMany(Click::class);
        // which tells Laravel:
        // "Search the `clicks` table for rows where:
        // clicks.link_id = links.id"
        // Since `$link` already holds one row from the `links` table,
        // Laravel uses:
        // links.id = $link->id
        // Example:
        // links table
        // id
        // 8
        //
        // clicks table
        // link_id
        // 8
        // 8
        // 8
        //
        // Laravel retrieves every matching row from the `clicks` table,
        // orders them from newest to oldest using `latest()`,
        // and stores the resulting collection of Click model objects
        // in the `$clicks` variable.
        $clicks = $link->clicks()->latest()->get();

        // Send the link and its click history to the stats view.
        return view('links.stats', [
            'link' => $link,
            'clicks' => $clicks,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     * Request is needed because the browser is sending data to the server when the user submits the "Create Link" form.
     * Browser submits form → Laravel creates Request → store(Request $request) receives the submitted data.
     */
    public function store(Request $request)
    {
        // Validate the submitted URL before Laravel creates a short link.
        // If validation fails, Laravel redirects the user back and does not run the code below.
        $validatedData = $request->validate([
            'original_url' => ['required', 'url', 'max:2048']
        ]);

        // Store the validated URL in a variable so the create logic uses checked data.
        $originalUrl = $validatedData['original_url'];

        // Generate a random 6-character code for the shortened URL.
        // Browser submits URL → Controller receives URL → Controller generates short code → Controller tells the Link model to save everything.

        $shortCode = Str::random(6);

        // Create a new row in the `links` table with Link model being responsible for working with anything link-related.
        // because it is a new row, we use Link::create() instead of Link::update().
        Link::create([
            // The ID of the currently logged-in user.
            'user_id' => auth()->id(),
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);

        // Redirect the user back to the dashboard and store
        // a success message in the session for the next page load.
        return redirect()
            ->route('dashboard')
            ->with('success', 'Link created successfully!');
    }



    public function destroy(Link $link)
    {


        // // Delete the row from the `links` table where links.id matches {link} placeholder in the URL.
        $link->delete();

        // Redirect back to the dashboard with a success message.
        return redirect()
            ->route('dashboard')
            ->with('success', 'Link deleted successfully!');
    }
}
