<?php

# RaceClocker Timing JSON formatter
# Copyright G.J.Nieuwenhuis 2025

# 2025-08-09 v0.1   Initial version
# 2025-08-10 v0.2   Added data for primary/secondary URL check and Lists in Sync
# 2025-08-13 v0.3   Make time difference always positive and present as string for JSON data
# 2025-09-09 v0.3.1 Minor correction for list sync to skip secondary check if there is no value available

$Version = "v0.3.1";

# Create empty JSON array
$JSONData = [];

# Get all required parameters

# We start with true and set to false if any check fails
$ParametersComplete = true;

# Return JSONData script version in JSON data
$JSONMsg = array('JSONVersion' => $Version);
$JSONData[] = $JSONMsg;

# Fetch Location, only "Start" or "Finish" is allowed
if (isset($_GET['Location']) && !empty($_GET['Location'])) {
    if ($_GET['Location'] == "Start" || $_GET['Location'] == "Finish") {
       $Location = $_GET['Location'];
       $JSONMsg = array('Location' => $Location);
       $JSONData[] = $JSONMsg;
    }
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'Location parameter is missing or incorrect');
    $JSONData[] = $ErrorMsg;
}

# Fetch the primary URL ID for the results
if (isset($_GET['PrimaryID']) && !empty($_GET['PrimaryID'])) {
   $PrimaryID = $_GET['PrimaryID'];
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'PrimaryID parameter is missing or incorrect');
    $JSONData[] = $ErrorMsg;

}

# Fetch the secondary URL ID for the results
if (isset($_GET['SecondaryID']) && !empty($_GET['SecondaryID'])) {
   $SecondaryID = $_GET['SecondaryID'];
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'SecondaryID parameter is mssing or incorrect');
    $JSONData[] = $ErrorMsg;

}

# Fetch the number of results to show
if (isset($_GET['Number']) && !empty($_GET['Number']) && is_numeric($_GET['Number'])) {
   $Number = $_GET['Number'];
   $JSONMsg = array('Number' => $Number);
   $JSONData[] = $JSONMsg;
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'Number parameter is missing or incorrect');
    $JSONData[] = $ErrorMsg;

}

# Fetch the maximum deviation between primary and secondary pulses
if (isset($_GET['MaxDeviation']) && !empty($_GET['MaxDeviation']) && is_numeric($_GET['MaxDeviation'])) {
   $MaxDeviation = $_GET['MaxDeviation'];
   $JSONMsg = array('MaxDeviation' => $MaxDeviation);
   $JSONData[] = $JSONMsg;
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'MaxDeviation parameter is missing or incorrect');
    $JSONData[] = $ErrorMsg;

}

# Fetch the refresh rate for the page
if (isset($_GET['Refresh']) && !empty($_GET['Refresh']) && is_numeric($_GET['Refresh'])) {
    $Refresh = $_GET['Refresh'];
    $JSONMsg = array('Refresh' => $Refresh);
    $JSONData[] = $JSONMsg;
} else {
    $ParametersComplete = false;
    $ErrorMsg = array('Error' => 'Refresh parameter is missing or incorrect');
    $JSONData[] = $ErrorMsg;
}

# Proceed if we have all required parameters
if ($ParametersComplete) {

    # Define the primary and secondary URLs to be used
    $PrimaryURL = "https://raceclocker.com/".$PrimaryID."?json=1";
    $SecondaryURL = "https://raceclocker.com/".$SecondaryID."?json=1";

    # Fetch URL contents to scrape the race titles
    $TimesPrimary = file_get_contents($PrimaryURL);
    $TimesSecondary =file_get_contents($SecondaryURL);
    # Set URL check flags
    $PrimaryURLCheck = true;
    $SecondaryURLCheck = true;

    # Fetch the RaceClocker names for the primary race from the URL content
    preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)["\']\s*\/?>/i', $TimesPrimary, $TitlePrimaryMatches);
    if (isset($TitlePrimaryMatches[1])) {
        $TitleData= array('PrimaryTitle' => $TitlePrimaryMatches[1]);
    } else {
        $TitleData= array('PrimaryTitle' => 'Not found');
    }
    $JSONData[] = $TitleData;

    # Fetch the RaceClocker names for the secondary race from the URL content
    preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)["\']\s*\/?>/i', $TimesSecondary, $TitleSecondaryMatches);
    if (isset($TitleSecondaryMatches[1])) {
        $TitleData= array('SecondaryTitle' => $TitleSecondaryMatches[1]);
    } else {
        $TitleData= array('SecondaryTitle' => 'Not found');
    }
    $JSONData[] = $TitleData;

     
    # Fetch the primary times
    if (preg_match('/let\s+AllResults\s*=\s*(.*);/', $TimesPrimary, $matches)) {
        
        # Decode the JSON results from the html
        $jsonraw = $matches[1];
        $json = json_decode($jsonraw,true);
        
        if (is_array($json)) {
            # Define empty arrays
            $BibPrimary = [];
            $BibPrimaryUnsorted = [];
            $NamePrimary = [];
            $TimePrimary = [];
            $CatPrimary = [];

            # Fill Bib,Name, Time as separate arrays 
            foreach($json as $item) {
                $BibPrimary[] = $item['Bib'];
                $BibPrimaryUnsorted[] = $item['Bib'];
                $NamePrimary[] = $item['Name'];
                $CatPrimary[] = $item['Cat'];

                # Fetch start times and combine with the decimal values to 00:00:00.0 format
                if ($Location == "Start") {
                    $TimePrimary[] = $item['TmSplit1'].".".$item['TmSplit1dc'];
                } else {
                    $TimePrimary[] = $item['TmSplit5'].".".$item['TmSplit5dc'];
                }
            }

            # Sort the arrays with the last time first
            array_multisort($TimePrimary, SORT_DESC,$BibPrimary,$NamePrimary);

            $StatusMsg = array('PrimaryURLStatus' => 'OK');
            $JSONData[] = $StatusMsg;
        } else {
            $PrimaryURLCheck = false;
        }
    } else {
        $PrimaryURLCheck = false;
    }


    # Fetch the secondary times
    if (preg_match('/let\s+AllResults\s*=\s*(.*);/', $TimesSecondary, $matches)) {

        # Decode the JSON results from the html
        $jsonraw = $matches[1];
        $json = json_decode($jsonraw,true);
        
        if (is_array($json)) {

            # Define empty arrays
            $BibSecondary = [];
            $NameSecondary = [];
            $TimeSecondary = [];
            $CatSecondary = [];

            # Fill Bib,Name, Time as separate arrays
            foreach($json as $item) {
                $BibSecondary[] = $item['Bib'];
                $NameSecondary[] = $item['Name'];
                $CatSecondary[] = $item['Cat'];

                # Fetch start times and combine with the decimal values to 00:00:00.0 format
                if ($Location == "Start") {
                    $TimeSecondary[] = $item['TmSplit1'].".".$item['TmSplit1dc'];
                } else {
                    $TimeSecondary[] = $item['TmSplit5'].".".$item['TmSplit5dc'];
                }
            }
            $StatusMsg = array('SecondaryURLStatus' => 'OK');
            $JSONData[] = $StatusMsg;
        } else {
            $SecondaryURLCheck = false;
        }
    } else {
        $SecondaryURLCheck = false;
    }


    # If the request number of results is higher than the actual results, match it
    if ($PrimaryURLCheck) {
        if ($Number > count($BibPrimary)) {
            $Number = count($BibPrimary);
        }
    }


    # Check if Primary and Secondary lists are in sync
    $ListsInSyncCountCheck = true;
    $ListsInSyncBibCheck = true;
    $ListsInSyncCatCheck = true;

    if ($PrimaryURLCheck && $SecondaryURLCheck) {
        $ListsInSync = true;
        If (count($BibPrimary) != count($BibSecondary)) {
            $ListsInSync = false;
            $ListsInSyncCountCheck = false;
        }
        for ($Counter = 0; $Counter < count($BibPrimary); $Counter++) {
            if (isset($BibSecondary[$Counter])) {
                if ($BibPrimaryUnsorted[$Counter] != $BibSecondary[$Counter]) {
                    $ListsInSync = false;
                    $ListsInSyncBibCheck = false;
                }
            }
            if (isset($CatSecondary[$Counter])) {
                if ($CatPrimary[$Counter] != $CatSecondary[$Counter]) {
                    $ListsInSync = false;
                    $ListsInSyncCatCheck = false;
                }
            }
        }
    }

    # Report warnings
    if (!$PrimaryURLCheck || !$SecondaryURLCheck || !$ListsInSync) {

        # Add warning messages
        if (!$PrimaryURLCheck) {
            $ErrorMsg = array('Error' => 'Unable to fetch results for primary URL');
            $JSONData[] = $ErrorMsg;
            $StatusMsg = array('PrimaryURLStatus' => 'Failed');
            $JSONData[] = $StatusMsg;
        }
        if (!$SecondaryURLCheck) {
            $ErrorMsg = array('Error' => 'Unable to fetch results for secondary URL');
            $JSONData[] = $ErrorMsg;
            $StatusMsg = array('SecondaryURLStatus' => 'Failed');
            $JSONData[] = $StatusMsg;
        }
        if (!$ListsInSyncCountCheck) {
            $ErrorMsg = array('Error' => 'There is a difference between the number of entries for the primary and secondary lists');
            $JSONData[] = $ErrorMsg;
            $StatusMsg = array('ListsInSyncCountStatus' => 'Failed');
            $JSONData[] = $StatusMsg;
        }
        if (!$ListsInSyncBibCheck) {
            $ErrorMsg = array('Error' => 'There is a difference between the Bib numbers for the primary and secondary lists');
            $JSONData[] = $ErrorMsg;
            $StatusMsg = array('ListsInSyncBibStatus' => 'Failed');
            $JSONData[] = $StatusMsg;
        }
        if (!$ListsInSyncCatCheck) {
            $ErrorMsg = array('Error' => 'There is a difference between the Category names for the primary and secondary lists');
            $JSONData[] = $ErrorMsg;
            $StatusMsg = array('ListsInSyncCategoryStatus' => 'Failed');
            $JSONData[] = $StatusMsg;
        }

    }

    # Check for main list validation, create error if there is a mismatched check (Count/Bib/Category)
    if ($ListsInSync) {
        $StatusMsg = array('ListsInSyncStatus' => 'OK');
        $JSONData[] = $StatusMsg;
    } else {
        $StatusMsg = array('ListsInSyncStatus' => 'Failed');
        $JSONData[] = $StatusMsg;        
    }

    # Are all checks valid? Generate JSON output
    if ($PrimaryURLCheck && $SecondaryURLCheck && $ListsInSync) {
          
        for ($Counter = 0; $Counter < $Number; $Counter++) {
            # Find the matching secondary time
            $SearchTime = array_search($BibPrimary[$Counter],$BibSecondary);
            
            $PrimaryTimeSplit = explode(":",$TimePrimary[$Counter]);
            $SecondaryTimeSplit = explode(":",$TimeSecondary[$SearchTime]);
            $TimeDifference = strval(abs(round(((($PrimaryTimeSplit[0] * 3600) + ($PrimaryTimeSplit[1] * 60) + $PrimaryTimeSplit[2]) - (($SecondaryTimeSplit[0] * 3600) + ($SecondaryTimeSplit[1] * 60) + $SecondaryTimeSplit[2])),1)));

            # Add data to JSON array
            $NewData = ['Results' => ['Bib' => $BibPrimary[$Counter], 'Name' => $NamePrimary[$Counter], 'Primary' => $TimePrimary[$Counter], 'Secondary' => $TimeSecondary[$SearchTime], 'Deviation' => $TimeDifference]]; 
            $JSONData[] = $NewData;

        }
    }

} else {
    $ErrorMsg = array('Error' => 'Required parameters are missing');
    $JSONData[] = $ErrorMsg;
}

# Output JSON data to screen
header('Content-Type: application/json; charset=utf-8');
echo json_encode($JSONData);


?>

