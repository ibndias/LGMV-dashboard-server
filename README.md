# LGMV-dashboard-server

This is the LGMV Dashboard Server repository for Smart Appliance Project.
The `upload.html` will show the upload report page, and prepared to receive LGMV report in html format.
While the `upload.php` handles the parsing of the html report, construct the json, and upload process to the DB.

CouchDB server address `pc.derrylab.com:5984`

Requirements:
sudo apt-get install php
sudo apt-get install php-xml
sudo apt-get install php-dom
sudo apt-get install php-curl
sudo apt-get install couchdb #then bind chttpd to 0.0.0.0