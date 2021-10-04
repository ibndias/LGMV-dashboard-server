<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ERROR); //E_ALL
include 'minifyjs.php';

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$filename = strtolower(pathinfo($target_file, PATHINFO_FILENAME));

// Check file validation
if (isset($_POST["submit"])) {
  $check = true;
  if ($check !== false) {
    echo "Report check... [OK]</br>";
    $uploadOk = 1;
  } else {
    echo "Report check... [FAIL]</br>";
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

    echo "Initialization... ";

    // $html = $dom->saveHTML();
    $dom = new DOMDocument();
    //$text = minifyJavascript($text);
    $dom->loadHTML($text);

    $xpath = new DOMXPath($dom);
    $result = $xpath->query('//script');
    echo "[OK]</br>";


    //$output = minifyJavascript($inp);
    //$pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
    //$output = preg_replace($pattern, '', $output);
    //echo nl2br($output);



    $jsontext = "{ ";

    function get_script_var($result, $pattern, $type)
    {
      $key = null;
      foreach ($result as $currScriptTag) {
        $currScriptContent = $currScriptTag->nodeValue;

        //Remove comments
        $currScriptContent = preg_replace('/#.*/', '', preg_replace('#//.*#', '', preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#', '', ($currScriptContent))));
        //Remove line breaks
        $currScriptContent = trim(preg_replace('/\s+/', ' ', $currScriptContent));


        if ($type == "int")
          $matchFound = preg_match('/var ' . $pattern . ' = (.*?);/', $currScriptContent, $matches);
        else if ($type == "str")
          $matchFound = preg_match('/var ' . $pattern . ' = new String\((.*?)\);/', $currScriptContent, $matches);
        else if ($type == "num")
          $matchFound = preg_match('/var ' . $pattern . ' = new Number\((.*?)\);/', $currScriptContent, $matches);
        else if ($type == "arr")
          $matchFound = preg_match('/var ' . $pattern . ' = new Array\((.*?)\);/', $currScriptContent, $matches);
        else if ($type == "arrStr")
          $matchFound = preg_match('/var ' . $pattern . ' = new Array\((.*?)\);/', $currScriptContent, $matches);
        else if ($type == "arrInt")
          $matchFound = preg_match('/var ' . $pattern . ' = new Array\((.*?)\);/', $currScriptContent, $matches);
        else if ($type == "flt")
          $matchFound = preg_match('/var ' . $pattern . ' = new parseFloat\((.*?)\);/', $currScriptContent, $matches);
        else {
          echo "Oh no! $pattern returns null!";
          return null;
        }
        if ($matchFound) {
          /*
            * $matches[0] will contain the whole line like var key = "..." 
            * $matches[1] just contains the value of the var
            */
          $key = $matches[1];
          //echo $matches[0];
          //echo $pattern . " = " . $key  . "</br>";

          //format to JSON
          if ($type == "arrInt" | $type == "arrStr" | $type == "arr") {
            //echo '"' . $pattern . '": ' . '[' . $key . ']'  . ",</br>";
            $GLOBALS['jsontext'] .= '"' . $pattern . '": ' . '[' . $key . ']'  . ",\n";
          } else {
            //echo '"' . $pattern . '": ' . $key  . ",</br>";
            $GLOBALS['jsontext'] .= '"' . $pattern . '": ' . $key  . ",\n";
          }
        }
      }
      //return "Making a cup of $type.\n";
      return $key;
    }

    echo "Parsing... ";
    // Report 1 (LGMV 1.0.9 Report Parse)
    $lgmvVersion = get_script_var($result, "lgmvVersion", "str");
    $LGMVVersionString = get_script_var($result, "LGMVVersionString", "str");
    if ($lgmvVersion == null && $LGMVVersionString == null) {
      echo "[Unknown LGMV Report Version]";
      exit();
    } else if ($lgmvVersion != null) {

      echo "[LGMV 1.0.9 Report Detected]";
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
      //END Report 1 LGMV 1.0.9
    }
    //LGMV Version 1.0.8
    else {
      echo "[LGMV 1.0.8 Report Detected]";

      $TEMP_UNIT_F = get_script_var($result, "TEMP_UNIT_F", "int");
      $TEMP_UNIT_C = get_script_var($result, "TEMP_UNIT_C", "int");
      $WEIGHT_UNIT_KG = get_script_var($result, "WEIGHT_UNIT_KG", "int");
      $WEIGHT_UNIT_POUND = get_script_var($result, "WEIGHT_UNIT_POUND", "int");
      $MOOD_COOL = get_script_var($result, "MOOD_COOL", "int");
      $MOOD_HEAT = get_script_var($result, "MOOD_HEAT", "int");
      $LANG_TYPE_KOR = get_script_var($result, "LANG_TYPE_KOR", "int");
      $LANG_TYPE_ENG = get_script_var($result, "LANG_TYPE_ENG", "int");

      $langType = $LANG_TYPE_ENG;

      $tempLangStr = get_script_var($result, "tempLangStr", "str");
      $tempUnitString = get_script_var($result, "tempUnitString", "str");
      $pressureUnitString = get_script_var($result, "pressureUnitString", "str");
      $weightUnitString = get_script_var($result, "weightUnitString", "str");

      $pressureUnit = $PRESSURE_UNIT_KPA;
      $tempUnit = $TEMP_UNIT_C;
      $weightUnit = $WEIGHT_UNIT_KG;

      $installerName = get_script_var($result, "installerName", "str");
      $installerInfo = get_script_var($result, "installerInfo", "str");
      $inspectorName = get_script_var($result, "inspectorName", "str");
      $inspectorInfo = get_script_var($result, "inspectorInfo", "str");
      $managerName = get_script_var($result, "managerName", "str");
      $managerInfo = get_script_var($result, "managerInfo", "str");
      $siteName = get_script_var($result, "siteName", "str");
      $siteInfo = get_script_var($result, "siteInfo", "str");
      $modelType = get_script_var($result, "modelType", "str");
      $userModelName = get_script_var($result, "userModelName", "str");
      $writeDate = get_script_var($result, "writeDate", "str");
      $odu1Version = get_script_var($result, "odu1Version", "str");
      $odu2Version = get_script_var($result, "odu2Version", "str");
      $odu3Version = get_script_var($result, "odu3Version", "str");
      $odu4Version = get_script_var($result, "odu4Version", "str");

      $oduNumber = get_script_var($result, "oduNumber", "str");
      $iduNumber = get_script_var($result, "iduNumber", "str");
      $hruNumber = get_script_var($result, "hruNumber", "str");

      $iduTemp = get_script_var($result, "iduTemp", "num");
      $oduTemp = get_script_var($result, "oduTemp", "num");
      $runCycleInfo = get_script_var($result, "runCycleInfo", "num");

      $totalRefgVolume = get_script_var($result, "totalRefgVolume", "num");
      $refgResultVolume = get_script_var($result, "refgResultVolume", "num");
      $systemErrorInfo = get_script_var($result, "systemErrorInfo", "num");
      $refgErrorInfo = get_script_var($result, "refgErrorInfo", "num");
      $oduErrorInfo = get_script_var($result, "oduErrorInfo", "num");
      $iduErrorInfo = get_script_var($result, "iduErrorInfo", "num");

      $tempErrorInfo = get_script_var($result, "tempErrorInfo", "num");
      $capacityErrorInfo = get_script_var($result, "capacityErrorInfo", "num");
      $masterEEVErrorInfo = get_script_var($result, "masterEEVErrorInfo", "num");
      $slave1EEVErrorInfo = get_script_var($result, "slave1EEVErrorInfo", "num");
      $slave2EEVErrorInfo = get_script_var($result, "slave2EEVErrorInfo", "num");
      $slave3EEVErrorInfo = get_script_var($result, "slave3EEVErrorInfo", "num");
      $E05Reason = get_script_var($result, "E05Reason", "num");
      $iduTempMin = get_script_var($result, "iduTempMin", "num");
      $iduTempMax = get_script_var($result, "iduTempMax", "num");
      $basicRef = get_script_var($result, "basicRef", "num");

      $iduEevErrorArray = get_script_var($result, "iduEevErrorArray", "arrStr");
      $IduModelNameArray = get_script_var($result, "IduModelNameArray", "arrStr");
      $IduSerialNumArray = get_script_var($result, "IduSerialNumArray", "arrStr");
      $HruModelNameArray = get_script_var($result, "HruModelNameArray", "arrStr");
      $HruSerialNumArray = get_script_var($result, "HruSerialNumArray", "arrStr");

      $masterHighPressureMin = get_script_var($result, "masterHighPressureMin", "num");
      $masterHighPressureMax = get_script_var($result, "masterHighPressureMax", "num");
      $masterHighPressureAvg = get_script_var($result, "masterHighPressureAvg", "num");
      $slave1HighPressureMin = get_script_var($result, "slave1HighPressureMin", "num");
      $slave1HighPressureMax = get_script_var($result, "slave1HighPressureMax", "num");
      $slave1HighPressureAvg = get_script_var($result, "slave1HighPressureAvg", "num");
      $slave2HighPressureMin = get_script_var($result, "slave2HighPressureMin", "num");
      $slave2HighPressureMax = get_script_var($result, "slave2HighPressureMax", "num");
      $slave2HighPressureAvg = get_script_var($result, "slave2HighPressureAvg", "num");
      $slave3HighPressureMin = get_script_var($result, "slave3HighPressureMin", "num");
      $slave3HighPressureMax = get_script_var($result, "slave3HighPressureMax", "num");
      $slave3HighPressureAvg = get_script_var($result, "slave3HighPressureAvg", "num");

      $masterEEVMin = get_script_var($result, "masterEEVMin", "num");
      $masterEEVMax = get_script_var($result, "masterEEVMax", "num");
      $masterEEVAvg = get_script_var($result, "masterEEVAvg", "num");

      $slave1EEVMin = get_script_var($result, "slave1EEVMin", "num");
      $slave1EEVMax = get_script_var($result, "slave1EEVMax", "num");
      $slave1EEVAvg = get_script_var($result, "slave1EEVAvg", "num");

      $slave2EEVMin = get_script_var($result, "slave2EEVMin", "num");
      $slave2EEVMax = get_script_var($result, "slave2EEVMax", "num");
      $slave2EEVAvg = get_script_var($result, "slave2EEVAvg", "num");

      $slave3EEVMin = get_script_var($result, "slave3EEVMin", "num");
      $slave3EEVMax = get_script_var($result, "slave3EEVMax", "num");
      $slave3EEVAvg = get_script_var($result, "slave3EEVAvg", "num");

      $masterDischargeSHAvg = get_script_var($result, "masterDischargeSHAvg", "num");
      $slave1DischargeSHAvg = get_script_var($result, "slave1DischargeSHAvg", "num");
      $slave2DischargeSHAvg = get_script_var($result, "slave2DischargeSHAvg", "num");
      $slave3DischargeSHAvg = get_script_var($result, "slave3DischargeSHAvg", "num");
      $masterSutionSHAvg = get_script_var($result, "masterSutionSHAvg", "num");
      $slave1SutionSHAvg = get_script_var($result, "slave1SutionSHAvg", "num");
      $slave2SutionSHAvg = get_script_var($result, "slave2SutionSHAvg", "num");
      $slave3SutionSHAvg = get_script_var($result, "slave3SutionSHAvg", "num");
      $masterSuperCoolAvg = get_script_var($result, "masterSuperCoolAvg", "num");
      $slave1SuperCoolAvg = get_script_var($result, "slave1SuperCoolAvg", "num");
      $slave2SuperCoolAvg = get_script_var($result, "slave2SuperCoolAvg", "num");
      $slave3SuperCoolAvg = get_script_var($result, "slave3SuperCoolAvg", "num");
      $masterINV1DichargeTempAvg = get_script_var($result, "masterINV1DichargeTempAvg", "num");
      $slave1INV1DichargeTempAvg = get_script_var($result, "slave1INV1DichargeTempAvg", "num");
      $slave2INV1DichargeTempAvg = get_script_var($result, "slave2INV1DichargeTempAvg", "num");
      $slave3INV1DichargeTempAvg = get_script_var($result, "slave3INV1DichargeTempAvg", "num");
      $masterINV2DichargeTempAvg = get_script_var($result, "masterINV2DichargeTempAvg", "num");
      $slave1INV2DichargeTempAvg = get_script_var($result, "slave1INV2DichargeTempAvg", "num");
      $slave2INV2DichargeTempAvg = get_script_var($result, "slave2INV2DichargeTempAvg", "num");
      $slave3INV2DichargeTempAvg = get_script_var($result, "slave3INV2DichargeTempAvg", "num");

      $masterINV1InputVoltageMin = get_script_var($result, "masterINV1InputVoltageMin", "num");
      $masterINV1InputVoltageMax = get_script_var($result, "masterINV1InputVoltageMax", "num");
      $masterINV1InputVoltageAvg = get_script_var($result, "masterINV1InputVoltageAvg", "num");

      $slave1INV1InputVoltageMin = get_script_var($result, "slave1INV1InputVoltageMin", "num");
      $slave1INV1InputVoltageMax = get_script_var($result, "slave1INV1InputVoltageMax", "num");
      $slave1INV1InputVoltageAvg = get_script_var($result, "slave1INV1InputVoltageAvg", "num");

      $slave2INV1InputVoltageMin = get_script_var($result, "slave2INV1InputVoltageMin", "num");
      $slave2INV1InputVoltageMax = get_script_var($result, "slave2INV1InputVoltageMax", "num");
      $slave2INV1InputVoltageAvg = get_script_var($result, "slave2INV1InputVoltageAvg", "num");

      $slave3INV1InputVoltageMin = get_script_var($result, "slave3INV1InputVoltageMin", "num");
      $slave3INV1InputVoltageMax = get_script_var($result, "slave3INV1InputVoltageMax", "num");
      $slave3INV1InputVoltageAvg = get_script_var($result, "slave3INV1InputVoltageAvg", "num");

      $masterINV1InputCurrentMin = get_script_var($result, "masterINV1InputCurrentMin", "num");
      $masterINV1InputCurrentMax = get_script_var($result, "masterINV1InputCurrentMax", "num");
      $masterINV1InputCurrentAvg = get_script_var($result, "masterINV1InputCurrentAvg", "num");

      $slave1INV1InputCurrentMin = get_script_var($result, "slave1INV1InputCurrentMin", "num");
      $slave1INV1InputCurrentMax = get_script_var($result, "slave1INV1InputCurrentMax", "num");
      $slave1INV1InputCurrentAvg = get_script_var($result, "slave1INV1InputCurrentAvg", "num");

      $slave2INV1InputCurrentMin = get_script_var($result, "slave2INV1InputCurrentMin", "num");
      $slave2INV1InputCurrentMax = get_script_var($result, "slave2INV1InputCurrentMax", "num");
      $slave2INV1InputCurrentAvg = get_script_var($result, "slave2INV1InputCurrentAvg", "num");

      $slave3INV1InputCurrentMin = get_script_var($result, "slave3INV1InputCurrentMin", "num");
      $slave3INV1InputCurrentMax = get_script_var($result, "slave3INV1InputCurrentMax", "num");
      $slave3INV1InputCurrentAvg = get_script_var($result, "slave3INV1InputCurrentAvg", "num");

      $masterINV1PhaseCurrentAvg = get_script_var($result, "masterINV1PhaseCurrentAvg", "num");
      $slave1INV1PhaseCurrentAvg = get_script_var($result, "slave1INV1PhaseCurrentAvg", "num");
      $slave2INV1PhaseCurrentAvg = get_script_var($result, "slave2INV1PhaseCurrentAvg", "num");
      $slave3INV1PhaseCurrentAvg = get_script_var($result, "slave3INV1PhaseCurrentAvg", "num");

      $masterINV2PhaseCurrentAvg = get_script_var($result, "masterINV2PhaseCurrentAvg", "num");
      $slave1INV2PhaseCurrentAvg = get_script_var($result, "slave1INV2PhaseCurrentAvg", "num");
      $slave2INV2PhaseCurrentAvg = get_script_var($result, "slave2INV2PhaseCurrentAvg", "num");
      $slave3INV2PhaseCurrentAvg = get_script_var($result, "slave3INV2PhaseCurrentAvg", "num");

      $capacityM = get_script_var($result, "capacityM", "num");
      $capacityS1 = get_script_var($result, "capacityS1", "num");
      $capacityS2 = get_script_var($result, "capacityS2", "num");
      $capacityS3 = get_script_var($result, "capacityS3", "num");

      $odu1EEPVersion = get_script_var($result, "odu1EEPVersion", "str");
      $odu2EEPVersion = get_script_var($result, "odu2EEPVersion", "str");
      $odu3EEPVersion = get_script_var($result, "odu3EEPVersion", "str");
      $odu4EEPVersion = get_script_var($result, "odu4EEPVersion", "str");

      $capacityRatio = get_script_var($result, "capacityRatio", "num");
      $iduCapaSum = get_script_var($result, "iduCapaSum", "num");
      $systemError = get_script_var($result, "systemError", "num");
      $errorUnit = get_script_var($result, "errorUnit", "num");

      $SerialNumberM = get_script_var($result, "SerialNumberM", "str");
      $SerialNumberS1 = get_script_var($result, "SerialNumberS1", "str");
      $SerialNumberS2 = get_script_var($result, "SerialNumberS2", "str");
      $SerialNumberS3 = get_script_var($result, "SerialNumberS3", "str");

      $ModelNameM = get_script_var($result, "ModelNameM", "str");
      $ModelNameS1 = get_script_var($result, "ModelNameS1", "str");
      $ModelNameS2 = get_script_var($result, "ModelNameS2", "str");
      $ModelNameS3 = get_script_var($result, "ModelNameS3", "str");
      $LGMVVersionString = get_script_var($result, "LGMVVersionString", "str");
    }
    $jsontext = substr($jsontext, 0, -2);
    $jsontext .= "}";

    echo "[OK]</br>";

    echo "Writing JSON... ";
    //TODO: Use json_encode instead from scratch (but need to really parse the arrays from html)
    $jsonfile = fopen("uploads/" . $filename . ".json", "w") or die("Unable to open file!");
    fwrite($jsonfile, $jsontext);
    fclose($jsonfile);

    echo "[OK]</br>";

    $jsonfile = "uploads/" . $filename . ".json";
    echo 'JSON created in <a href="uploads/' . $filename . '.json">here</a></br>';
    echo "Upload to CouchDB... ";

    //Send to couchDB
    $ch = curl_init();

    //curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5984/customers/' . $customer['username']);
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5984/lgmv/' . $filename);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); /* or PUT */
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsontext);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-type: application/json',
      'Accept: */*'
    ));

    curl_setopt($ch, CURLOPT_USERPWD, 'admin:admin');

    $response = curl_exec($ch);

    curl_close($ch);
    echo $response . "</br>";
    $response = json_decode($response, true);

    if ($response['error'] == "conflict")
      echo "Upload skipped. Report already exist. </br>";
    else
      echo "Upload [OK] </br>";

    echo "Finish. </br></br>";

    echo '<form action="http://pc.derrylab.com:5984/_utils/"><input type="submit" value="Go to CouchDB" /></form>';
    echo '<form action="http://pc.derrylab.com"><input type="submit" value="Upload another Report" /></form>';
    echo '<form action="'. $jsonfile .'"><input type="submit" value="View JSON" /></form>';
    echo $text;
  } else {
    echo "Sorry, there was an error uploading report.</br>";
  }
}
