<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Round Robin Preliminary Round</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <style>
      body{
        width: 1920px;
        height: 1080px;
        overflow: hidden;
      }

      #header {
        color: #014E82;
      }

      .name-horizontal {
        position: absolute;
        
        width: 169.457px; 
        height: 63.7541px;
        display: flex; flex-direction: column; justify-content: center; align-items: center; 
        z-index: 2;
        text-align: center;
        color: #014E82;
        font-size: 24px;
        font-weight: bold;
        line-height: 20px;
      }

      .name-vertical {
        position: absolute;
  
        width:  62.9976px; 
        height: 166.423px;
        display: flex; flex-direction: column; justify-content: center; align-items: center; 
        z-index: 2;
        text-align: center;
        color: #014E82;
        font-size: 24px;
        font-weight: bold;
        line-height: 20px;
        text-orientation: mixed;
        writing-mode: sideways-lr;
        text-orientation: mixed;
      }

      .r1{
        top: 307px;
      }

      .r2{
        top: 490px;
      }

      .r3{
        top: 572px;
      }

      .r4{
        top: 648px;
      }

      .r5{
        top: 726px;
      }

      .r6{
        top: 808px;
      }


      .c1{
        left: 542px;
      }

      .c2{
        left: 728px;
      }

      .c3{
        left: 809px;
      }

      .c4{
        left: 888px;
      }

      .c5{
        left: 966px;
      }

      .c6{
        left: 1043px;
      }

      .c7{
        left: 1124px;
      }

    .c8{
        left: 1204px;
      } 

      .c9{
        left: 1284px;
      }

      .score{
        position: absolute;
        width: 63px; height: 63px;  display: block; background-image: unset; 
     
        display: flex; flex-direction: column; justify-content: center; align-items: center; 
        z-index: 2;
        text-align: center; color: #014E82; 
        font-size: 36px; 
        font-weight: bold; 
      }
      
  </style>
  <body class=" justify-center ">
    <img src="images/bg1920.png" class="absolute" style="z-index: -1;" />
    <div id="header" class="w-full h-48 mt-30  flex justify-center items-center">
     
    </div>


    <!-- Dynamic Score Boards Grid Layout - 2 per row -->
    @php
        $fieldCount = $fields->count();
        $columns = 2; // Always 2 columns per row
        $rows = ceil($fieldCount / $columns); // Calculate rows needed
    @endphp
    
    <div class="w-full flex flex-col mt-10 space-y-8" style="align-items: flex-start; padding-left: 15%;">
        @for ($row = 0; $row < $rows; $row++)
            <div class="flex space-x-12" style="width: 100%; justify-content: flex-start;">
                @for ($col = 0; $col < $columns; $col++)
                    @php
                        $index = $row * $columns + $col;
                    @endphp
                    @if ($index < $fieldCount)
                        @php
                            $field = $fields[$index];
                            $boardId = 'field' . ($index + 1);
                            $latestGame = $field->latestGame;
                            
                            // Calculate total wins for each team against each other in the same game type
                            $team1TotalWins = 0;
                            $team2TotalWins = 0;
                            
                            if ($latestGame && $latestGame->team1 && $latestGame->team2) {
                                // Get all finished games where these two teams played against each other with the same game name
                                $historicalGames = \App\Models\Game::where('name', $latestGame->name)
                                    ->where('status', 'Completed')
                                    ->where('name', $latestGame->name)
                                    ->where(function($query) use ($latestGame) {
                                        $query->where(function($subQuery) use ($latestGame) {
                                            $subQuery->where('team1_id', $latestGame->team1_id)
                                                     ->where('team2_id', $latestGame->team2_id);
                                        })->orWhere(function($subQuery) use ($latestGame) {
                                            $subQuery->where('team1_id', $latestGame->team2_id)
                                                     ->where('team2_id', $latestGame->team1_id);
                                        });
                                    })
                                    ->get();

                                
                                foreach ($historicalGames as $game) {
                                    if ($game->winner_id == $latestGame->team1_id) {
                                        $team1TotalWins++;
                                    } elseif ($game->winner_id == $latestGame->team2_id) {
                                        $team2TotalWins++;
                                    }
                                }
                            }
                        @endphp
                        <div class="score-board relative">
                            <img id="{{ $boardId }}" src="images/score-2.png" style="z-index:1;" />
                            
                            <!-- Team 1 Name -->
                            <div class="team1-name absolute " style="text-align:center; top: 30px; height:35px; left: 43px; width:330px; z-index: 2;">
                                <span class="px-2 py-1 uppercase text-2xl font-bold  text-white">
                                    @if($latestGame)
                                        {{ $latestGame->team1->name }}
                                    @else
                                        No Team
                                    @endif
                                </span>
                            </div>
                            <div class="team1-bola absolute {{ ($latestGame && $latestGame->who_is_serving == 'team1') ? '': 'hidden' }}" style="color: #014E82;text-align:center; top: 0px; height:35px; left: 390px; width:40px; z-index: 2;">
                                <span class=" px-2 py-1 uppercase text-2xl font-bold  ">
                                   <img src="images/bola.png" />
                                </span>
                            </div>
                             <div class="team1-totalwin absolute " style="color: #014E82;text-align:center; top: 30px; height:35px; left: 436px; width:40px; z-index: 2;">
                                <span class=" px-2 py-1 uppercase text-2xl font-bold  ">
                                    @if($latestGame && $latestGame->team1)
                                        {{ $team1TotalWins }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </div>
                            <div class="team1-score absolute " style="color: #014E82;text-align:center; top: 30px; height:35px; left: 493px; width:40px; z-index: 2;">
                                <span class=" px-2 py-1 uppercase text-2xl font-bold  ">
                                    @if($latestGame)
                                        {{ $latestGame->team1_score }} 
                                    @else
                                        No Team
                                    @endif
                                </span>
                            </div>
                            
                            <!-- Team 2 Name -->
                            <div class="team2-name absolute " style="text-align:center; top: 90px; height:35px; left: 43px; width:330px; z-index: 2;">
                                <span class="text-white px-2 py-1 uppercase text-2xl font-bold  ">
                                    @if($latestGame)
                                        {{ $latestGame->team2->name }}
                                    @else
                                        No Team
                                    @endif
                                </span>
                            </div>
                            <div class="team2-bola absolute {{ ($latestGame && $latestGame->who_is_serving == 'team2') ? '': 'hidden' }} " style="color: #014E82;text-align:center; top: 60px; height:35px; left: 390px; width:40px; z-index: 2;">
                                <span class=" px-2 py-1 uppercase text-2xl font-bold  ">
                                   <img src="images/bola.png" />
                                </span>
                            </div>
                            <div class="team2-totalwin absolute " style="color: #014E82;text-align:center; top: 90px; height:35px; left: 436px; width:40px; z-index: 2;">
                                <span class=" px-2 py-1 uppercase text-2xl font-bold  ">
                                    @if($latestGame && $latestGame->team2)
                                        {{ $team2TotalWins }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </div>
                            
                            <div class="team2-score absolute " style="color: #014E82;text-align:center; top: 90px; height:35px; left: 493px; width:40px; z-index: 2;">
                                <span class=" px-2 py-1 uppercase text-2xl font-bold  ">
                                    @if($latestGame)
                                        {{ $latestGame->team2_score }} 
                                    @else
                                        No Team
                                    @endif
                                </span>
                            </div>
                          
                            

                            <!-- Field Label -->
                            <div class="team1-name absolute " style="text-align:center; top: 178px; height:35px; left: 250px; width:320px; z-index: 2;">
                                <span class="text-white px-2 py-1 uppercase text-lg font-bold text-blue-900 ">
                                    {{ $field->name }} / {{ $latestGame ? ucfirst($latestGame->name) : 'No Game' }}
                                </span>
                            </div>
                             <!-- Field Label -->
                            @if ($team1TotalWins == 3 && $team2TotalWins == 3)
                            <div class="team1-tiebreak absolute " style="text-align:center; top: 140px; height:35px; left: 40px; width:320px; z-index: 2;">
                                <span class="text-white px-2 py-1 uppercase text-lg font-bold text-blue-900 ">
                                   <img src="images/tiebreak.png" />
                                </span>
                            </div>
                            @endif

                            @if ($latestGame->team1_score == 40 && $latestGame->team2_score == 40)
                            <div class="team1-tiebreak absolute " style="text-align:center; top: -20px; height:35px; left: 580px; width:320px; z-index: 2;">
                                <span class="text-white px-2 py-1 uppercase text-lg font-bold text-blue-900 ">
                                   <img src="images/gp.png" />
                                </span>
                            </div>
                            @endif


                        </div>
                    @else
                        <!-- Empty placeholder to maintain grid alignment -->
                        <div class="score-board relative" style="opacity: 0; pointer-events: none;">
                            <!-- Invisible placeholder with same dimensions -->
                        </div>
                    @endif
                @endfor
            </div>
        @endfor
    </div>

 

  
  
  </body>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function updateFields() {
      $.ajax({
        url: window.location.href,
        method: 'GET',
        success: function(response) {
          // Extract the field data from the response
          const parser = new DOMParser();
          const doc = parser.parseFromString(response, 'text/html');
          const newFieldsContainer = doc.querySelector('.w-full.flex.flex-col.mt-10.space-y-8');
          
          if (newFieldsContainer) {
            // Replace the current fields container with the updated one
            const currentContainer = document.querySelector('.w-full.flex.flex-col.mt-10.space-y-8');
            if (currentContainer) {
              currentContainer.innerHTML = newFieldsContainer.innerHTML;
            }
          }
        },
        error: function(xhr, status, error) {
          console.log('Error updating fields:', error);
        }
      });
    }
    
    // Start auto-refresh every 500ms
    setInterval(updateFields, 500);
   
  </script>
</html>  