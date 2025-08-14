<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wasit - Select Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
      
        <!-- Match Selection/Creation Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Match Selection</h2>
            
            <!-- Match Type Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Match Type</label>
                <select id="match-type" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Select Match Type --</option>
                    <option value="qualification">Qualification</option>
                    <option value="Quarter-Final">Quarter-Final</option>
                    <option value="semifinal">Semi Final</option>
                    <option value="final">Final</option>
                </select>
            </div>

            <!-- Team Selection -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team 1</label>
                    <select id="team1" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select Team 1 --</option>
                        @php $currentGroup = null; @endphp
                        @foreach($teams as $team)
                            @if($currentGroup !== $team->group->name)
                                @if($currentGroup !== null)
                                    <option disabled>──────────</option>
                                @endif
                                @php $currentGroup = $team->group->name; @endphp
                            @endif
                            <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->group->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team 2</label>
                    <select id="team2" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select Team 2 --</option>
                        @php $currentGroup = null; @endphp
                        @foreach($teams as $team)
                            @if($currentGroup !== $team->group->name)
                                @if($currentGroup !== null)
                                    <option disabled>──────────</option>
                                @endif
                                @php $currentGroup = $team->group->name; @endphp
                            @endif
                            <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->group->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4 mb-6">
                <button id="show-existing" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                    Show Existing Matches
                </button>
                <button id="create-new" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                    Create New Match
                </button>
            </div>
            
            <!-- Existing Matches List -->
            <div id="existing-matches" class="hidden">
                <h3 class="font-semibold text-lg mb-3 text-gray-800">Existing Matches</h3>
                <div id="matches-list" class="space-y-3 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    <div class="text-gray-500 text-center py-4">Select teams and match type first, then click "Show Existing Matches"</div>
                </div>
            </div>

            <!-- Create New Match Form -->
            <div id="new-match-form" class="hidden">
                <h3 class="font-semibold text-lg mb-3 text-gray-800">Create New Match</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <!-- Field Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Field <span class="text-red-500">*</span></label>
                        <select id="field" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">-- Select Field --</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}">{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Game <span class="text-red-500">*</span></label>
                            <input type="number" id="set" min="1" value="1" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Enter set number">
                            <p class="text-xs text-gray-500 mt-1">Which set of the match</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"> Set <span class="text-gray-400">(Auto)</span></label>
                            <input type="number" id="game-set" value="1" readonly class="w-full p-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Auto-generated game number</p>
                        </div>
                    </div>
                    <button id="create-match-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold">
                        Create Match
                    </button>
                </div>
            </div>
        </div>

        

    <script>
        $(document).ready(function() {
            // Match Selection and Creation functionality
            $('#show-existing').on('click', function() {
                const matchType = $('#match-type').val();
                const team1Id = $('#team1').val();
                const team2Id = $('#team2').val();
                
                // Field is not required for showing existing matches
                if (!matchType || !team1Id || !team2Id) {
                    alert('Please select match type and both teams first.');
                    return;
                }
                
                if (team1Id === team2Id) {
                    alert('Please select different teams.');
                    return;
                }
                
                // Hide create form
                $('#new-match-form').addClass('hidden');
                
                // Show existing matches section
                $('#existing-matches').removeClass('hidden');
                
                // Load matches via AJAX
                loadExistingMatches(matchType, team1Id, team2Id);
            });
            
            $('#create-new').on('click', function() {
                const matchType = $('#match-type').val();
                const team1Id = $('#team1').val();
                const team2Id = $('#team2').val();
                
                // Only match type and teams are required to show the create form
                if (!matchType || !team1Id || !team2Id) {
                    alert('Please select match type and both teams first.');
                    return;
                }
                
                if (team1Id === team2Id) {
                    alert('Please select different teams.');
                    return;
                }
                
                // Hide existing matches
                $('#existing-matches').addClass('hidden');
                
                // Show create form and calculate game set
                $('#new-match-form').removeClass('hidden');
                calculateNextGameSet(team1Id, team2Id);
            });
            
            // Calculate next game set when set number changes
            $('#set').on('input change', function() {
                const team1Id = $('#team1').val();
                const team2Id = $('#team2').val();
                if (team1Id && team2Id) {
                    calculateNextGameSet(team1Id, team2Id);
                }
            });
            
            // Recalculate game set when match type changes
            $('#match-type').on('change', function() {
                const team1Id = $('#team1').val();
                const team2Id = $('#team2').val();
                
                // If both teams are selected and create form is visible, recalculate game set
                if (team1Id && team2Id && !$('#new-match-form').hasClass('hidden')) {
                    calculateNextGameSet(team1Id, team2Id);
                }
                
                // Update team2 options as well
                updateTeam2Options();
            });
            
            function calculateNextGameSet(team1Id, team2Id) {
                const setNumber = $('#set').val() || 1;
                const matchType = $('#match-type').val();
                
                // Make AJAX call to get existing games for these teams, set, and match type
                $.ajax({
                    url: '{{ route("wasit.matches") }}',
                    method: 'GET',
                    data: {
                        team1_id: team1Id,
                        team2_id: team2Id,
                        match_type: matchType  // Include match type in the query
                    },
                    success: function(response) {
                        let nextGameSet = 1;
                        
                        if (response.games.length > 0) {
                            // Filter games by both set number and match type
                            const filteredGames = response.games.filter(game => 
                                game.set == setNumber && game.name === matchType
                            );
                            
                            if (filteredGames.length > 0) {
                                const maxGameSet = Math.max(...filteredGames.map(game => game.game_set));
                                nextGameSet = maxGameSet + 1;
                            }
                        }
                        
                        $('#game-set').val(nextGameSet);
                        
                        // Debug logging
                        console.log('Calculating Game Set for:', {
                            matchType: matchType,
                            setNumber: setNumber,
                            totalGames: response.games.length,
                            filteredGames: response.games.filter(game => 
                                game.set == setNumber && game.name === matchType
                            ).length,
                            nextGameSet: nextGameSet
                        });
                    },
                    error: function() {
                        // Default to 1 if there's an error
                        $('#game-set').val(1);
                    }
                });
            }
            
            $('#create-match-btn').on('click', function() {
                const matchType = $('#match-type').val();
                const team1Id = $('#team1').val();
                const team2Id = $('#team2').val();
                const fieldId = $('#field').val();
                const gameSet = $('#game-set').val();
                const set = $('#set').val();
                
                if (!matchType || !team1Id || !team2Id || !fieldId || !gameSet || !set) {
                    alert('Please fill in all fields.');
                    return;
                }
                
                if (team1Id === team2Id) {
                    alert('Please select different teams.');
                    return;
                }
                
                // Disable button and show loading
                const $btn = $(this);
                const originalText = $btn.text();
                $btn.prop('disabled', true).text('Creating...');
                
                // Create match via AJAX
                $.ajax({
                    url: '{{ route("wasit.create-match") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        match_type: matchType,
                        team1_id: team1Id,
                        team2_id: team2Id,
                        field_id: fieldId,
                        game_set: gameSet,
                        set: set
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Match created successfully!');
                            // Redirect to referee page
                            window.location.href = `/wasit/referee/${response.game.id}`;
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error creating match.';
                        
                        if (xhr.responseJSON) {
                            // Check if it's a field busy error
                            if (xhr.responseJSON.field_busy && xhr.responseJSON.incomplete_game) {
                                const game = xhr.responseJSON.incomplete_game;
                                const message = `Cannot create match because there is an unfinished match on this field:\n\n` +
                                    `${game.team1_name} vs ${game.team2_name}\n` +
                                    `${game.formatted_name} • Game Set ${game.game_set} • Set ${game.set}\n` +
                                    `Score: ${game.team1_score} - ${game.team2_score}\n` +
                                    `Status: ${game.status}\n\n` +
                                    `Would you like to go to the referee page for this match?`;
                                
                                if (confirm(message)) {
                                    // Navigate to the referee page for the incomplete game
                                    window.location.href = game.referee_url;
                                    return;
                                }
                            } 
                            // Handle other validation errors
                            else if (xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join('\n');
                            }
                            // Handle general message errors
                            else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }
                        
                        alert(errorMessage);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });
            
            function loadExistingMatches(matchType, team1Id, team2Id) {
                const $matchesList = $('#matches-list');
                $matchesList.html('<div class="text-center py-4 text-gray-500">Loading...</div>');
                
                $.ajax({
                    url: '{{ route("wasit.matches") }}',
                    method: 'GET',
                    data: {
                        match_type: matchType,
                        team1_id: team1Id,
                        team2_id: team2Id
                    },
                    success: function(response) {
                        if (response.games.length === 0) {
                            $matchesList.html('<div class="text-center py-4 text-gray-500">No matches found with the selected criteria.</div>');
                            return;
                        }
                        
                        let html = '';
                        response.games.forEach(function(game) {
                            const winnerText = game.winner_name ? `Winner: ${game.winner_name}` : 'Ongoing';
                            const servingText = game.serving_team_name ? `Serving: ${game.serving_team_name}` : '';
                            
                            html += `
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">
                                                ${game.team1_name} vs ${game.team2_name}
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                ${game.formatted_name} • Game Set ${game.game_set} • Set ${game.set}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                Score: ${game.team1_score} - ${game.team2_score} • ${winnerText}
                                            </div>
                                            ${servingText ? `<div class="text-xs text-gray-500 mt-1">${servingText}</div>` : ''}
                                        </div>
                                        <div class="ml-4">
                                            <a href="/wasit/referee/${game.id}" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                Start Referee
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        $matchesList.html(html);
                    },
                    error: function() {
                        $matchesList.html('<div class="text-center py-4 text-red-500">Error loading matches. Please try again.</div>');
                    }
                });
            }
            
            // Team selection logic
            $('#team1').on('focus', function() {
                const matchType = $('#match-type').val();
                if (!matchType) {
                    alert('Please select Match Type first.');
                    $('#match-type').focus();
                    return false;
                }
            });
            
            $('#team1').on('change', function() {
                const selectedTeam1 = $(this).val();
                const team2Id = $('#team2').val();
                
                updateTeam2Options();
                
                // If both teams are selected and create form is visible, recalculate game set
                if (selectedTeam1 && team2Id && !$('#new-match-form').hasClass('hidden')) {
                    calculateNextGameSet(selectedTeam1, team2Id);
                }
            });
            
            $('#team2').on('focus', function() {
                const team1Val = $('#team1').val();
                if (!team1Val) {
                    alert('Please select Team 1 first.');
                    $('#team1').focus();
                    return false;
                }
            });
            
            $('#team2').on('change', function() {
                const team1Val = $('#team1').val();
                const team2Val = $(this).val();
                
                if (team1Val && team2Val && team1Val === team2Val) {
                    alert('Please select different teams.');
                    $(this).val('');
                    return;
                }
                
                // If both teams are selected and create form is visible, recalculate game set
                if (team1Val && team2Val && !$('#new-match-form').hasClass('hidden')) {
                    calculateNextGameSet(team1Val, team2Val);
                }
            });
            
            function updateTeam2Options() {
                const $team2Select = $('#team2');
                const selectedTeam1Id = $('#team1').val();
                const matchType = $('#match-type').val();
                const currentTeam2Value = $team2Select.val();
                
                // Reset team2 selection if needed
                let shouldResetTeam2 = false;
                
                if (currentTeam2Value === selectedTeam1Id) {
                    shouldResetTeam2 = true;
                }
                
                // Get the group of selected team1
                let selectedTeam1Group = null;
                if (selectedTeam1Id) {
                    const selectedTeam1Option = $('#team1 option:selected');
                    const team1Text = selectedTeam1Option.text();
                    const groupMatch = team1Text.match(/\(([^)]+)\)$/);
                    if (groupMatch) {
                        selectedTeam1Group = groupMatch[1];
                    }
                }
                
                // First pass: Show/hide team options based on match type and team1 selection
                $team2Select.find('option').each(function() {
                    const $option = $(this);
                    const optionValue = $option.val();
                    const optionText = $option.text();
                    
                    // Always show the empty option
                    if (optionValue === '') {
                        $option.show();
                        return;
                    }
                    
                    // Skip separator options for now, handle them separately
                    if ($option.prop('disabled')) {
                        return;
                    }
                    
                    // Hide the same team as team1
                    if (optionValue === selectedTeam1Id) {
                        $option.hide();
                        return;
                    }
                    
                    // If no team1 is selected, show all options
                    if (!selectedTeam1Id) {
                        $option.show();
                        return;
                    }
                    
                    // Get the group of this team option
                    const groupMatch = optionText.match(/\(([^)]+)\)$/);
                    const teamGroup = groupMatch ? groupMatch[1] : null;
                    
                    // Apply filtering based on match type
                    if (matchType === 'qualification') {
                        // For qualification matches, only show teams from the same group
                        if (teamGroup === selectedTeam1Group) {
                            $option.show();
                        } else {
                            $option.hide();
                            // Reset team2 if it's from a different group
                            if (optionValue === currentTeam2Value) {
                                shouldResetTeam2 = true;
                            }
                        }
                    } else {
                        // For semifinal and final matches, show all teams except team1
                        $option.show();
                    }
                });
                
                // Second pass: Handle separator visibility based on visible teams
                let currentGroup = null;
                let hasVisibleTeamInCurrentGroup = false;
                let lastSeparator = null;
                
                $team2Select.find('option').each(function() {
                    const $option = $(this);
                    const optionValue = $option.val();
                    const optionText = $option.text();
                    
                    // Skip empty option
                    if (optionValue === '') {
                        return;
                    }
                    
                    // Handle separator options
                    if ($option.prop('disabled') && optionText.includes('──')) {
                        // Hide the previous separator if no teams were visible in that group
                        if (lastSeparator && !hasVisibleTeamInCurrentGroup) {
                            lastSeparator.hide();
                        }
                        
                        // Store this separator for potential hiding
                        lastSeparator = $option;
                        hasVisibleTeamInCurrentGroup = false;
                        return;
                    }
                    
                    // If this team is visible, mark that we have visible teams in current group
                    if ($option.is(':visible')) {
                        hasVisibleTeamInCurrentGroup = true;
                        // Show the separator if we have one waiting
                        if (lastSeparator) {
                            lastSeparator.show();
                        }
                    }
                });
                
                // Hide the last separator if no teams were visible after it
                if (lastSeparator && !hasVisibleTeamInCurrentGroup) {
                    lastSeparator.hide();
                }
                
                // Reset team2 selection if needed
                if (shouldResetTeam2) {
                    $team2Select.val('');
                }
            }
            
            // Initialize team2 options on page load
            updateTeam2Options();
            
            // Search and filter functionality for existing games table
            function filterGames() {
                const teamSearch = $('#team-search').val().toLowerCase();
                const typeFilter = $('#type-filter').val();
                const statusFilter = $('#status-filter').val();

                $('tbody tr').each(function() {
                    const $row = $(this);
                    const teamText = $row.find('td:nth-child(2)').text().toLowerCase();
                    const gameType = $row.find('td:nth-child(5) span').text().toLowerCase();
                    const hasWinner = $row.find('td:nth-child(4) .text-xs:contains("Winner:")').length > 0;
                    
                    let showRow = true;
                    
                    // Team name filter
                    if (teamSearch && !teamText.includes(teamSearch)) {
                        showRow = false;
                    }
                    
                    // Type filter
                    if (typeFilter && !gameType.includes(typeFilter.replace('-', '-'))) {
                        showRow = false;
                    }
                    
                    // Status filter
                    if (statusFilter === 'completed' && !hasWinner) {
                        showRow = false;
                    } else if (statusFilter === 'ongoing' && hasWinner) {
                        showRow = false;
                    }
                    
                    $row.toggle(showRow);
                });
                
                // Update count
                const visibleRows = $('tbody tr:visible').length;
                console.log(`Showing ${visibleRows} games`);
            }

            // Bind filter events
            $('#team-search, #type-filter, #status-filter').on('input change', filterGames);
            
            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#team-search').val('');
                $('#type-filter').val('');
                $('#status-filter').val('');
                $('tbody tr').show();
            });

            // Add hover effects to action buttons
            $('a[href*="wasit/referee"]').on('mouseenter', function() {
                $(this).addClass('shadow-lg transform scale-105');
            }).on('mouseleave', function() {
                $(this).removeClass('shadow-lg transform scale-105');
            });

            console.log('Wasit game selection page initialized');
            console.log(`Total games available: {{ $games->count() }}`);
        });
    </script>
</body>
</html>
