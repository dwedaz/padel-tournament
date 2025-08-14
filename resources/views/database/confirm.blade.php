<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Database Operations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="max-w-xl mx-auto">
                        @if($action === 'clear-matches')
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <h3 class="text-lg font-bold text-orange-800">🎯 Clear Matches Only</h3>
                                </div>
                                <p class="text-orange-700 mb-4">
                                    This action will <strong>delete all matches/games</strong> but preserve teams, groups, and fields.
                                </p>
                                <ul class="text-orange-600 text-sm mb-6 space-y-1">
                                    <li>• All games and match results will be deleted</li>
                                    <li>• Teams, groups, and fields will be preserved</li>
                                    <li>• Tournament structure remains intact</li>
                                    <li>• Perfect for starting a new tournament with same teams</li>
                                    <li>• <strong>This action cannot be undone!</strong></li>
                                </ul>
                                
                                <div class="flex space-x-4">
                                    <form method="POST" action="{{ route('database.clear-matches') }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded"
                                                onclick="return confirm('Are you sure? This will delete all matches but keep teams and groups.')">
                                            🎯 Yes, Clear Matches Only
                                        </button>
                                    </form>
                                    <a href="{{ route('dashboard') }}" 
                                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        @elseif($action === 'reset')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <h3 class="text-lg font-bold text-red-800">⚠️ Reset Database</h3>
                                </div>
                                <p class="text-red-700 mb-4">
                                    This action will <strong>permanently delete ALL data</strong> in the database and recreate empty tables.
                                </p>
                                <ul class="text-red-600 text-sm mb-6 space-y-1">
                                    <li>• All teams, groups, games, and fields will be deleted</li>
                                    <li>• All user data will be preserved (users table not affected)</li>
                                    <li>• Tables will be recreated as empty</li>
                                    <li>• <strong>This action cannot be undone!</strong></li>
                                </ul>
                                
                                <div class="flex space-x-4">
                                    <form method="POST" action="{{ route('database.reset') }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                                onclick="return confirm('Are you absolutely sure? This will delete ALL tournament data!')">
                                            ⚠️ Yes, Reset Database
                                        </button>
                                    </form>
                                    <a href="{{ route('dashboard') }}" 
                                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <h3 class="text-lg font-bold text-blue-800">🔄 Reseed Database</h3>
                                </div>
                                <p class="text-blue-700 mb-4">
                                    This action will <strong>reset the database and populate it with sample data</strong> for development and testing.
                                </p>
                                <ul class="text-blue-600 text-sm mb-6 space-y-1">
                                    <li>• All current data will be deleted</li>
                                    <li>• Tables will be recreated</li>
                                    <li>• Sample teams, groups, games, and fields will be added</li>
                                    <li>• User data will be preserved (users table not affected)</li>
                                    <li>• <strong>This action cannot be undone!</strong></li>
                                </ul>
                                
                                <div class="flex space-x-4">
                                    <form method="POST" action="{{ route('database.reseed') }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                                onclick="return confirm('Are you sure you want to reseed the database with sample data?')">
                                            🔄 Yes, Reseed Database
                                        </button>
                                    </form>
                                    <a href="{{ route('dashboard') }}" 
                                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold text-gray-800 mb-2">💡 Development Tips:</h4>
                            <ul class="text-gray-600 text-sm space-y-1">
                                <li>• Use <strong>Clear Matches</strong> to delete games but keep teams/groups</li>
                                <li>• Use <strong>Reset</strong> to start with a completely clean database</li>
                                <li>• Use <strong>Reseed</strong> to populate with sample tournament data</li>
                                <li>• Your user account will remain intact in all cases</li>
                                <li>• These operations are intended for development use</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
