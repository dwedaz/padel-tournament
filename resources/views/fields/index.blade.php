<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fields') }}
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manage Fields</h1>
        @if(App\Models\Field::count() < 5)
            <a href="{{ route('fields.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Field
            </a>
        @else
            <span class="bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                Maximum Fields (5/5)
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Games</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($fields as $field)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $field->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $field->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                $activeGames = $field->games()->whereIn('status', ['pending', 'playing'])->count();
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $activeGames > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $activeGames }} active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $field->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('fields.show', $field) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                                <a href="{{ route('fields.edit', $field) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                
                                @php
                                    $canDelete = $field->games()->whereIn('status', ['pending', 'playing'])->count() == 0;
                                @endphp
                                
                                @if($canDelete)
                                    <form action="{{ route('fields.destroy', $field) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this field?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed">Delete</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No fields found. <a href="{{ route('fields.create') }}" class="text-blue-600 hover:text-blue-900">Create your first field</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Field Limits ({{ $fields->count() }}/5)
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Maximum 5 fields are allowed. Fields with active games cannot be deleted.</p>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
