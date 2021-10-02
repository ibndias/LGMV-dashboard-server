<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ERROR); //E_ALL

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$filetype = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check file validation
if(isset($_POST["submit"])) {
  $check = true;
  if($check !== false) {
    echo "Report check OK\n";
    $uploadOk = 1;
  } else {
    echo "Report check FAIL\n";
    $uploadOk = 0;
  }
}

// Check if file already exists
// if (file_exists($target_file)) {
//   echo "Sorry, file already exists.\n";
//   $uploadOk = 0;
// }

// Allow certain file formats
if($filetype != "html" ) {
  echo "Sorry, only html files are allowed.\n";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.\n";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.\n";
    $text= file_get_contents($target_file);
    echo "HELLO1.\n";
    
    // $DOM = new DOMDocument;
    // $DOM->loadHTML($text);

    $dom = new DOMDocument();

    $dom->loadHTML($text);

    $xpath = new DOMXPath($dom);
    $result = $xpath->query('//script');
    echo "HELLO2.\n";
    foreach($result as $currScriptTag)
    {
        $currScriptContent = $currScriptTag->nodeValue;

        $matchFound = preg_match('/var LANG_TYPE_ENG = (.*);/', $currScriptContent, $matches);

        if($matchFound)
        {
            /*
            * $matches[0] will contain the whole line like var key = "..." 
            * $matches[1] just contains the value of the var
            */
            $key = $matches[1];

            echo $key.PHP_EOL;
        }
    }
    echo "HELLO4.\n";

  } else {
    echo "Sorry, there was an error uploading report.\n";
  }
}
