<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasit - Referee: {{ $game->team1->name }} vs {{ $game->team2->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .tab-button {
            @apply px-6 py-2 font-medium text-sm border-b-2 border-transparent transition-all duration-200;
            color: #6b7280 !important; /* gray-500 */
        }
        .tab-button:hover {
            @apply text-blue-500 bg-gray-50;
        }
        .tab-button.active {
            @apply border-blue-500 bg-blue-50;
            color: #2563eb !important; /* blue-600 */
            font-weight: 600 !important;
        }
        .serve-button {
            @apply px-6 py-3 font-bold text-lg rounded-lg shadow-lg transition-all duration-300 cursor-pointer;
            min-width: 140px;
            min-height: 60px;
            background-color: #9ca3af !important; /* gray-400 */
            color: #374151 !important; /* gray-700 */
        }
        .serve-button:hover {
            @apply transform scale-105 shadow-xl;
            background-color: #6b7280 !important; /* gray-500 */
            color: white !important;
        }
        .serve-button.active {
            background-color: #2563eb !important; /* blue-600 */
            color: white !important;
            @apply ring-4 ring-blue-300;
        }
        .serve-button.active:hover {
            background-color: #1d4ed8 !important; /* blue-700 */
            color: white !important;
        }
        .serve-button.inactive {
            background-color: #9ca3af !important; /* gray-400 */
            color: #374151 !important; /* gray-700 */
        }
        .serve-button.inactive:hover {
            background-color: #6b7280 !important; /* gray-500 */
            color: white !important;
        }
        .overlay-button {
            @apply w-full py-6 text-white font-bold text-lg rounded-lg shadow-lg transition-all duration-200;
        }
        .overlay-button:hover {
            @apply transform scale-105 shadow-xl;
        }
        .score-button {
            @apply font-bold text-lg rounded-lg shadow-md transition-all duration-200 cursor-pointer;
            min-width: 80px;
            min-height: 50px;
            background-color: #3b82f6 !important; /* blue-500 */
            color: white !important;
        }
        .score-button:hover {
            @apply transform scale-105 shadow-lg;
            background-color: #2563eb !important; /* blue-600 */
        }
        .score-button.active {
            background-color: #16a34a !important; /* green-600 */
            @apply ring-4 ring-green-300;
        }
        .score-button.active:hover {
            background-color: #15803d !important; /* green-700 */
        }
        .score-button.inactive {
            background-color: #9ca3af !important; /* gray-400 */
            color: #374151 !important; /* gray-700 */
        }
        .score-button.inactive:hover {
            background-color: #6b7280 !important; /* gray-500 */
            color: white !important;
        }
        .score-button.selected {
            background-color: #16a34a !important; /* green-600 */
        }
        .score-button.selected:hover {
            background-color: #15803d !important; /* green-700 */
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <h1 class="text-sm font-bold text-gray-800">WASIT - Referee Control Panel</h1>
                    @if($isTieBreak)
                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-orange-300 animate-pulse">
                            🏆 TIE-BREAK ACTIVE
                        </span>
                    @endif
                </div>
                <a href="{{ route('wasit') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-2 rounded" title="Back to Game Selection">
                    ←
                </a>
            </div>
            
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button class="tab-button active" data-tab="general">General</button>
                    <button class="tab-button" data-tab="set">Set</button>
                    <button class="tab-button" data-tab="games">Games</button>
                    <button class="tab-button" data-tab="tie-break">Tie-Break</button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="general-tab" class="tab-content">
                <!-- SERVE Section -->
                <div class="text-center mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-6 tracking-wider">SERVE</h2>
                    <div class="grid grid-cols-2 gap-2">
                                <button id="team1-serve" class="serve-button inactive" data-player="{{ $game->team1->name }}" data-team="team1">
                                    {{ $game->team1->name }}
                                </button>
                                <button id="team2-serve" class="serve-button inactive" data-player="{{ $game->team2->name }}" data-team="team2">
                                    {{ $game->team2->name }}
                                </button>
                    </div>
                </div>

                <!-- SCORE SETTING Section -->
                <div class="text-center mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-6 tracking-wider">SCORE SETTING</h2>
                      <div class="grid grid-cols-1 gap-2">
                        <!-- Team Scores -->
                        <div class="text-center">
                            <div class="flex justify-between mb-4">
                                <div class="text-left">
                                    <h3 class="text-md font-semibold text-gray-600">{{ $game->team1->name }}</h3>
                                    <p class="text-xs text-blue-600 font-medium">Total Win: {{ $team1TotalWins }}</p>
                                </div>
                                <div class="text-right">
                                    <h3 class="text-md font-semibold text-gray-600">{{ $game->team2->name }}</h3>
                                    <p class="text-xs text-blue-600 font-medium">Total Win: {{ $team2TotalWins }}</p>
                                </div>
                            </div>
                            
                            @if($isTieBreak)
                                <!-- TIE-BREAK SCORING MODE -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="text-sm text-blue-700 font-medium mb-3">
                                        🏆 Tie-Break Mode - Current Scores:
                                    </div>
                                    
                                    <!-- Current Scores Display -->
                                    <div class="grid grid-cols-2 gap-6 mb-4">
                                        <div class="text-center">
                                            <div class="text-3xl font-bold text-blue-600" id="team1-current-score">
                                                {{ $game->team1_score }}
                                            </div>
                                            <div class="text-xs text-gray-600">{{ $game->team1->name }}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-3xl font-bold text-blue-600" id="team2-current-score">
                                                {{ $game->team2_score }}
                                            </div>
                                            <div class="text-xs text-gray-600">{{ $game->team2->name }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Tie-Break Controls -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex justify-center space-x-2">
                                            <button class="tiebreak-btn bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded-lg text-sm" 
                                                    data-team="team1" data-action="decrement">
                                                -1
                                            </button>
                                            <button class="tiebreak-btn bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 rounded-lg text-sm" 
                                                    data-team="team1" data-action="increment">
                                                +1
                                            </button>
                                        </div>
                                        <div class="flex justify-center space-x-2">
                                            <button class="tiebreak-btn bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded-lg text-sm" 
                                                    data-team="team2" data-action="decrement">
                                                -1
                                            </button>
                                            <button class="tiebreak-btn bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 rounded-lg text-sm" 
                                                    data-team="team2" data-action="increment">
                                                +1
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- NORMAL TENNIS SCORING MODE -->
                                <div class="grid grid-cols-2 gap-2">
                                    <button class="score-button team1-score {{ $game->team1_score == '0' ? 'active' : 'inactive' }}" data-team="team1" data-score="0">
                                        0
                                    </button>
                                    <button class="score-button team2-score {{ $game->team2_score == '0' ? 'active' : 'inactive' }}" data-team="team2" data-score="0">
                                        0
                                    </button>
                                    <button class="score-button team1-score {{ $game->team1_score == '15' ? 'active' : 'inactive' }}" data-team="team1" data-score="15">
                                        15
                                    </button>
                                    <button class="score-button team2-score {{ $game->team2_score == '15' ? 'active' : 'inactive' }}" data-team="team2" data-score="15">
                                        15
                                    </button>
                                    <button class="score-button team1-score {{ $game->team1_score == '30' ? 'active' : 'inactive' }}" data-team="team1" data-score="30">
                                        30
                                    </button>
                                    <button class="score-button team2-score {{ $game->team2_score == '30' ? 'active' : 'inactive' }}" data-team="team2" data-score="30">
                                        30
                                    </button>
                                    <button class="score-button team1-score {{ $game->team1_score == '40' ? 'active' : 'inactive' }}" data-team="team1" data-score="40">
                                        40
                                    </button>
                                    <button class="score-button team2-score {{ $game->team2_score == '40' ? 'active' : 'inactive' }}" data-team="team2" data-score="40">
                                        40
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- WINNER Section -->
                <div class="text-center mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-6 tracking-wider">WINNER</h2>
                    <div class="grid grid-cols-2 gap-2">
                        <button id="team1-winner" class="serve-button inactive" data-player="{{ $game->team1->name }}" data-team="{{ $game->team1->id }}">
                            {{ $game->team1->name }}
                        </button>
                        <button id="team2-winner" class="serve-button inactive" data-player="{{ $game->team2->name }}" data-team="{{ $game->team2->id }}">
                            {{ $game->team2->name }}
                        </button>
                    </div>
                </div>

                <!-- ACTION Section -->
                <div class="text-center mb-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-6 tracking-wider">ACTION</h2>
                    
                    <!-- Hidden form fields -->
                    <form id="game-form" style="display: none;">
                        <input type="hidden" id="winner_id" name="winner_id" value="{{ $game->winner_id ?? '' }}">
                        <input type="hidden" id="manual_winner" name="manual_winner" value="">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                    
                    <div class="w-full">
                        <button id="end-game-btn" class="w-full py-6 bg-red-600 hover:bg-red-700 text-white font-bold text-xl rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 hover:shadow-xl">
                            <svg class="inline w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9-3-9m-9 9a9 9 0 919-9"></path>
                            </svg>
                            END GAME
                        </button>
                    </div>
                    
                    <!-- Winner Selection Info (visible when needed) -->
                    <div id="winner-info" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg" style="display: none;">
                        <p class="text-sm text-yellow-800">
                            <strong>Selected Winner:</strong> <span id="selected-winner-name">None</span>
                        </p>
                    </div>
                </div>
            </div>

            <div id="set-tab" class="tab-content hidden">
                <div class="text-center py-12">
                    <h2 class="text-2xl font-bold text-gray-700 mb-4">Set Controls</h2>
                    <p class="text-gray-500">Set management controls will be available here.</p>
                </div>
            </div>

            <div id="games-tab" class="tab-content hidden">
                <div class="text-center py-12">
                    <h2 class="text-2xl font-bold text-gray-700 mb-4">Games Controls</h2>
                    <p class="text-gray-500">Game management controls will be available here.</p>
                </div>
            </div>

            <div id="tie-break-tab" class="tab-content hidden">
                @if($isTieBreak)
                    <!-- TIE-BREAK ACTIVE -->
                    <div class="text-center mb-8">
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                            <h2 class="text-xl font-bold mb-2">🏆 TIE-BREAK ACTIVE</h2>
                            <p class="text-sm">Match score is 3-3. This 7th game uses regular scoring (0, 1, 2, 3, etc.)</p>
                        </div>
                        
                        <!-- Current Tie-Break Scores -->
                        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Tie-Break Score</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div class="text-center">
                                    <h4 class="font-bold text-gray-600 mb-2">{{ $game->team1->name }}</h4>
                                    <div class="text-4xl font-bold text-blue-600" id="team1-tiebreak-score">
                                        {{ $game->team1_score }}
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h4 class="font-bold text-gray-600 mb-2">{{ $game->team2->name }}</h4>
                                    <div class="text-4xl font-bold text-blue-600" id="team2-tiebreak-score">
                                        {{ $game->team2_score }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tie-Break Score Controls -->
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Team 1 Controls -->
                            <div class="bg-white rounded-lg shadow-lg p-4">
                                <h4 class="font-semibold text-gray-700 mb-4 text-center">{{ $game->team1->name }}</h4>
                                <div class="flex justify-center space-x-2">
                                    <button class="tiebreak-btn bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg" 
                                            data-team="team1" data-action="decrement">
                                        -1
                                    </button>
                                    <button class="tiebreak-btn bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg" 
                                            data-team="team1" data-action="increment">
                                        +1
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Team 2 Controls -->
                            <div class="bg-white rounded-lg shadow-lg p-4">
                                <h4 class="font-semibold text-gray-700 mb-4 text-center">{{ $game->team2->name }}</h4>
                                <div class="flex justify-center space-x-2">
                                    <button class="tiebreak-btn bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg" 
                                            data-team="team2" data-action="decrement">
                                        -1
                                    </button>
                                    <button class="tiebreak-btn bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg" 
                                            data-team="team2" data-action="increment">
                                        +1
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 text-sm text-gray-600">
                            <p>💡 <strong>Tip:</strong> Tie-break scoring uses regular numbers. First to 7 points (with 2-point margin) wins.</p>
                        </div>
                    </div>
                @else
                    <!-- NO TIE-BREAK -->
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-lg p-6">
                            <h2 class="text-2xl font-bold text-gray-700 mb-4">No Tie-Break</h2>
                            <p class="text-gray-500 mb-2">Match score is not 3-3 yet.</p>
                            <p class="text-sm text-gray-400">Tie-break controls will be available when the match reaches 3-3.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentServer = null;
            let activeOverlay = null;
            let currentScores = {
                team1: '{{ $game->team1_score }}',
                team2: '{{ $game->team2_score }}'
            };

            // Tab switching functionality
            $('.tab-button').on('click', function() {
                const tabName = $(this).data('tab');
                
                // Update tab buttons
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                
                // Update tab content
                $('.tab-content').addClass('hidden');
                $(`#${tabName}-tab`).removeClass('hidden');
                
                // Update status
                $('#current-tab').text($(this).text());
                
                console.log(`Switched to tab: ${tabName}`);
            });

            // Serve button functionality
            $('#team1-serve, #team2-serve').on('click', function() {
                const $button = $(this);
                const player = $button.data('player');
                const team = $button.data('team');
                
                // Disable button during AJAX call
                $button.prop('disabled', true);
                
                // Update who_is_serving via AJAX
                $.ajax({
                    url: '{{ route("wasit.update-serving", $game->id) }}',
                    method: 'POST',
                    data: {
                        who_is_serving: team,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove active state from all serve buttons and add inactive
                            $('#team1-serve, #team2-serve').removeClass('active').addClass('inactive');
                            
                            // Add active state to clicked button and remove inactive
                            $button.removeClass('inactive').addClass('active');
                            
                            // Update current server displays
                            currentServer = player;
                            $('#current-server').text(player);
                            $('#serving-display').text(player);
                            
                            console.log(`${player} is now serving`);
                            
                            // Show success feedback
                            $button.addClass('ring-4 ring-green-300');
                            setTimeout(() => {
                                $button.removeClass('ring-4 ring-green-300');
                            }, 1500);
                        } else {
                            alert('Failed to update serving team. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Error updating serving team. Please check your connection and try again.');
                    },
                    complete: function() {
                        // Re-enable button
                        $button.prop('disabled', false);
                    }
                });
            });

            // Winner button functionality
            $('#team1-winner, #team2-winner').on('click', function() {
                const $button = $(this);
                const player = $button.data('player');
                const teamId = $button.data('team');
                
                // Remove active state from all winner buttons and add inactive
                $('#team1-winner, #team2-winner').removeClass('active').addClass('inactive');
                
                // Add active state to clicked button and remove inactive
                $button.removeClass('inactive').addClass('active');
                
                // Update hidden form field
                $('#manual_winner').val(teamId);
                
                // Update winner info display
                $('#selected-winner-name').text(player);
                $('#winner-info').show();
                
                console.log(`${player} selected as winner (ID: ${teamId})`);
                
                // Show success feedback
                $button.addClass('ring-4 ring-green-300');
                setTimeout(() => {
                    $button.removeClass('ring-4 ring-green-300');
                }, 1500);
            });

            // Overlay button functionality
            $('.overlay-button').on('click', function() {
                const overlayType = $(this).attr('id').replace('-overlay', '').toUpperCase();
                
                // Toggle overlay state
                if (activeOverlay === overlayType) {
                    // Deactivate overlay
                    activeOverlay = null;
                    $('#active-overlay').text('None');
                    $(this).removeClass('ring-4 ring-yellow-300');
                    console.log(`${overlayType} overlay deactivated`);
                } else {
                    // Remove active state from other overlays
                    $('.overlay-button').removeClass('ring-4 ring-yellow-300');
                    
                    // Activate this overlay
                    activeOverlay = overlayType;
                    $('#active-overlay').text(overlayType);
                    $(this).addClass('ring-4 ring-yellow-300');
                    console.log(`${overlayType} overlay activated`);
                }
                
                // Add click feedback
                $(this).addClass('transform scale-95');
                setTimeout(() => {
                    $(this).removeClass('transform scale-95');
                }, 150);
            });

            // Score button functionality
            $('.score-button').on('click', function() {
                const $button = $(this);
                const team = $button.data('team');
                const score = $button.data('score').toString(); // Ensure string format
                
                // Disable button during AJAX call
                $button.prop('disabled', true);
                
                // Update score via AJAX
                const scoreData = {};
                scoreData[team + '_score'] = score;
                
                $.ajax({
                    url: '{{ route("wasit.update-score", $game->id) }}',
                    method: 'POST',
                    data: {
                        ...scoreData,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove active state from all buttons for this team and make them inactive
                            $(`.${team}-score`).removeClass('active selected').addClass('inactive');
                            
                            // Add active state to clicked button and remove inactive
                            $button.removeClass('inactive selected').addClass('active');
                            
                            // Update currentScores tracking - ensure string format
                            currentScores[team] = score.toString();
                            
                            console.log(`${team} score updated to ${score}`);
                            console.log('Current scores:', currentScores);
                            
                            // Show success feedback
                            $button.addClass('ring-4 ring-green-300');
                            setTimeout(() => {
                                $button.removeClass('ring-4 ring-green-300');
                            }, 1000);
                        } else {
                            alert('Failed to update score. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Error updating score. Please check your connection and try again.');
                    },
                    complete: function() {
                        // Re-enable button
                        $button.prop('disabled', false);
                    }
                });
            });

            // End Game button functionality
            $('#end-game-btn').on('click', function() {
                const $button = $(this);
                
                // Get current scores from dynamic tracking
                const team1Score = currentScores.team1.toString();
                const team2Score = currentScores.team2.toString();
                
                console.log('End Game button clicked');
                console.log('Current scores object:', currentScores);
                console.log('Team1 score:', team1Score, '(type:', typeof team1Score, ')');
                console.log('Team2 score:', team2Score, '(type:', typeof team2Score, ')');
                
                // Check if a winner has been selected
                const selectedWinner = $('#manual_winner').val();
                if (!selectedWinner) {
                    alert('Please select a winner using the WINNER section before ending the game.');
                    return;
                }
                
                // Get winner name for display
                let winnerName = '';
                if (selectedWinner === '{{ $game->team1->id }}') {
                    winnerName = '{{ $game->team1->name }}';
                } else if (selectedWinner === '{{ $game->team2->id }}') {
                    winnerName = '{{ $game->team2->name }}';
                }
                
                // Show confirmation dialog with manually selected winner
                const confirmEnd = confirm('Are you sure you want to end this game?\n\nSelected Winner: ' + winnerName + '\nFinal Scores: {{ $game->team1->name }} ' + team1Score + ' - ' + team2Score + ' {{ $game->team2->name }}\n\nThis action cannot be undone.');
                
                console.log('Validation passed - proceeding to confirmation');
                
                if (confirmEnd) {
                    // Disable button during AJAX call
                    $button.prop('disabled', true);
                    $button.html('<svg class="inline w-6 h-6 mr-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>ENDING GAME...');
                    
                    // Add visual feedback
                    $button.addClass('opacity-75 cursor-not-allowed');
                    
                    // Make AJAX call to end game with manual winner
                    $.ajax({
                        url: '{{ route("wasit.end-game", $game->id) }}',
                        method: 'POST',
                        data: {
                            manual_winner: selectedWinner,
                            _token: '{{ csrf_token() }}'
                        },
                    success: function(response) {
                        console.log('AJAX Success - Full response:', response);
                        console.log('Response type:', typeof response);
                        console.log('Response.success:', response.success);
                        console.log('Response.requires_manual_winner:', response.requires_manual_winner);
                        
                        if (response.success) {
                            alert('Game ended successfully!\n\nWinner: ' + response.winner_name + '\nFinal Scores: {{ $game->team1->name }} ' + response.final_scores.team1 + ' - ' + response.final_scores.team2 + ' {{ $game->team2->name }}\n\nRedirecting to game selection...');
                            
                            // Redirect to wasit selection page
                            setTimeout(() => {
                                window.location.href = '{{ route("wasit") }}';
                            }, 1500);
                            
                            console.log('Game ended successfully:', response);
                        } else if (response.requires_manual_winner) {
                            console.log('40-40 detected! Showing manual winner dialog...');
                            console.log('Teams data:', response.teams);
                            console.log('Scores data:', response.current_scores);
                            
                            // Special case: 40-40 requires manual winner selection
                            showManualWinnerDialog(response.teams, response.current_scores);
                            
                            // Re-enable button for re-submission after manual winner selection
                            $button.prop('disabled', false);
                            $button.html('<svg class="inline w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 919-9"></path></svg>END GAME');
                            $button.removeClass('opacity-75 cursor-not-allowed');
                        } else {
                            console.log('Error case - showing alert with message:', response.message);
                            alert('Error: ' + response.message);
                            // Re-enable button on error
                            $button.prop('disabled', false);
                            $button.html('<svg class="inline w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 919-9"></path></svg>END GAME');
                            $button.removeClass('opacity-75 cursor-not-allowed');
                        }
                        },
                        error: function(xhr, status, error) {
                            let errorMessage = 'Error ending game. Please try again.';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            alert('Error: ' + errorMessage);
                            console.error('AJAX Error:', error);
                            console.error('Response:', xhr.responseJSON);
                            
                            // Re-enable button on error
                            $button.prop('disabled', false);
                            $button.html('<svg class="inline w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 919-9"></path></svg>END GAME');
                            $button.removeClass('opacity-75 cursor-not-allowed');
                        }
                    });
                    
                    console.log('Attempting to end game: {{ $game->team1->name }} vs {{ $game->team2->name }}');
                }
            });

            // Tie-break button functionality
            $('.tiebreak-btn').on('click', function() {
                const $button = $(this);
                const team = $button.data('team');
                const action = $button.data('action');
                
                // Disable button during AJAX call
                $button.prop('disabled', true);
                
                // Update tie-break score via AJAX
                $.ajax({
                    url: '{{ route("wasit.update-tiebreak-score", $game->id) }}',
                    method: 'POST',
                    data: {
                        team: team,
                        action: action,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the tie-break score displays in Tie-Break tab
                            $('#team1-tiebreak-score').text(response.team1_score);
                            $('#team2-tiebreak-score').text(response.team2_score);
                            
                            // Update the current score displays in General tab
                            $('#team1-current-score').text(response.team1_score);
                            $('#team2-current-score').text(response.team2_score);
                            
                            // Update currentScores tracking
                            currentScores.team1 = response.team1_score.toString();
                            currentScores.team2 = response.team2_score.toString();
                            
                            console.log(`Tie-break score updated: ${team} ${action} to ${response.new_score}`);
                            console.log('Current tie-break scores:', currentScores);
                            
                            // Show success feedback
                            $button.addClass('ring-4 ring-green-300');
                            setTimeout(() => {
                                $button.removeClass('ring-4 ring-green-300');
                            }, 1000);
                        } else {
                            alert('Failed to update tie-break score. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        alert('Error updating tie-break score. Please check your connection and try again.');
                    },
                    complete: function() {
                        // Re-enable button
                        $button.prop('disabled', false);
                    }
                });
            });
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                switch(e.key) {
                    case '1':
                        $('#team1-serve').click();
                        break;
                    case '2':
                        $('#team2-serve').click();
                        break;
                    case 't':
                    case 'T':
                        $('#tie-break-overlay').click();
                        break;
                    case 'b':
                    case 'B':
                        $('#breakpoint-overlay').click();
                        break;
                    case 'e':
                    case 'E':
                        $('#end-game-btn').click();
                        break;
                }
            });

            // Initialize current server from game data
            @if($game->who_is_serving)
                const initialServer = '{{ $game->who_is_serving }}';
                if (initialServer === 'team1') {
                    $('#team1-serve').removeClass('inactive').addClass('active');
                    $('#team2-serve').addClass('inactive');
                    $('#current-server').text('{{ $game->team1->name }}');
                    $('#serving-display').text('{{ $game->team1->name }}');
                    currentServer = '{{ $game->team1->name }}';
                } else if (initialServer === 'team2') {
                    $('#team2-serve').removeClass('inactive').addClass('active');
                    $('#team1-serve').addClass('inactive');
                    $('#current-server').text('{{ $game->team2->name }}');
                    $('#serving-display').text('{{ $game->team2->name }}');
                    currentServer = '{{ $game->team2->name }}';
                }
            @else
                $('#serving-display').text('Not Set');
            @endif

            // Add some initial animations
            setTimeout(() => {
                $('.serve-button').addClass('animate-pulse');
                setTimeout(() => {
                    $('.serve-button').removeClass('animate-pulse');
                }, 2000);
            }, 1000);

            console.log('Wasit Referee Control Panel initialized for game: {{ $game->team1->name }} vs {{ $game->team2->name }}');
            console.log('Match: M{{ $game->game_set }}, Set: {{ $game->set }}, Type: {{ $game->formatted_name }}');
            console.log('Keyboard shortcuts: 1 ({{ $game->team1->name }}), 2 ({{ $game->team2->name }}), T (Tie-break), B (Breakpoint), E (End Game)');
            console.log('Initial scores:', currentScores);
            console.log('Initial team1_score:', '{{ $game->team1_score }}', 'Initial team2_score:', '{{ $game->team2_score }}');
            
            // Manual winner selection function for 40-40 scores
            window.showManualWinnerDialog = function(teams, scores) {
                console.log('Showing manual winner dialog for 40-40 situation', teams, scores);
                
                // Create a modal overlay
                const modalHtml = `
                    <div id="manual-winner-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
                            <div class="text-center mb-6">
                                <h2 class="text-xl font-bold text-red-600 mb-2">⚠️ 40-40 Situation</h2>
                                <p class="text-gray-700 mb-2">Both teams have 40 points.</p>
                                <p class="text-sm text-gray-600">Please select which team wins this game:</p>
                            </div>
                            
                            <div class="space-y-4 mb-6">
                                <button id="select-team1" class="w-full p-4 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105">
                                    <div class="text-lg">${teams.team1.name}</div>
                                    <div class="text-sm opacity-80">${teams.team1.group}</div>
                                </button>
                                
                                <button id="select-team2" class="w-full p-4 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105">
                                    <div class="text-lg">${teams.team2.name}</div>
                                    <div class="text-sm opacity-80">${teams.team2.group}</div>
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <button id="cancel-manual-winner" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add modal to the page
                $('body').append(modalHtml);
                
                // Handle team1 selection
                $('#select-team1').on('click', function() {
                    submitEndGameWithManualWinner(teams.team1.id, teams.team1.name);
                });
                
                // Handle team2 selection
                $('#select-team2').on('click', function() {
                    submitEndGameWithManualWinner(teams.team2.id, teams.team2.name);
                });
                
                // Handle cancel
                $('#cancel-manual-winner').on('click', function() {
                    $('#manual-winner-modal').remove();
                });
                
                // Close modal when clicking outside
                $('#manual-winner-modal').on('click', function(e) {
                    if (e.target === this) {
                        $(this).remove();
                    }
                });
            };
            
            // Function to submit end game with manual winner selection
            window.submitEndGameWithManualWinner = function(winner, winnerName) {
                console.log('Submitting end game with manual winner:', winner, winnerName);
                
                // Update radio button selection - winner is now a team ID
                const team1Id = '{{ $game->team1->id }}';
                const team2Id = '{{ $game->team2->id }}';
                
                if (winner.toString() === team1Id) {
                    $('#team1_winner').prop('checked', true);
                } else if (winner.toString() === team2Id) {
                    $('#team2_winner').prop('checked', true);
                }
                
                // Update winner info display
                $('#selected-winner-name').text(winnerName);
                $('#winner-info').show();
                
                // Remove the modal
                $('#manual-winner-modal').remove();
                
                // Get the end game button and disable it
                const $button = $('#end-game-btn');
                $button.prop('disabled', true);
                $button.html('<svg class="inline w-6 h-6 mr-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>ENDING GAME...');
                $button.addClass('opacity-75 cursor-not-allowed');
                
                // Make AJAX call with manual winner
                $.ajax({
                    url: '{{ route("wasit.end-game", $game->id) }}',
                    method: 'POST',
                    data: {
                        manual_winner: winner,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Game ended successfully!\\n\\nWinner: ' + response.winner_name + ' (Manual Selection)' + '\\nFinal Scores: {{ $game->team1->name }} ' + response.final_scores.team1 + ' - ' + response.final_scores.team2 + ' {{ $game->team2->name }}\\n\\nRedirecting to game selection...');
                            
                            // Redirect to wasit selection page
                            setTimeout(() => {
                                window.location.href = '{{ route("wasit") }}';
                            }, 1500);
                            
                            console.log('Game ended successfully with manual winner:', response);
                        } else {
                            alert('Error: ' + response.message);
                            // Re-enable button on error
                            $button.prop('disabled', false);
                            $button.html('<svg class="inline w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 919-9"></path></svg>END GAME');
                            $button.removeClass('opacity-75 cursor-not-allowed');
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Error ending game with manual winner. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        alert('Error: ' + errorMessage);
                        console.error('AJAX Error:', error);
                        console.error('Response:', xhr.responseJSON);
                        
                        // Re-enable button on error
                        $button.prop('disabled', false);
                        $button.html('<svg class="inline w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 919-9"></path></svg>END GAME');
                        $button.removeClass('opacity-75 cursor-not-allowed');
                    }
                });
            };
        });
    </script>
</body>
</html>
