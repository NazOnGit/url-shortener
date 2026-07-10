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
                    {{-- CREATE SHORT LINK FORM
                         Flow:
                           User enters a URL
                           → POST /links (route('links.store'))
                           → LinkController::store()
                           → validate, generate short code, save, redirect
                    --}}
                    <form method="POST" action="{{ route('links.store') }}">

                        {{-- Generate a CSRF token so Laravel accepts this POST request. --}}
                        @csrf

                        <label for="original_url" class="block mb-2 font-medium">
                            Original URL
                        </label>

                        <div class="flex items-end gap-3">

                            <input
                                type="url"
                                id="original_url"
                                name="original_url"
                                placeholder="Enter original URL"
                                required
                                class="w-full h-11 px-3 border rounded">

                            <button
                                type="submit"
                                class="h-11 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 whitespace-nowrap">
                                Create Short Link
                            </button>

                        </div>

                    </form>

                    {{-- START: Display links that belong to the authenticated user. --}}
                    <h2 class="mt-8 text-lg font-semibold mb-4">Your Links</h2>

                    <table class="w-full border border-gray-300 border-collapse">

                        <thead class="bg-gray-100">

                            <tr>
                                <th class="border border-gray-300 p-2">#</th>
                                <th class="border border-gray-300 p-2">Original URL</th>
                                <th class="border border-gray-300 p-2">Short URL</th>
                                <th class="border border-gray-300 p-2">Clicks</th>
                                <th class="border border-gray-300 p-2 text-center">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            {{-- Loop through every link passed from LinkController::index().

                                 Each `$link` represents one row from the `links` table.
                            --}}
                            @forelse ($links as $link)

                            <tr>

                                {{-- Display links.id --}}
                                <td class="border border-gray-300 p-2">
                                    {{ $link->id }}
                                </td>

                                {{-- Display links.original_url --}}
                                <td class="border border-gray-300 p-2 break-all max-w-xs">
                                    {{ $link->original_url }}
                                </td>

                                {{-- Display the full shortened URL created from links.short_code --}}
                                <td class="border border-gray-300 p-2">
                                    <a href="{{ url($link->short_code) }}" target="_blank">
                                        {{ url($link->short_code) }}
                                    </a>
                                </td>

                                {{-- Display the temporary clicks_count property added by withCount('clicks'). --}}
                                <td class="border border-gray-300 p-2 text-center">
                                    {{ $link->clicks_count }}
                                </td>

                                <td class="border border-gray-300 p-2 text-center">

                                    {{-- Open the statistics page for the current link. --}}
                                    <a href="{{ route('links.stats', $link->id) }}">
                                        <button
                                            type="button"
                                            class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mr-2">
                                            Statistics
                                        </button>
                                    </a>

                                    {{-- Delete the current link. --}}
                                    <form
                                        action="{{ route('links.destroy', $link->id) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this link?');">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="5" class="border border-gray-300 p-4 text-center">
                                    You haven't created any links yet.
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>