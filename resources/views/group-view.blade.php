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
     
        display: flex; flex-direction: row; justify-content: center; align-items: center; 
        z-index: 2;
        text-align: center; color: #014E82; 
        font-size: 36px; 
        font-weight: bold; 
      }
      
      /* Tie-break beside styling */
      .tiebreak-small {
        font-size: 24px !important;
        font-weight: bold !important;
        color: #014E82 !important;
        vertical-align: middle !important;
        line-height: 1 !important;
        margin-left: 1px;
        display: inline !important;
      }
      
  </style>
  <body class=" justify-center ">
    <img src="images/bg1920.png" class="absolute" style="z-index: -1;" />
    <div id="header" class="w-full h-48 mt-30  flex justify-center items-center">
     
    </div>

    <div class="absolute uppercase" style="width: 191.517px; height: 47.5987px;  display: block; background-image: unset; top: 361.229px; left: 1139.5px;z-index: 2; font-size: 24px;color: #D76047; text-align: center;">
      POINT {{ $groupName }}
    </div>

    @if($error)
        <div class="absolute" style="top: 400px; left: 50%; transform: translateX(-50%); z-index: 10;">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline">{{ $error }}</span>
            </div>
        </div>
    @else
        <div id="content" class="flex w-full justify-center mt-20 ">
            <img id="roundrobin" src="images/roundrobin{{ $teams->count() }}.png" style="z-index:1;" />
            
            {{-- Team names horizontal --}}
            @foreach($teams as $index => $team)
                @if($index < 5)
                    <div class="name-horizontal c1 r{{ $index + 2 }}">
                        {{ $team->name }}
                    </div>
                @endif
            @endforeach

            {{-- Team names vertical --}}
            @foreach($teams as $index => $team)
                @if($index < 5)
                    <div class="name-vertical r1 c{{ $index + 2 }}">
                        {{ $team->name }}
                    </div>
                @endif
            @endforeach

            {{-- Score matrix including Win/Lose/Total columns --}}
            @foreach($teams as $i => $team1)
                @if($i < 5)
                    {{-- Team vs Team scores --}}
                    @foreach($teams as $j => $team2)
                        <div class="score r{{ $i + 2 }} c{{ $j + 2 }}">
                            @php
                                $scoreValue = $scores[$i][$j] ?? '';
                                $isTieBreak = strpos($scoreValue, '/') !== false;
                            @endphp
                            
                            @if($isTieBreak)
                                @php
                                    $parts = explode('/', $scoreValue);
                                    $mainScore = $parts[0];
                                    $tieBreakScore = $parts[1] ?? '0';
                                @endphp
                                <span style="font-size: 36px; font-weight: bold;">{{ $mainScore }}</span><span class="tiebreak-small">/{{ $tieBreakScore }}</span>
                            @else
                                {{ $scoreValue }}
                            @endif
                        </div>
                    @endforeach
                    
                    {{-- Win column --}}
                    <div class="score r{{ $i + 2 }} c{{ $teams->count() + 3 }}">
                        {{ $scores[$i][$teams->count()] ?? '0' }}
                    </div>
                    
                    {{-- Lose column --}}
                    <div class="score r{{ $i + 2 }} c{{ $teams->count() + 4 }}">
                        {{ $scores[$i][$teams->count() + 1] ?? '0' }}
                    </div>
                    
                    {{-- Total Games column --}}
                    <div class="score r{{ $i + 2 }} c{{ $teams->count() + 5 }}">
                        {{ $scores[$i][$teams->count() + 2] ?? '0' }}
                    </div>
                @endif
            @endforeach
        </div>
    @endif

 

  
  
  </body>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>

    var routes = ['Group A', 'Group B', 'Group C', 'Group D', 'Group E', 'Group F'];

    var group = '{{ $groupName }}';

    setTimeout(function () {
          // find the current group index
          var currentIndex = routes.indexOf(group);

          // move to next index, loop back to 0 if at the end
          var nextIndex = (currentIndex + 1) % routes.length;

          // build redirect URL (adjust as needed)
          var nextGroup = routes[nextIndex];
          window.location.href = "?group=" + encodeURIComponent(nextGroup);
      }, 8000);
 


    function updateGroupView() {
      $.ajax({
        url: window.location.href,
        method: 'GET',
        success: function(response) {
          // Extract the content data from the response
          const parser = new DOMParser();
          const doc = parser.parseFromString(response, 'text/html');
          const newContent = doc.querySelector('#content');
          
          if (newContent) {
            // Replace the current content container with the updated one
            const currentContent = document.querySelector('#content');
            if (currentContent) {
              currentContent.innerHTML = newContent.innerHTML;
            }
          }
        },
        error: function(xhr, status, error) {
          console.log('Error updating group view:', error);
        }
      });
    }
    
    // Start auto-refresh every 500ms
    setInterval(updateGroupView, 500);
    
    console.log('Group-view page loaded with AJAX auto-refresh every 500ms');
  </script>
</html>  