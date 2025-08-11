# RaceClocker
PHP scripts for RaceClocker (https://raceclocker.com) in the event where on the Start/Finish primary and backup times are being used and either the timing crew would like to quickly compare the time difference.

**Installation**

Simply copy all 4 files to the PHP webserver of your choice or clone this repository

**Usage**

Running the script without any parameters in the URL will provide you with the help page usage.html
index.php is an exmaple script which handles the parameter validation and pushes the parameters to dataquery.html
dataquery.html will use jsondata.php to fetch both the primary and secondary race results

A typical example would be: https://yourwebsite.com/index.php?Location=Start&PrimaryID=b4e0c99a&SecondaryID=b4e0c99b&Refresh=10&MaxDeviation=0.5&Number=10

If you would like to interpret the JSON data yourself, simply call the jsondata.php direcly with the same parameters
https://yourwebsite.com/jsondata.php?Location=Start&PrimaryID=b4e0c99a&SecondaryID=b4e0c99b&Refresh=10&MaxDeviation=0.5&Number=10
