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

    <div class="absolute" style="width: 191.517px; height: 47.5987px;  display: block; background-image: unset; top: 361.229px; left: 1139.5px;z-index: 2; font-size: 24px;color: #D76047; text-align: center;">
      POINT GROUP <span id="group-name"></span>
    </div>

    <div id="content" class="flex w-full justify-center mt-20 ">
        <img id="roundrobin"src="images/roundrobin5.png" style="z-index:1;" />
        <div id="t-h-1" class="name-horizontal c1 r2" >
          
        </div>
          <div  id="t-h-2"  class="name-horizontal c1 r3">
             
          </div>
         <div  id="t-h-3"  class="name-horizontal c1 r4">
          
         </div>
          <div  id="t-h-4"  class="name-horizontal c1 r5">
           
         </div>
          <div  id="t-h-5"  class="name-horizontal c1 r6">
           
         </div>

         <div id="t-v-1" class="name-vertical r1 c2">
            
         </div>

         <div id="t-v-2" class="name-vertical r1 c3">
           
         </div>
           <div id="t-v-3" class="name-vertical r1 c4">
           
         </div>
           <div id="t-v-4" class="name-vertical r1 c5">
           
         </div>
         <div id="t-v-5" class="name-vertical r1 c6">
           
         </div>

         <div id="score-0-0" class="score r2 c2">
          
         </div>
          <div id="score-0-1" class="score r2 c3">
          
         </div>
          <div id="score-0-2" class="score r2 c4">
          
         </div>
          <div id="score-0-3" class="score r2 c5">
          
         </div>
          <div id="score-0-4" class="score r2 c6">
          
         </div>
          <div id="score-0-5" class="score r2 c7">
          
         </div>
          <div id="score-0-6" class="score r2 c8">
          
         </div>
          <div id="score-0-7" class="score r2 c9">
          
         </div>
         

         <div id="score-1-0" class="score r3 c2">
          
         </div>
          <div id="score-1-1" class="score r3 c3">
          
         </div>
          <div id="score-1-2" class="score r3 c4">
          
         </div>
          <div id="score-1-3" class="score r3 c5">
          
         </div>
          <div id="score-1-4" class="score r3 c6">
          
         </div>
          <div id="score-1-5" class="score r3 c7">
          
         </div>
          <div id="score-1-6" class="score r3 c8">
          
         </div>
          <div id="score-1-7" class="score r3 c9">
          
         </div>

        

          <div id="score-2-0" class="score r4 c2">
          
         </div>
          <div id="score-2-1" class="score r4 c3">
          
         </div>
          <div id="score-2-2"  class="score r4 c4">
          
         </div>
          <div id="score-2-3" class="score r4 c5">
          
         </div>
          <div id="score-2-4" class="score r4 c6">
          
         </div>
          <div id="score-2-5" class="score r4 c7">
          
         </div>
          <div id="score-2-6" class="score r4 c8">
          
         </div>
          <div id="score-2-7" class="score r4 c9">
          
         </div>



          <div id="score-3-0" class="score r5 c2">
          
         </div>
          <div id="score-3-1" class="score r5 c3">
          
         </div>
          <div id="score-3-2" class="score r5 c4">
          
         </div>
          <div id="score-3-3" class="score r5 c5">
          
         </div>
          <div id="score-3-4"  class="score r5 c6">
          
         </div>
          <div id="score-3-5"  class="score r5 c7">
          
         </div>
          <div id="score-3-6"  class="score r5 c8">
          
         </div>
          <div id="score-3-7"  class="score r5 c9">
          
         </div>

          <div id="score-4-0"  class="score r6 c2">
          
         </div>
          <div id="score-4-1" class="score r6 c3">
          
         </div>
          <div id="score-4-2" class="score r6 c4">
          
         </div>
          <div id="score-4-3" class="score r6 c5">
          
         </div>
          <div id="score-4-4" class="score r6 c6">
          
         </div>
          <div id="score-4-5" class="score r6 c7">
          
         </div>
          <div id="score-4-6" class="score r6 c8">
          
         </div>
          <div id="score-4-7" class="score r6 c9">
          
         </div>
    </div>

 

  
  
  </body>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // ajax for 500ms to getscore.php update scores using jQuery
    function updateScores() {
   
      var urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('group')) {
        group = urlParams.get('group');
        $('#group-name').text(group);
      } else {
       alert('No group specified in URL. Please add ?group=GROUP_NAME to the URL.');
        return;
      }

      $.getJSON('getscore?group='+group, function(data) {
    
      if (data.teams && Array.isArray(data.teams)) {
        if (data.teams.length == 5) {
          $('#roundrobin').attr('src', 'images/roundrobin5.png');
          
        }else if (data.teams.length == 4) {
          $('#roundrobin').attr('src', 'images/roundrobin4.png');
        }
        for (let i = 0; i < data.teams.length; i++) {
        const nameElement = $(`#t-h-${i + 1}`);
        const nameElement2 = $(`#t-v-${i + 1}`);
       
        if (nameElement.length) {
          nameElement.text(data.teams[i]);
        }
        if (nameElement2.length) {
          nameElement2.text(data.teams[i]);
        }
        }
      }
      // handle data.scores
      if (data.scores && Array.isArray(data.scores)) {
        for (let i = 0; i < data.scores.length; i++) {
        for (let j = 0; j < data.scores[i].length; j++) {
          const scoreCell = $(`#score-${i}-${j}`);
          if (scoreCell.length) {
          scoreCell.text(data.scores[i][j]);
          }
        }
        }
      }
      });
    }
    setInterval(updateScores, 500);
    updateScores();
   
  </script>
</html>%    