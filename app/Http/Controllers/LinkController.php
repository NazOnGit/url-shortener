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
        $link = Link::where('short_code', $shortCode)->firstOrFail();



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
            ->latest()
            ->get();

        // Pass the links to the dashboard view so it can display them.
        return view('dashboard', ['links' => $links]);
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
}
