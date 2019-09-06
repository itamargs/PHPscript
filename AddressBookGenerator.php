<?php

//-----------------  Functions --------------------------

// Generates html template file and put variable as 'body'
function generateHtml($content)
{
    $htmlFile = "index.html"; // generated html file name
    $f = fopen($htmlFile, 'w');

// the html code injected to the html file

    $htmlData = "
<!doctype html>
<html>
<head><meta charset='utf-8'>
<title>Address book</title>
</head>
<body>
$content 
</body>
</html>
";
    fwrite($f, $htmlData);
    fclose($f);
}

//Generates the php for 'person card'
// Handles data of single person from the html
function generatePersonCard()
{
    $personFile = "personCard.php"; // generated html file name
    $f = fopen($personFile, 'w');

// The html code injected to the html file

    $personCardContent = '
<?php
// Get data about persons from the html link
    if(isset($_GET["first"]) && isset($_GET["last"]) && isset($_GET["phone"])
        && isset($_GET["street"]) && isset($_GET["city"]) && isset($_GET["zip"]))
    {
        $first = $_GET["first"];
        $last = $_GET["last"];
        $phone = $_GET["phone"];
        $street = $_GET["street"];
        $city = $_GET["city"];
        $zip = $_GET["zip"];
    }
    
// Get person image from google potso search
// A bit tricky since google Search photo API via json deprecated since 2011 and didnt want to use API key here
// Mehod: search with keywords-> get html content -> use REGEX to get the specific image link appropriate 
    $search_keyword = $first." ".$last; //Key word for searching photo
$search_keyword=str_replace(\' \',\'+\',$search_keyword);
$url = "https://www.google.com/search?q=".$search_keyword."&tbm=isch";
//get string with html data of the content of requested keywords
$input = @file_get_contents($url) or die("Could not access file: $url");
//build REGEX to get only the link to the first photo (best matching) encrypted (small Thumbnail sized photo)
$regexp = \'"https://encrypted-tbn0.gstatic.com(.*?)"\';
if (preg_match_all("~".$regexp."~", $input, $result)) {
    $matches = $result[0];
    $photoUrl =  $matches[0];
} else
    print "nothing found";
echo \'
<!DOCTYPE html>
<html>
<body>
<img src=\'.$photoUrl. \' alt="Trulli" width="150" height="130">
</body>
</html>
    \';
    
Echo "<html>";
Echo
    "<p> First Name: " . $first. "</p>".
    "<p> Last Name: " . $last. "</p>".
    "<p> phone number: " . $phone. "</p>".
    "<p> street: " . $street. "</p>".
    "<p> city: " . $city. "</p>".
    "<p> zip code: " . $zip. "</p>"
;
';
    fwrite($f, $personCardContent);
    fclose($f);
}

// Collect warnings and threw errors
set_error_handler(
    function ($severity, $message, $file, $line) {
        throw new ErrorException($message, $severity, $severity, $file, $line);
    }
);

//-----------------------   </Functions>  ----------------------

//todo: check path other then in same folder as php
// ------------------------- Running from HERE ---------------------------
//check if CSV file passed as first argument into script
if ($argc == 2) {
    $file_parts = pathinfo($argv[1]);
    if ($file_parts['extension'] != "csv") {
        echo "Accepting only csv files \n";
        echo "EXIT \n";
        exit();
    }
} else {
    echo "USAGE: php -f AddressBookGenerator.php filename.csv \n";
    echo "EXIT \n";
    exit();
}
echo "Input is OK.\nRUNNING...\n";


//creating array from the csv file

try {
    $csvArray = array_map('str_getcsv', file($argv[1]));
}
catch (Exception $e) {
    echo "ERROR: CSV FIle not found or in wrong format \n";
    echo "EXIT";
    exit();
}
restore_error_handler();
$firstLineArray = $csvArray[0]; //save the first row in the array


// Extracting the first line of the csv (Keys line)
$firstLine = "";
foreach ($csvArray[0] as &$word) {
    $firstLine .= $word .= " | ";
}
$firstLine = "<p>" . $firstLine . "</p>" . "\n"; //attaching html tags for later use


$words = ""; //hold and attaches each word from a row in csv
$rows = ""; //hold and attaches rows from the csv


//removing the first line of the so we can manipulate the rest without interruption
array_shift($csvArray);


//sorting the array from the csv file by 'last name'
foreach ($csvArray as $key => $value) {
    //build array with only the column for sorting (here its "last name" column in cell '[1]')
    $lastNameArr[$key] = $value[1];
}
//sorts array based on that 'last name' column
array_multisort($lastNameArr, SORT_ASC, $csvArray);


for ($i = 0; $i < count($csvArray); $i++) { //iterates rows
    $rowHolder = $csvArray[$i];
    $words = ""; // resets after each row to collect the words from the next row
    for ($j = 0; $j < count($rowHolder); $j++) { //iterates words in each row
        if ($j == 0) { //then we in the 'first name' value and need to hyper link it
            //Attaching the person data to send it later combined with HTML link into our "person card"
            $words = '<a href=" ./personCard.php?' . "first=" . $rowHolder[0] . "&last=" . $rowHolder[1] . "&phone="
                . $rowHolder[2] . "&street=" . $rowHolder[3] . "&city=" . $rowHolder[4] . "&zip=" . $rowHolder[5] . '"'
                . ' target="_blank"' . ">" . $rowHolder[$j] . "</a>" . " | ";
        } else { // attach the none 'first name' values to show later in the html
            $words = $words . $rowHolder[$j] . ' | '; //building the rows
        }
    }
    $rows = $rows . "<p>" . $words . "</p>" . "\n"; //attach html tags for each row

}

//echo $firstLine;
//echo $rows;

//insert all the data we created before into single string
$htmlContent =
    $firstLine
    . $rows;

// injecting to the 'body' inside the html file and generates it
generateHtml($htmlContent);
// injecting content of 'person card' php page and generates it
generatePersonCard();

echo "Complete\n";




