<?php 
   $html = ' 
      <head> 
         <title>Tutorialspoint</title>
      </head> 
   
      <body> 
         <h2>Course details</h2> 
      
         <table border = "0"> 
            <tbody> 
               <tr> 
                  <td>Android</td> 
                  <td>Gopal</td> 
                  <td>Sairam</td> 
               </tr> 
         
               <tr> 
                  <td>Hadoop</td> 
                  <td>Gopal</td> 
                  <td>Satish</td> 
               </tr> 
         
               <tr> 
                  <td>HTML</td> 
                  <td>Gopal</td> 
                  <td>Raju</td> 
               </tr> 
         
               <tr> 
                  <td>Web technologies</td> 
                  <td>Gopal</td> 
                  <td>Javed</td> 
               </tr> 
         
               <tr> 
                  <td>Graphic</td> 
                  <td>Gopal</td> 
                  <td>Satish</td> 
               </tr> 
         
               <tr> 
                  <td>Writer</td> 
                  <td>Kiran</td> 
                  <td>Amith</td> 
               </tr> 
         
               <tr> 
                  <td>Writer</td> 
                  <td>Kiran</td> 
                  <td>Vineeth</td> 
               </tr> 
            </tbody> 
         </table> 
      </body> 
   </html> 
   '; 
   /*** a new dom object ***/ 
   $dom = new domDocument; 
   
   /*** load the html into the object ***/ 
   $dom->loadHTML($html); 
   
   /*** discard white space ***/ 
   $dom->preserveWhiteSpace = false; 
   
   /*** the table by its tag name ***/ 
   $tables = $dom->getElementsByTagName('table'); 
   
   /*** get all rows from the table ***/ 
   $rows = $tables->item(0)->getElementsByTagName('tr'); 
   
   /*** loop over the table rows ***/ 
   foreach ($rows as $row) {
      /*** get each column by tag name ***/ 
      $cols = $row->getElementsByTagName('td'); 
      
      /*** echo the values ***/ 
      echo 'Designation: '.$cols->item(0)->nodeValue.'<br />'; 
      echo 'Manager: '.$cols->item(1)->nodeValue.'<br />'; 
      echo 'Team: '.$cols->item(2)->nodeValue; 
      echo '<hr />'; 
   }
?> 