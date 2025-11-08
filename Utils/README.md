### RaceClocker to JSON script
## Usage
This script is intended for people who want proper formatted JSON data when fetching RaceClocker results data.
At this moment (November 2025) the results from RaceClocker when adding the ?json=1 parameter to the RaceClocker results URL will return html formatted JSON output.

Call script with rc2json.php?RaceClockerID=<ResultsID> (replace <ResultsID> with the actual number as found in the results URL of the race.
The data returned will be the same JSON data as found in the AllResults variable from the results page.

