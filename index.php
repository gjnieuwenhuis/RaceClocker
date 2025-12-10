<?php

# RaceClocker Timing checked
# Copyright G.J.Nieuwenhuis 2025

# 2025-08-03 v0.1 Initial version
# 2025-08-04 v0.2 Decimals added to time values
# 2025-08-05 v0.3 Location added to define Start/Finish
# 2025-08-05 v0.4 Usage/help page added in case required parameters are missing
# 2025-08-06 v0.5 Add further validation checks
# 2025-08-07 v0.6 Added sync status to check if primairy/secondary lists are the same
# 2025-08-07 v0.7 Added error messages
# 2025-08-09 v0.8 Page refresh redone as Jquery and separate php processing for JSON
# 2025-08-14 v0.8.1 Removed URL content actions as they are handled in the JSON data script
# 2025-08-17 v0.8.2 Added execution time in dataquery.html
# 2025-10-20 v0.9 Added support to filter on Block number / changed to relative path for jsondata.php
# 2025-11-08 v0.9.2 Added AllResults in jsondata.php, corrected deviation bug in dataquery.html to not show red when deviation is negative
# 2025-12-10 v0.9.4 Changed jsondata.php to support new API endpoint of RaceClocker

$Version = "v0.9.4";

# Get all required parameters

# We start with true and set to false if any check fails
$ParametersComplete = true;

# Fetch Location, only "Start" or "Finish" is allowed
if (isset($_GET['Location']) && !empty($_GET['Location'])) {
    if ($_GET['Location'] == "Start" || $_GET['Location'] == "Finish") {
       $Location = $_GET['Location'];
    }
} else {
    $ParametersComplete = false;
}

# Fetch the primary URL ID for the results
if (isset($_GET['PrimaryID']) && !empty($_GET['PrimaryID'])) {
   $PrimaryID = $_GET['PrimaryID'];
} else {
    $ParametersComplete = false;
}

# Fetch the secondary URL ID for the results
if (isset($_GET['SecondaryID']) && !empty($_GET['SecondaryID'])) {
   $SecondaryID = $_GET['SecondaryID'];
} else {
    $ParametersComplete = false;
}

# Fetch the refresh rate for the page
if (isset($_GET['Refresh']) && !empty($_GET['Refresh']) && is_numeric($_GET['Refresh'])) {
    $Refresh = $_GET['Refresh'];
} else {
    $ParametersComplete = false;
}

# Fetch the number of results to show
if (isset($_GET['Number']) && !empty($_GET['Number']) && is_numeric($_GET['Number'])) {
   $Number = $_GET['Number'];
} else {
    $ParametersComplete = false;
}

# Fetch the maximum deviation between primary and secondary pulses
if (isset($_GET['MaxDeviation']) && !empty($_GET['MaxDeviation']) && is_numeric($_GET['MaxDeviation'])) {
   $MaxDeviation = $_GET['MaxDeviation'];
} else {
    $ParametersComplete = false;
}

if ($ParametersComplete) {
    
    $JSONURL = "./jsondata.php?Location=".$Location."&PrimaryID=".$PrimaryID."&SecondaryID=".$SecondaryID."&Number=".$Number."&MaxDeviation=".$MaxDeviation."&Refresh=".$Refresh;

    # Add optional filtering parameters to the JSON request
    # Fetch block number for filtering results
    if (isset($_GET['Block']) && !empty($_GET['Block']) && is_numeric($_GET['Block'])) {
        $JSONURL = $JSONURL . "&Block=" . $_GET['Block'];
    }
 
    # jQuery script to fetch and show data
    include_once 'dataquery.html';
} else {
    # Required parameters are missing or wrong, show usage/help page
    include_once 'usage.html';
}

?>

