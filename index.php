<?php

$SITENAME = "Matt's Network Status Site";
$USERPWD = "matt@mattrude.com:master12";
$HTTPHEADER = "ldpz0jdrsx43jcyswlkn037vstg4ena3";
 
// Init cURL
$curl = curl_init();
// Set target URL
curl_setopt($curl, CURLOPT_URL, "https://api.pingdom.com/api/2.0/checks");
// Set the desired HTTP method (GET is default, see the documentation for each request)
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
// Set user (email) and password
curl_setopt($curl, CURLOPT_USERPWD, $USERPWD);
// Add a http header containing the application key (see the Authentication section of this document)
curl_setopt($curl, CURLOPT_HTTPHEADER, array("App-Key: $HTTPHEADER"));
// Ask cURL to return the result as a string
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
 
// Init cURL
$rtime = curl_init();
// Set target URL
curl_setopt($rtime, CURLOPT_URL, "https://api.pingdom.com/api/2.0/servertime");
// Set the desired HTTP method (GET is default, see the documentation for each request)
curl_setopt($rtime, CURLOPT_CUSTOMREQUEST, "GET");
// Set user (email) and password
curl_setopt($rtime, CURLOPT_USERPWD, $USERPWD);
// Add a http header containing the application key (see the Authentication section of this document)
curl_setopt($rtime, CURLOPT_HTTPHEADER, array("App-Key: $HTTPHEADER"));
// Ask cURL to return the result as a string
curl_setopt($rtime, CURLOPT_RETURNTRANSFER, 1);


// Execute the request and decode the json result into an associative array
$response = json_decode(curl_exec($curl),true);
$time = json_decode(curl_exec($rtime),true);
 
// Check for errors returned by the API
if (isset($response['error'])) {
    print "Error: " . $response['error']['errormessage'] . "\n";
    exit;
}
 
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Public Website Health Status for Automattic - Sites</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
    <div id="container">
        <div id="header">
            <h2>$SITENAME</h2>
        <div id="section_curr_status">
            <div class="date floatright" id="psp_last_update">';
                print date("M, j Y H:i:s e", $time['servertime']);echo '
            </div>
            <h2>Current Performance and Availability Status</h2>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="psp-table" id="table_curr_status">
                <tbody>
                    <tr>
                        <td width="28%" class="tablehead" colspan="2">Service / Website</td>
                        <td width="24%" class="tablehead">Current Status</td>
                        <td width="23%" class="tablehead">Current Performance</td>
                        <td width="25%" class="tablehead">Last Error</td>
                    </tr>';
                    // Fetch the list of checks from the response
                    $checksList = $response['checks'];
                    // Print the names and statuses of all checks in the list
                    foreach ($checksList as $check) {
                        print "<tr>";
                        print "<td><img src=\"./images/" . $check['status'] . "-status.png\"></td>";
                        print "<td>" . $check['name'] . "</td>";
                        print "<td>" . $check['status'] . "</td>";
                        print "<td>" . $check['lastresponsetime'] . "ms</td>";
                        if (isset($check['lasterrortime'])) {
                            print "<td>" . date("Y-m-d H:i:s", $check['lasterrortime']) . "</td>";
                        } else {
                            echo "<td></td>";
                        }
                        print "</td>";
                    }
                echo '</tbody>
            </table>
        </div>
    </div>
</body>'; ?>
