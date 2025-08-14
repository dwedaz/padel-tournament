<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Field Details') }}
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Field Details: {{ $field->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('fields.edit', $field) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Field
                </a>
                <a href="{{ route('fields.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Fields
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Field Information -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Field Information</h2>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Field ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $field->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $field->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $field->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $field->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Games</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $field->games()->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Active Games</dt>
                                @php
                                    $activeGames = $field->games()->whereIn('status', ['pending', 'playing'])->count();
                                @endphp
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $activeGames > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $activeGames }} active
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Actions</h2>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <a href="{{ route('fields.edit', $field) }}" 
                           class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                            Edit Field
                        </a>
                        
                        @php
                            $canDelete = $field->games()->whereIn('status', ['pending', 'playing'])->count() == 0;
                        @endphp
                        
                        @if($canDelete)
                            <form action="{{ route('fields.destroy', $field) }}" method="POST" class="block"
                                  onsubmit="return confirm('Are you sure you want to delete this field? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Delete Field
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-100 text-gray-500 font-bold py-2 px-4 rounded text-center cursor-not-allowed">
                                Cannot Delete (Active Games)
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Games List -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Games on this Field</h2>
                    </div>
                    
                    @if($field->games()->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Game</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teams</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($field->games()->latest()->take(10)->get() as $game)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $game->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="space-y-1">
                                                    <div>{{ $game->team1->name ?? 'N/A' }}</div>
                                                    <div>{{ $game->team2->name ?? 'N/A' }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="space-y-1">
                                                    <div>{{ $game->team1_score ?? 0 }}</div>
                                                    <div>{{ $game->team2_score ?? 0 }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'playing' => 'bg-blue-100 text-blue-800',
                                                        'finished' => 'bg-green-100 text-green-800',
                                                        'cancelled' => 'bg-gray-100 text-gray-800'
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$game->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($game->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $game->created_at->format('M d, H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($field->games()->count() > 10)
                            <div class="px-6 py-3 bg-gray-50 text-sm text-gray-500 text-center">
                                Showing latest 10 games. Total games: {{ $field->games()->count() }}
                            </div>
                        @endif
                    @else
                        <div class="px-6 py-4 text-center text-gray-500">
                            No games found for this field.
                        </div>
                    @endif
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
