<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Semi Final Round</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <style>
      body{
        width: 1920px;
        height: 1080px;
        overflow: hidden;
        text-transform: uppercase;
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

      .name{
      position: absolute;
      width: 430px; 
      height: 55px;
      display: flex; flex-direction: column; justify-content: center; align-items: left; 
      margin-left: 40px;
      z-index: 2;
      text-align: left;
      color: #014E82;
      font-size: 24px;
      font-weight: bold;
      line-height: 20px;
    }
      
  </style>
  <body class=" justify-center ">
    <img src="images/bg1920.png" class="absolute" style="z-index: -1;" />
    <div id="header" class="w-full h-48 mt-30  flex justify-center items-center">
     
    </div>


  
    <!-- 2x2 Grid Layout for Semifinal Games -->
    @php
        $gameCount = min($games->count(), 4); // Maximum 4 games for 2x2 grid
        $columns = 2; // 2 columns per row
        $rows = 1; // Fixed 2 rows for 2x2 grid
    @endphp
    
    <div class="w-full flex flex-col" style="align-items: center; padding-top: 20px;">
        @for ($row = 0; $row < $rows; $row++)
            <div class="flex mb-8" style="gap: 40px; justify-content: center;">
                @for ($col = 0; $col < $columns; $col++)
                    @php
                        $index = $row * $columns + $col;
                    @endphp
                    @if ($index < $gameCount)
                        @php
                            $game = $games[$index];
                            
                            $team1FinalCount = \App\Models\Game::where('name', 'semifinal')->where('winner_id', $game->team1->id)
                                ->where('status', 'Completed')
                                ->where(function ($query) use ($game) {
                                    $query->where('team1_id', $game->team1->id)
                                          ->orWhere('team2_id', $game->team1->id);
                                })->where('winner_id', $game->team1->id)
                                ->count();
                            
                            // Count total final games for team2
                            $team2FinalCount = \App\Models\Game::where('name', 'semifinal')->where('winner_id', $game->team2->id)
                                ->where('status', 'Completed')
                                ->where(function ($query) use ($game) {
                                    $query->where('team1_id', $game->team2->id)
                                          ->orWhere('team2_id', $game->team2->id);
                                })
                                ->count();
                        @endphp
                        <div class="vs relative">
                            <img src="{{ asset('images/final-seminfinal-quarter2.png') }}" >
                               <div class="team1-name absolute  " style="text-align:center; left: 150px; top: 50px; width:400px;">
                                <span class="text-white" style="font-size: 32px">Semi Final {{ $index+1 }} </span>
                            </div>
                            <div class="team1-name absolute " style="left: 45px; top: 205px; background-color: red;">
                                <span class="name text-white" >{{ $game->team1->name }}</span>
                            </div>
                            <div class="team2-name absolute bg-red-300" style="left: 45px; top: 265px;">
                                <span class="name text-white">{{ $game->team2->name }}</span>  
                            </div>
                            <div class="team1-total absolute" style="left: 540px; top: 200px;">
                                <span class="name-horizontal text-white">{{ $game->team1->getLatestGame('quarterfinal')->team1_score }}</span>
                            </div>
                            <div class="team1-total absolute" style="left: 470px; top: 200px;">
                                <span class="name-horizontal text-white">{{ $team1FinalCount }}</span>  
                            </div>

                            <div class="team2-total absolute" style="left: 540px; top: 265px;">
                                <span class="name-horizontal text-white">{{ $game->team2->getLatestGame('quarterfinal')->team2_score }}</sp }}</span>  
                            </div>
                            <div class="team2-total absolute" style="left: 470px; top: 265px;">
                                <span class="name-horizontal text-white">{{ $team2FinalCount }}</span>  
                            </div>
                        </div>
                    @else
                        <!-- Empty placeholder to maintain grid alignment -->
                        <div class="vs relative" style="opacity: 0; pointer-events: none;">
                            <img src="{{ asset('images/final-seminfinal-quarter.png') }}" style="visibility: hidden;">
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
          // Target the correct container with the right classes and style attributes
          const newFieldsContainer = doc.querySelector('div.w-full.flex.flex-col[style*="align-items: center; padding-top: 20px;"]');
          
          if (newFieldsContainer) {
            // Replace the current fields container with the updated one
            const currentContainer = document.querySelector('div.w-full.flex.flex-col[style*="align-items: center; padding-top: 20px;"]');
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
