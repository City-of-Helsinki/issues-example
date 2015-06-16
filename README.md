Helsinki issue reporting API example
=============


This repository contains code examples and demo which use Helsinki issue reporting API. The API is defined in http://dev.hel.fi/apis/issuereporting. The repo contains following demos and test environment:

- Mapviever (mapviever.html) which is running at ttp://dev.hel.fi/open311-test/mapviewer.html. The example web page shows service requests on a map and it is possible test API calls. Service requests are fetched from Helsinki (Open311) issue reporting endpoint https://asiointi.hel.fi/palautews/rest/v1/requests.json.
- PHP example for posting service request (post_example.php)
- Swagger json files (/swagger/api-docs)
- test environment for app developers (/v1). The base URL for the test environment is http://dev.hel.fi/open311-test/v1/ and api key f1301b1ded935eabc5faa6a2ce975f6.  


Code uses PHP and javascript and its libraries like leaflet. 

