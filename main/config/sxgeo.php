<?php

//todo - put update on cron. Should be updated once per 2 weeks. php artisan sxgeo:update
return [
    // URL of countries or cities database file to download and update.
    // See https://sypexgeo.net/ru/download/.
    'dbFileURL' => 'https://sypexgeo.net/files/SxGeoCity_utf8.zip',

    // The path where the database will be stored.
    'localPath' => database_path('GeoIP.dat'),
];
