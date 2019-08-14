# Overview

PHP Live departure board using Nation Rail Enquires data. Geared to 4k portrait digital signage. 

Links for OpenLDBWS:

https://www.nationalrail.co.uk/100296.aspx
https://wiki.openraildata.com
https://lite.realtime.nationalrail.co.uk/OpenLDBWS/


https://github.com/railalefan/phpOpenLDBWS

## Requires
* OpenLDBWS authorization token: http://realtime.nationalrail.co.uk/OpenLDBWSRegistration/
* php-soap


## Deploy

Put on a webserver and add your OpenLDBWS token to ```conf/token.php```. Remember to block access to ```conf/```.

## Usage

http://\<yourserver\>/index.php?dep=\<CRS\>

where CRS is the station code  http://www.nationalrail.co.uk/stations_destinations/48541.aspx

## Resources

* https://github.com/railalefan/phpOpenLDBWS
* Based on South Western Railway's Live Departure Board.