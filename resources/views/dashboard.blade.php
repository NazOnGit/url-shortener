<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>

                {{-- Display the success message stored in the session after the user
                     is redirected back to the dashboard. --}}
                @if (session('success'))
                <div class="p-6 bg-green-50 border border-green-200 text-green-800">
                    {{ session('success') }}
                </div>
                @endif

                <!-- INSERT POINT: dashboard form starts below -->
                <!-- Flow: -->
                <!-- dashboard form submits → links.store route → LinkController store() -->
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <form method="POST" action="{{ route('links.store') }}">
                        @csrf

                        <input
                            type="url"
                            name="original_url"
                            placeholder="Enter original URL"
                            required
                            class="w-full border rounded p-2">

                        <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded">
                            Create short link
                        </button>
                    </form>

                    {{-- START: display links that belong to the authenticated user --}}
                    <h2>Your Links</h2>

                    {{-- Loop through every link passed to this view by LinkController@index. --}}
                    @forelse ($links as $link)

                    <p>Clicks: {{ $link->clicks_count }}</p>

                    {{-- Create a link to the statistics page for the current link.
                         
                         route('links.stats', $link->id)
                         
                         uses the route:
                         
                         /links/{link}/stats
                         
                         and replaces the `{link}` placeholder with the current
                         link's id from the `links` table.
                         
                         Example:
                         
                         links.id = 8 points to /links/8/stats

                         FLOW:
                         
                         Dashboard -> Click "View Statistics" -> route('links.stats', $link->id) -> /links/8/stats -> LinkController::stats($link) -> stats.blade.php
                    --}}
                    <a href="{{ route('links.stats', $link->id) }}">
                        View Statistics
                    </a>

                    <div>

                        {{-- Display one shortened link. --}}
                        <p>{{ $link->original_url }}</p>
                        <a href="{{ url($link->short_code) }}" target="_blank">{{ url($link->short_code) }}</a>
                    </div>

                    @empty
                    {{-- Display a message when the user has not created any links. --}}
                    <p>You haven't created any links yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>