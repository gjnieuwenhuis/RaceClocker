<?php

# RaceClocker Timing checked
# Copyright G.J.Nieuwenhuis 2025

# 2025-08-03 Versie 0.1 Initial version
# 2025-08-04 Versie 0.2 Decimals added to time values
# 2025-08-05 Versie 0.3 Location added to define Start/Finish
# 2025-08-05 Versie 0.4 Usage/help page added in case required parameters are missing
# 2025-08-06 Versie 0.5 Add further validation checks 

$Version = "0.5";

#setlocale(LC_NUMERIC, 'C');

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

    # Set the auto refresh rate of the page
    if ($Refresh > 0) {
        header("Refresh: $Refresh");
    }

    # Define the primary and secondary URLs to be used
    $PrimaryURL = "https://raceclocker.com/".$PrimaryID."?json=1";
    $SecondaryURL = "https://raceclocker.com/".$SecondaryID."?json=1";

    $TimesPrimary = file_get_contents($PrimaryURL);
    $TimesSecondary =file_get_contents($SecondaryURL);

    # Fetch the RaceClocker names for the primary race
    preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)["\']\s*\/?>/i', $TimesPrimary, $TitlePrimaryMatches);
    if (isset($TitlePrimaryMatches[1])) {
        $TitlePrimary = $TitlePrimaryMatches[1]; 
    } else {
        $TitlePrimary = "";
    }
    # Fetch the RaceClocker names for the secondary race
    preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']+)["\']\s*\/?>/i', $TimesSecondary, $TitleSecondaryMatches);
    if (isset($TitleSecondaryMatches[1])) {
        $TitleSecondary = $TitleSecondaryMatches[1]; 
    } else {
        $TitleSecondary = "";
    }
     

    # Fetch the primary times
    if (preg_match('/let\s+AllResults\s*=\s*(.*);/', $TimesPrimary, $matches)) {
        
        # Decode the JSON results from the html
        $jsonraw = $matches[1];
        $json = json_decode($jsonraw,true);
        
        if (is_array($json)) {
            # Define empty arrays
            $BibPrimary = [];
            $NamePrimary = [];
            $TimePrimary = [];

            # Fill Bib,Name, Time as separate arrays 
            foreach($json as $item) {
                $BibPrimary[] = $item['Bib'];
                $NamePrimary[] = $item['Name'];
                # Fetch start times and combine with the decimal values to 00:00:00.0 format
                if ($Location == "Start") {
                    $TimePrimary[] = $item['TmSplit1'].".".$item['TmSplit1dc'];
                } else {
                    $TimePrimary[] = $item['TmSplit5'].".".$item['TmSplit5dc'];
                }
            }

            # Sort the arrays with the last time first
            array_multisort($TimePrimary, SORT_DESC,$BibPrimary,$NamePrimary);
        } else {
            echo "Unable to fetch primary times, check the PrimaryID!";
            exit;
        }
    } else {
        echo "Unable to fetch primary times, check the PrimaryID!";
        exit;
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

            # Fill Bib,Name, Time as separate arrays
            foreach($json as $item) {
                $BibSecondary[] = $item['Bib'];
                $NameSecondary[] = $item['Name'];
                if ($Location == "Start") {
                    $TimeSecondary[] = $item['TmSplit1'].".".$item['TmSplit1dc'];
                } else {
                    $TimeSecondary[] = $item['TmSplit5'].".".$item['TmSplit5dc'];
                }
            }
        } else {
            echo "Unable to fetch secondary times, check the SecondaryID!";
            exit;
        }
    } else {
        echo "Unable to fetch secondary times, check the SecondaryID!";
        exit;
    }


    # If the request number of results is higher than the actual results, match it
    if ($Number > count($BibPrimary)) {
        $Number = count($BibPrimary);
    }


    # Show header
    include_once 'header.html';


    # Show content
    echo "<body class='bg-light d-flex align-items-center' style='height: 100;'>\n";
      echo "<div class='container'>\n";
        echo "<div class='row justify-content-center'>\n";
          echo "<div class='col-md-10 col-lg-8'>\n";
            echo "<div class='card shadow-lg rounded-4'>\n";
              echo "<div class='card-body'>\n";
                echo "<h3 class='card-title text-center mb-4'>".$TitlePrimary." / ".$TitleSecondary." - ".$Location."</h3>\n";
                echo "<div class='table-responsive'>\n";
                  echo "<table class='table table-bordered table-striped text-center align-middle'>\n";
                    echo "<thead class='table-dark'>\n";
                      echo "<tr>\n";
                        echo "<th>#</th>\n";
                        echo "<th>Name</th>\n";
                        echo "<th>Pimary</th>\n";
                        echo "<th>Secundary</th>\n";
                        echo "<th>Deviation</th>\n";
                      echo "</tr>\n";
                    echo "</thead>\n";
                    echo "<tbody>\n";

    for ($Counter = 0; $Counter < $Number; $Counter++) {
        # Find the matching secondary time
        $SearchTime = array_search($BibPrimary[$Counter],$BibSecondary);
        
        $PrimaryTimeSplit = explode(":",$TimePrimary[$Counter]);
        $SecondaryTimeSplit = explode(":",$TimeSecondary[$SearchTime]);
        $TimeDifference = round(((($PrimaryTimeSplit[0] * 3600) + ($PrimaryTimeSplit[1] * 60) + $PrimaryTimeSplit[2]) - (($SecondaryTimeSplit[0] * 3600) + ($SecondaryTimeSplit[1] * 60) + $SecondaryTimeSplit[2])),1);

        if (($MaxDeviation > 0) && ($TimePrimary[$Counter] != "00:00:00.0") && ($TimeSecondary[$SearchTime] != "00:00:00.0")) {
            if (abs($TimeDifference) > $MaxDeviation) {    
              echo "<tr class='table-danger'>\n";
            } else {
              echo "<tr class='table-success'>\n";
            }
        } else {
            echo "<tr>\n";
        }

            echo "<td>".$BibPrimary[$Counter]."</td>\n";
            echo "<td>".$NamePrimary[$Counter]."</td>\n";
            echo "<td>".$TimePrimary[$Counter]."</td>\n";
            echo "<td>".$TimeSecondary[$SearchTime]."</td>\n";
            echo "<td>".$TimeDifference."</td>\n";
          echo "</tr>\n";
    }

                    echo "</tbody>\n";
                  echo "</table>\n";
                echo "</div>\n";
              echo "</div>\n";
              echo "<div class='card-footer text-muted'>\n";
                  echo "RaceClocker Timing Check v".$Version;
              echo "</div>\n";
            echo "</div>\n";
          echo "</div>\n";
        echo "</div>\n";
      echo "</div>\n";

      echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'></script>\n";
    echo "</body>\n";
    echo "</html>\n";
} else {

    # Required parameters are missing or wrong, show usage page
    include_once 'help.html';
}

?>

