<?php

# RaceClocker Timing JSON formatter
# Copyright G.J.Nieuwenhuis 2025

# 2025-10-28 v0.1   Initial version


$Version = "v0.1";

# Create empty JSON array
$JSONData = [];

# Get all required parameters

# We start with true and set to false if any check fails
$ParametersComplete = true;

# Return JSONData script version in JSON data
$JSONMsg = array('JSONVersion' => $Version);
$JSONData[] = $JSONMsg;


# Fetch the RaceClocker URL ID for the results
if (isset($_GET['RaceClockerID']) && !empty($_GET['RaceClockerID'])) {
   $RaceClockerID = $_GET['RaceClockerID'];
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'RaceClockerID parameter is missing or incorrect');
    $JSONData[] = $ErrorMsg;
}



# Proceed if we have all required parameters
if ($ParametersComplete) {

    # Define the RaceClocker URL to be used
    $RaceClockerURL = "https://raceclocker.com/".$RaceClockerID."?json=1";

    # Fetch URL contents to scrape the race titles
    $RaceClockerData = file_get_contents($RaceClockerURL);
    # Set URL check flags
    $RaceClockerURLCheck = true;

    # Fetch the RaceClocker names for the primary race from the URL content
    preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)["\']\s*\/?>/i', $RaceClockerData, $RaceClockerDataMatches);
    if (isset($RaceClockerDataMatches[1])) {
        $TitleData= array('Title' => $RaceClockerDataMatches[1]);
    } else {
        $TitleData= array('Title' => 'Not found');
    }
    $JSONData[] = $TitleData;

     
    # Fetch the race results data
    if (preg_match('/let\s+AllResults\s*=\s*(.*);/', $RaceClockerData, $matches)) {
        
        # Decode the JSON results from the html
        $jsonraw = $matches[1];
        $json = json_decode($jsonraw,true);
        
        if (is_array($json)) {
            $StatusMsg = array('RaceClockerURLStatus' => 'OK');
            $JSONData[] = $StatusMsg;
        } else {
            $RaceClockerURLCheck = false;
        }
    } else {
        $RaceClockerURLCheck = false;
    }


    # Report warnings
    if (!$RaceClockerURLCheck) {
        # Add warning messages
        if (!$RaceClockerURLCheck) {
            $ErrorMsg = array('Error' => 'Unable to fetch results for primary URL');
            $JSONData[] = $ErrorMsg;
            $StatusMsg = array('RaceClockerURLStatus' => 'Failed');
            $JSONData[] = $StatusMsg;
        }
    }


    # Are all checks valid? Generate JSON output
    if ($RaceClockerURLCheck) {
        $JSONData[] = $json;
    }

} else {
    $ErrorMsg = array('Error' => 'Required parameters are missing');
    $JSONData[] = $ErrorMsg;
}

# Output JSON data to screen
header('Content-Type: application/json; charset=utf-8');
echo json_encode($JSONData);


?>

