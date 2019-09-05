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
Echo "<html>";
Echo
    "<p>Person card:</p>".
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

//------------------   </Functions>  ----------------------

//creating array from the csv file
$csvArray = array_map('str_getcsv', file('addressBook.csv'));
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
        if($j == 0){ //then we in the 'first name' value and need to hyper link it
            //Attaching the person data to send it later combined with HTML link into our "person card"
            $words = '<a href=" ./personCard.php?'. "first=".$rowHolder[0]."&last=".$rowHolder[1]."&phone="
            .$rowHolder[2]."&street=".$rowHolder[3]."&city=".$rowHolder[4]."&zip=".$rowHolder[5].'"'. ' target="_blank"'.">".$rowHolder[$j] ."</a>" . " | ";
        }
        else { // attach the none 'first name' values to show later in the html
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




