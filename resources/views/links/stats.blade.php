{{-- Link Statistics Page --}}

<h1>Link Statistics</h1>

{{-- Display the original URL of the current link.

    `$link` was passed from LinkController::stats().

    `$link` holds the row retrieved from the `links` table where:

    links.id = {link} where {link} is the ID of the link the user wants to see stats for coming from the URL parameter in the route.

    `$link->original_url` accesses the `original_url` column of the row in the `links` table.
--}}
<p>{{ $link->original_url }}</p>

<h2>Click History</h2>

{{-- Loop through every click row passed from LinkController::stats().

    `$clicks` is a collection of Click model objects.

    Each `$click` represents one row from the `clicks` table.
--}}
@forelse ($clicks as $click)

<div>

  {{-- Display the IP address stored in the `clicks.ip_address` column. --}}
  <p>IP: {{ $click->ip_address }}</p>

  {{-- Display the date and time stored in the `clicks.created_at` column. --}}
  <p>Clicked: {{ $click->created_at }}</p>

</div>

@empty

{{-- Display this message when no click rows exist for the current link. --}}
<p>No clicks recorded.</p>

@endforelse