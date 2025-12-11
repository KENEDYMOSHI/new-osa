<?php 

function Success(){
 
    $flashData = session()->getFlashdata('success');
   
    if($flashData != null){
        return <<<"HTML"
        <script>
           swal({
           title:'$flashData',
           icon: "success",
           });
        </script>
     HTML;
    }
}
function Error(){
  
    $flashData = session()->getFlashdata('error');
   
     if($flashData != null){
      return <<<"HTML"
        <script>
           swal({
           title:'$flashData',
           icon: "warning",
           });
        </script>
     HTML;
     }


      function name(){
         ob_start(); // Start output buffering
         ?>
         <!DOCTYPE html>
         <html>
         <head>
             <title>Output Buffering Example</title>
         </head>
         <body>
             <h1>Hello, World!</h1>
             <p></p>
         </body>
         </html>
         <?php
         $html = ob_get_contents(); // Capture HTML output
         ob_end_clean(); // Clear the buffer without sending output
         
         // Modify output before displaying
         $html = str_replace("Hello", "Welcome to", $html);
     }
}
