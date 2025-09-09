<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Service account key
    |--------------------------------------------------------------------------
    |
    | This key is used for authentication and authorization to access the Google Sheets API.
    | https://console.developers.google.com/cloud-resource-manager
    */

    'credentials' => env('GOOGLE_SHEETS_CREDENTIALS'),
    'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
];
