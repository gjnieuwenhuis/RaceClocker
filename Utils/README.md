### RaceClocker to JSON script
#### Usage
This script is intended for people who want proper formatted JSON data when fetching RaceClocker results data.
At this moment (November 2025) the results from RaceClocker when adding the ?json=1 parameter to the RaceClocker results URL will return html formatted JSON output.

Call script with rc2json.php?RaceClockerID=ResultsID (replace ResultsID with the actual number as found in the results URL of the race)

#### JSON structure
The data returned will be the same JSON data as found in the AllResults variable from the results page and will also add the title of the race and if the URL returned valid data.

JSONVersion => Returns the version of the script

Title => Returns the title of the race

RaceClockerURLStatus => Returns "OK" or "Failed" depending if the results ID provided returned valid results

Results => The actual timing data

Error => Returns any errors found


#### Data example
Example JSON (truncated)


[
  {
    "JSONVersion": "v0.1"
  },
  {
    "Title": "Tromp Boat Races (40e editie)"
  },
  {
    "RaceClockerURLStatus": "OK"
  },
  [
    {
      "RaceID": "1424895",
      "Rank": "1",
      "Name": "Asser Roeiclub",
      "Bib": "1",
      "Club": "Asser Roeiclub",
      "CategoryID": "194972",
      "Cat": "MixO 2x",
      "CatRank": "1",
      "WaveID": 0,
      "WaveName": "None",
      "WaveRank": 1,
      "Age": "0",
      "GenderID": 1,
      "Gender": "",
      "Custom": "",
      "Handicap": "1.000",
      "ExtraInfo": [
        [
          "Blok",
          "Blok 1 - Za 9:30"
        ],
        [
          "Slag",
          "Liesbeth van den Eerenbeemt"
        ],
        [
          "Boeg",
          "Bas Nieuwenhuis"
        ],
        [
          "PloegID",
          "806576"
        ]
      ],
      "TmSplit1": "09:38:36",
      "TmSplit1dc": "5",
      "TmSplit1Sec": "",
      "TmSplit2": "00:00:00",
      "TmSplit2dc": "0",
      "TmSplit2Sec": "",
      "TmSplit3": "00:00:00",
      "TmSplit3dc": "0",
      "TmSplit3Sec": "",
      "TmSplit4": "00:00:00",
      "TmSplit4dc": "0",
      "TmSplit4Sec": "",
      "TmSplit5": "10:01:36",
      "TmSplit5dc": "7",
      "TmSplit5Sec": "",
      "TmSplit6": "00:00:00",
      "TmSplit6dc": "0",
      "TmSplit6Sec": "",
      "Result": "00:23:00.2",
      "TmResultSec": "1380.2",
      "Penalty": "0",
      "PenaltyNote": "",
      "TmHandicapSec": "",
      "Laps": []
    },
    {
      "RaceID": "1424896",
      "Rank": "2",
      "Name": "Honte",
      "Bib": "2",
      "Club": "Honte",
      "CategoryID": "194972",
      "Cat": "MixO 2x",
      "CatRank": "1",
      "WaveID": 0,
      "WaveName": "None",
      "WaveRank": 1,
      "Age": "0",
      "GenderID": 1,
      "Gender": "",
      "Custom": "",
      "Handicap": "1.000",
      "ExtraInfo": [
        [
          "Blok",
          "Blok 1 - Za 9:30"
        ],
        [
          "Slag",
          "Collin Bohncke"
        ],
        [
          "Boeg",
          "Tessa van Hateren"
        ],
        [
          "PloegID",
          "806423"
        ]
      ],
      "TmSplit1": "09:38:53",
      "TmSplit1dc": "2",
      "TmSplit1Sec": "",
      "TmSplit2": "00:00:00",
      "TmSplit2dc": "0",
      "TmSplit2Sec": "",
      "TmSplit3": "00:00:00",
      "TmSplit3dc": "0",
      "TmSplit3Sec": "",
      "TmSplit4": "00:00:00",
      "TmSplit4dc": "0",
      "TmSplit4Sec": "",
      "TmSplit5": "10:00:30",
      "TmSplit5dc": "4",
      "TmSplit5Sec": "",
      "TmSplit6": "00:00:00",
      "TmSplit6dc": "0",
      "TmSplit6Sec": "",
      "Result": "00:21:37.2",
      "TmResultSec": "1297.2",
      "Penalty": "0",
      "PenaltyNote": "",
      "TmHandicapSec": "",
      "Laps": []
    },
