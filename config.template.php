<?php

define('APIBASEURL','https://outlook.office.com'); # Original base URL of your Webhook links

# DEBUG Options - don't activate in production environment
define('DEBUG',false); # Save all (!) inputs in JSONDIR and enable DEBUG Actions for unknown messages.
define('JSONDIR','/tmp/'); # Writeable path for json (debug) files - for easy debugging put it below the document root and set JSONURL as well
#define('JSONURL','https://www.example.com/json'); # URL to the directory JSONDIR. Remember to not activate auto index on this. Don't define if json files should not be linked
