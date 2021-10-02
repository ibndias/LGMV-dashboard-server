<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ERROR); //E_ALL

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check file validation
if (isset($_POST["submit"])) {
  $check = true;
  if ($check !== false) {
    echo "Report check OK</br>";
    $uploadOk = 1;
  } else {
    echo "Report check FAIL</br>";
    $uploadOk = 0;
  }
}

// Check if file already exists
// if (file_exists($target_file)) {
//   echo "Sorry, file already exists.</br>";
//   $uploadOk = 0;
// }

// Allow certain file formats
if ($filetype != "html") {
  echo "Sorry, only html files are allowed.</br>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.</br>";
  // if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.</br>";
    $text = file_get_contents($target_file);

    echo "Initialization...</br>";

    // $DOM = new DOMDocument;
    // $DOM->loadHTML($text);

    $dom = new DOMDocument();

    $dom->loadHTML($text);

    $xpath = new DOMXPath($dom);
    $result = $xpath->query('//script');

    echo "Parsing...</br>";

    function get_script_var($result, $pattern, $type)
    {
      $key = null;
      foreach ($result as $currScriptTag) {
        $currScriptContent = $currScriptTag->nodeValue;

        if ($type == "int")
          $matchFound = preg_match('/var ' . $pattern . ' = (.*);/', $currScriptContent, $matches);
        else if ($type == "str")
          $matchFound = preg_match('/var ' . $pattern . ' = new String\((.*)\);/', $currScriptContent, $matches);
        else if ($type == "num")
          $matchFound = preg_match('/var ' . $pattern . ' = new Number\((.*)\);/', $currScriptContent, $matches);
        else if ($type == "arr")
          $matchFound = preg_match('/var ' . $pattern . ' = new Array\((.*)\);/', $currScriptContent, $matches);
        else if ($type == "flt")
            $matchFound = preg_match('/var ' . $pattern . ' = new parseFloat\((.*)\);/', $currScriptContent, $matches);
        else
          return null;

        if ($matchFound) {
          /*
            * $matches[0] will contain the whole line like var key = "..." 
            * $matches[1] just contains the value of the var
            */
          $key = $matches[1];

          //echo $key . PHP_EOL;
        }
      }
      //return "Making a cup of $type.\n";
      return $key;
    }

    $TEMP_UNIT_F = get_script_var($result, "TEMP_UNIT_F", "int");
    $TEMP_UNIT_C = get_script_var($result, "TEMP_UNIT_C", "int");
    $PRESSURE_UNIT_KPA = get_script_var($result, "PRESSURE_UNIT_KPA", "int");
    $PRESSURE_UNIT_PSI = get_script_var($result, "PRESSURE_UNIT_PSI", "int");
    $WEIGHT_UNIT_KG = get_script_var($result, "WEIGHT_UNIT_KG", "int");
    $WEIGHT_UNIT_POUND = get_script_var($result, "WEIGHT_UNIT_POUND", "int");
    $CALORY_UNIT_KBTU = get_script_var($result, "CALORY_UNIT_KBTU", "int");
    $CALORY_UNIT_KW = get_script_var($result, "CALORY_UNIT_KW", "int");
    $MODE_STOP = get_script_var($result, "MODE_STOP", "int");
    $MODE_COOL = get_script_var($result, "MODE_COOL", "int");
    $MODE_HEAT = get_script_var($result, "MODE_HEAT", "int");
    $LANG_TYPE_KOR = get_script_var($result, "LANG_TYPE_KOR", "int");
    $LANG_TYPE_ENG = get_script_var($result, "LANG_TYPE_ENG", "int");
    $OLD_ITR = get_script_var($result, "OLD_ITR", "int");
    $NEW_ITR = get_script_var($result, "LANG_TYPE_KOR", "int");
    $old_itr = get_script_var($result, "old_itr", "num");
    $langType = $LANG_TYPE_ENG;
    $tempLangStr = get_script_var($result, "tempLangStr", "str");
    $tempUnitString = get_script_var($result, "tempUnitString", "str");
    $pressureUnitString = get_script_var($result, "pressureUnitString", "str");
    $calogyUnitString = get_script_var($result, "calogyUnitString", "str");
    $tempUnit = $TEMP_UNIT_C;
    $pressureUnit = $PRESSURE_UNIT_KPA;
    $caloryUnit = $CALORY_UNIT_KBTU;
    $lgmvVersion = get_script_var($result, "lgmvVersion", "str");
    $modelType = get_script_var($result, "modelType", "str");
    $writeDate = get_script_var($result, "writeDate", "str");
    $siteName = get_script_var($result, "siteName", "str");
    $modelName = get_script_var($result, "modelName", "str");
    $installerName = get_script_var($result, "installerName", "str");
    $distributorName = get_script_var($result, "distributorName", "str");
    $oduCapa = get_script_var($result, "oduCapa", "str");
    $iduNumber = get_script_var($result, "iduNumber", "str");
    $mainSWVersion = get_script_var($result, "mainSWVersion", "str");
    $eepromSWVersion = get_script_var($result, "eepromSWVersion", "str");
    $iduConbinationRatio = get_script_var($result, "iduConbinationRatio", "str");
    $serialNum = get_script_var($result, "serialNum", "str");
    $productNum = get_script_var($result, "productNum", "str");
    $oduTemp = get_script_var($result, "iduConbinationRatio", "num");
    $oduTempBefore = get_script_var($result, "oduTempBefore", "num");
    $oduRunMode = get_script_var($result, "oduRunMode", "num");
    $resultTestRun = get_script_var($result, "resultTestRun", "num");
    $oduErrorInfo = get_script_var($result, "oduErrorInfo", "num");
    $itrRefCheck = get_script_var($result, "itrRefCheck", "flt");
    $itrOduEevCheck = get_script_var($result, "itrOduEevCheck", "num");
    $itrIduEevCheck = get_script_var($result, "itrIduEevCheck", "num");
    $isIDUExport = get_script_var($result, "isIDUExport", "num");
    $iduCapSum = get_script_var($result, "iduCapSum", "int");

    echo "Finished. $iduCapSum </br>";
    echo $text;

  } else {
    echo "Sorry, there was an error uploading report.</br>";
  }
}
