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
      <img src="images/logo2-padelore.png" class="h-12 absolute" style="left:200px" />
      <div class="pt-8 justify-center items-center h-20 ">
        <h1 class="text-4xl font-bold">Round Robin Preliminary Round</h1>
      </div>
      <img src="images/logo2-dvl.png" class="h-32 absolute" style="right:200px" />
    </div>


    <!-- Dynamic Score Boards Grid Layout - 3x2 grid (3 columns, 2 rows) -->
    @php
        $fieldCount = min($groups->count(), 6); // Maximum 6 groups for 3x2 grid
        $columns = 3; // 3 columns per row
        $rows = 2; // Fixed 2 rows for 3x2 grid
    @endphp
    
    <div class="w-full flex flex-col" style="align-items: center; padding-top: 20px;">
        @for ($row = 0; $row < $rows; $row++)
            <div class="flex mb-8" style="gap: 40px; justify-content: center;">
                @for ($col = 0; $col < $columns; $col++)
                    @php
                        $index = $row * $columns + $col;
                    @endphp
                    @if ($index < $fieldCount)
                        @php
                           $group = $groups[$index];
                           $teams = $group->teams;
                        @endphp
                        
                        <div class="score-board relative">
                            <img id="" src="images/{{ $teams->count() == 5 ? 'group5.png': 'group4.png' }} " style="z-index:10;width:100%; max-width: 500px;" />
                            <div class="group-name absolute " style="color: white;text-align:center; top: 45px; height:35px; left: 150px; width:210px; z-index: 2;">
                              <span class=" px-2 py-1 uppercase text-2xl font-bold  shadow">
                                  {{$group->name}}
                              </span>
                            </div>
                            @php $top =92; $i=0; @endphp
                            @foreach($teams as $team)
                                @php $top += 44; @endphp
                                 <div class="group-name absolute " style="color: white;text-align:left; top: {{ $top }}px; height:35px; left: 90px; width:200px; z-index: 2;">
                                      <span class=" px-2 py-1 uppercase text-2xl font-bold  shadow">
                                          {{$team->name}}
                                      </span>
                                  </div>
                                  <div class="group-name absolute " style="color: white;text-align:center; top: {{ $top }}px; height:35px; left: 310px; width:40px; z-index: 2;">
                                      <span class=" px-2 py-1 uppercase text-2xl font-bold  shadow">
                                         {{ $team->qualification_wins + $team->qualification_losses }}
                                      </span>
                                  </div>
                                  <div class="team-win absolute  " style="color: white;text-align:center; top: {{ $top }}px; height:35px; left: 350px; width:40px; z-index: 2;">
                                      <span class=" px-2 py-1 uppercase text-2xl font-bold  shadow">
                                         {{ $team->qualification_wins }}
                                      </span>
                                  </div>
                                  <div class="team-lose absolute  " style="color: white;text-align:center; top: {{ $top }}px; height:35px; left: 390px; width:40px; z-index: 2;">
                                      <span class=" px-2 py-1 uppercase text-2xl font-bold  shadow">
                                         {{ $team->qualification_losses }}
                                      </span>
                                  </div>
                                  <div class="team-game absolute " style="color: white;text-align:center; top: {{ $top }}px; height:35px; left: 430px; width:40px; z-index: 2;">
                                      <span class=" px-2 py-1 uppercase text-2xl font-bold  shadow">
                                         {{ $team->qualification_games }}
                                      </span>
                                  </div>

                            @endforeach
                        </div>
                    @else
                        <!-- Empty placeholder to maintain grid alignment -->
                        <div class="score-board relative" style="opacity: 0; pointer-events: none; width: 500px; height: 300px;">
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