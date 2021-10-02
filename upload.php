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


    $tbl_KW_MultiV = get_script_var($result, "tbl_KW_MultiV", "arrInt");
    $tbl_KW_MultiV_Export = get_script_var($result, "tbl_KW_MultiV_Export", "arrInt");
    $itrIduCapaArray = get_script_var($result, "itrIduCapaArray", "arrStr");
    $IduModelNameArray = get_script_var($result, "IduModelNameArray", "arrStr");
    $IduSerialNumArray = get_script_var($result, "IduSerialNumArray", "arrStr");
    $bbInv1TargetArray = get_script_var($result, "bbInv1TargetArray", "arrStr");
    $bbInv1CurrentArray = get_script_var($result, "bbInv1CurrentArray", "arrStr");
    $bbFan1TargetArray = get_script_var($result, "bbFan1TargetArray", "arrStr");
    $bbFan2TargetArray = get_script_var($result, "bbFan2TargetArray", "arrStr");
    $bbFan2CurrentArray = get_script_var($result, "bbFan2CurrentArray", "arrStr");
    $bbMainEEV1Array = get_script_var($result, "bbMainEEV1Array", "arrStr");
    $bbSCEEVArray = get_script_var($result, "bbSCEEVArray", "arrStr");
    $bbHighpressureArray = get_script_var($result, "bbHighpressureArray", "arrStr");
    $bbLowpressureArray = get_script_var($result, "bbLowpressureArray", "arrStr");
    $bbDischargeInv1Array = get_script_var($result, "bbDischargeInv1Array", "arrStr");
    $bbTSuctionArray = get_script_var($result, "bbTSuctionArray", "arrStr");
    $bbTAirArray = get_script_var($result, "bbTAirArray", "arrStr");

    $bbTLiqArray = get_script_var($result, "bbTLiqArray", "arrStr");
    $bbTSCoutArray = get_script_var($result, "bbTSCoutArray", "arrStr");
    $bbTHexArray = get_script_var($result, "bbTHexArray", "arrStr");
    $bbIInv1inArray = get_script_var($result, "bbIInv1inArray", "arrStr");
    $bbVInv1inArray = get_script_var($result, "bbVInv1inArray", "arrStr");
    $bbIPhaseInv1Array = get_script_var($result, "bbIPhaseInv1Array", "arrStr");

    $bbVInv1DCLinkArray = get_script_var($result, "bbVInv1DCLinkArray", "arrStr");
    $bbTCompIPMArray = get_script_var($result, "bbTCompIPMArray", "arrStr");
    $bbTotalIDUNumArray = get_script_var($result, "bbTotalIDUNumArray", "arrStr");
    $bbONIDUNumArray = get_script_var($result, "bbONIDUNumArray", "arrStr");
    $bbCoolIDUNumArray = get_script_var($result, "bbCoolIDUNumArray", "arrStr");
    $bbHeatIDUNUMArray = get_script_var($result, "bbHeatIDUNUMArray", "arrStr");
    $bbErrorNumArray = get_script_var($result, "bbErrorNumArray", "arrStr");
    $bb4wayArray = get_script_var($result, "bb4wayArray", "arrStr");
    $bbCompPreHeatArray = get_script_var($result, "bbCompPreHeatArray", "arrStr");
    $bbHotGasArray = get_script_var($result, "bbHotGasArray", "arrStr");
    $bbBaseHeaterArray = get_script_var($result, "bbBaseHeaterArray", "arrStr");
    $bbFanOverloadArray = get_script_var($result, "bbFanOverloadArray", "arrStr");
    $bbInv1IPMLimitArray = get_script_var($result, "bbInv1IPMLimitArray", "arrStr");
    $bbInv1OverloadArray = get_script_var($result, "bbInv1OverloadArray", "arrStr");

    $bbInVoltLimitArray = get_script_var($result, "bbInVoltLimitArray", "arrStr");
    $bbInILimitArray = get_script_var($result, "bbInILimitArray", "arrStr");
    $bbOperModeArray = get_script_var($result, "bbOperModeArray", "arrStr");
    $bbOilReturnArray = get_script_var($result, "bbOilReturnArray", "arrStr");
    $bbAllDefrostArray = get_script_var($result, "bbAllDefrostArray", "arrStr");
    $bbInv1ErrorArray = get_script_var($result, "bbInv1ErrorArray", "arrStr");

    $bbFan2ErrorArray = get_script_var($result, "bbFan2ErrorArray", "arrStr");
    $bbFan1ErrorArray = get_script_var($result, "bbFan1ErrorArray", "arrStr");
    $bbHumidityArray = get_script_var($result, "bbHumidityArray", "arrStr");

    $fddIdu1PipeInArray = get_script_var($result, "fddIdu1PipeInArray", "arrStr");
    $fddIdu1PipeOutArray = get_script_var($result, "fddIdu1PipeOutArray", "arrStr");
    $fddIdu1EEVArray = get_script_var($result, "fddIdu1EEVArray", "arrStr");
    $fddIdu1AirTempArray = get_script_var($result, "fddIdu1AirTempArray", "arrStr");
    
    $fddIdu2PipeInArray = get_script_var($result, "fddIdu2PipeInArray", "arrStr");
    $fddIdu2PipeOutArray = get_script_var($result, "fddIdu2PipeOutArray", "arrStr");
    $fddIdu2EEVArray = get_script_var($result, "fddIdu2EEVArray", "arrStr");
    $fddIdu2AirTempArray = get_script_var($result, "fddIdu2AirTempArray", "arrStr");
    
    $fddIdu3PipeInArray = get_script_var($result, "fddIdu3PipeInArray", "arrStr");
    $fddIdu3PipeOutArray = get_script_var($result, "fddIdu3PipeOutArray", "arrStr");
    $fddIdu3EEVArray = get_script_var($result, "fddIdu3EEVArray", "arrStr");
    $fddIdu3AirTempArray = get_script_var($result, "fddIdu3AirTempArray", "arrStr");

    $fddIdu4PipeInArray = get_script_var($result, "fddIdu4PipeInArray", "arrStr");
    $fddIdu4PipeOutArray = get_script_var($result, "fddIdu4PipeOutArray", "arrStr");
    $fddIdu4EEVArray = get_script_var($result, "fddIdu4EEVArray", "arrStr");
    $fddIdu4AirTempArray = get_script_var($result, "fddIdu4AirTempArray", "arrStr");

    $fddIdu5PipeInArray = get_script_var($result, "fddIdu5PipeInArray", "arrStr");
    $fddIdu5PipeOutArray = get_script_var($result, "fddIdu5PipeOutArray", "arrStr");
    $fddIdu5EEVArray = get_script_var($result, "fddIdu5EEVArray", "arrStr");
    $fddIdu5AirTempArray = get_script_var($result, "fddIdu5AirTempArray", "arrStr");

    $fddIdu6PipeInArray = get_script_var($result, "fddIdu6PipeInArray", "arrStr");
    $fddIdu6PipeOutArray = get_script_var($result, "fddIdu6PipeOutArray", "arrStr");
    $fddIdu6EEVArray = get_script_var($result, "fddIdu6EEVArray", "arrStr");
    $fddIdu6AirTempArray = get_script_var($result, "fddIdu6AirTempArray", "arrStr");

    $fddIdu7PipeInArray = get_script_var($result, "fddIdu7PipeInArray", "arrStr");
    $fddIdu7PipeOutArray = get_script_var($result, "fddIdu7PipeOutArray", "arrStr");
    $fddIdu7EEVArray = get_script_var($result, "fddIdu7EEVArray", "arrStr");
    $fddIdu7AirTempArray = get_script_var($result, "fddIdu7AirTempArray", "arrStr");

    $fddIdu8PipeInArray = get_script_var($result, "fddIdu8PipeInArray", "arrStr");
    $fddIdu8PipeOutArray = get_script_var($result, "fddIdu8PipeOutArray", "arrStr");
    $fddIdu8EEVArray = get_script_var($result, "fddIdu8EEVArray", "arrStr");
    $fddIdu8AirTempArray = get_script_var($result, "fddIdu8AirTempArray", "arrStr");

    $fddIdu9PipeInArray = get_script_var($result, "fddIdu9PipeInArray", "arrStr");
    $fddIdu9PipeOutArray = get_script_var($result, "fddIdu9PipeOutArray", "arrStr");
    $fddIdu9EEVArray = get_script_var($result, "fddIdu9EEVArray", "arrStr");
    $fddIdu9AirTempArray = get_script_var($result, "fddIdu9AirTempArray", "arrStr");

    $fddIdu10PipeInArray = get_script_var($result, "fddIdu10PipeInArray", "arrStr");
    $fddIdu10PipeOutArray = get_script_var($result, "fddIdu10PipeOutArray", "arrStr");
    $fddIdu10EEVArray = get_script_var($result, "fddIdu10EEVArray", "arrStr");
    $fddIdu10AirTempArray = get_script_var($result, "fddIdu10AirTempArray", "arrStr");

    $fddIdu1BeforeAirTempArray = get_script_var($result, "fddIdu1BeforeAirTempArray", "arrStr");
    $fddIdu2BeforeAirTempArray = get_script_var($result, "fddIdu2BeforeAirTempArray", "arrStr");
    $fddIdu3BeforeAirTempArray = get_script_var($result, "fddIdu3BeforeAirTempArray", "arrStr");
    $fddIdu4BeforeAirTempArray = get_script_var($result, "fddIdu4BeforeAirTempArray", "arrStr");
    $fddIdu5BeforeAirTempArray = get_script_var($result, "fddIdu5BeforeAirTempArray", "arrStr");  
    $fddIdu6BeforeAirTempArray = get_script_var($result, "fddIdu6BeforeAirTempArray", "arrStr");
    $fddIdu7BeforeAirTempArray = get_script_var($result, "fddIdu7BeforeAirTempArray", "arrStr");
    $fddIdu8BeforeAirTempArray = get_script_var($result, "fddIdu8BeforeAirTempArray", "arrStr");
    $fddIdu9BeforeAirTempArray = get_script_var($result, "fddIdu9BeforeAirTempArray", "arrStr");
    $fddIdu10BeforeAirTempArray = get_script_var($result, "fddIdu10BeforeAirTempArray", "arrStr");

    $fddcondhighpressArray = get_script_var($result, "fddcondhighpressArray", "arrStr");
    $fddIduErrorArray = get_script_var($result, "fddIduErrorArray", "arrStr");

    echo "Finished. </br>";
    echo $text;

  } else {
    echo "Sorry, there was an error uploading report.</br>";
  }
}
