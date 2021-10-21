# LGMV-dashboard-server

This is the LGMV Dashboard Server repository for Smart Appliance Project.
The `upload.html` will show the upload report page, and prepared to receive LGMV report in html format.
While the `upload.php` handles the parsing of the html report, construct the json, and upload process to the DB.

Main address `vps1.derrylab.com/lgmv`
CouchDB server address `vps1.derrylab.com:5984`

Requirements:
`sudo apt-get install php php-xml php-dom php-curl couchdb`

Then bind CouchDB `chttpd` to `0.0.0.0` to enable access from outside.