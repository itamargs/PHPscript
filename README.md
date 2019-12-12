This Php script generates Html address book with contacts cards and photos from csv file
---

USAGE: php AddressBookGenerator.php [csvFile].csv

Execution:
* Converts csv into multi-dimensional Array
* Generates index.html and personCard.php to handle links from index.html and process their data
* Send this data to the index.html file
* Show data in Html
* Saves persons details data hiding in the html url link of each person
* Links refer to the personCard.php which also gets the data hiding in the url and process it 

Html creation:
1. Generates index.html
2. Save html content as string
3. Puts string in index.html

PHP person card generated the same as HTML

Restriction for the CSV:

* Should include 6 fields: 'first name','last name','phone number','street','city','zip code'
* Should have details of at least 1 person
