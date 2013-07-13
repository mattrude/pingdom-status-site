<?php

$SITEID = "mattrude";
$SITENAME = "Matt's Network Status Site";
$USERPWD = "matt@mattrude.com:master12";
$HTTPHEADER = "ldpz0jdrsx43jcyswlkn037vstg4ena3";
 
$apc_response = apc_fetch("$SITEID-response");
if ($apc_response === false) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.pingdom.com/api/2.0/checks");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_USERPWD, $USERPWD);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("App-Key: $HTTPHEADER"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = json_decode(curl_exec($curl),true);
    apc_store("$SITEID-response", $response, 60);
} else {
    $response = $apc_response;
} 

$apc_time = apc_fetch("$SITEID-time");
if ($apc_time === false) {
    $rtime = curl_init();
    curl_setopt($rtime, CURLOPT_URL, "https://api.pingdom.com/api/2.0/servertime");
    curl_setopt($rtime, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($rtime, CURLOPT_USERPWD, $USERPWD);
    curl_setopt($rtime, CURLOPT_HTTPHEADER, array("App-Key: $HTTPHEADER"));
    curl_setopt($rtime, CURLOPT_RETURNTRANSFER, 1);
    $time = json_decode(curl_exec($rtime),true);
    apc_store("$SITEID-time", $time, 60);
} else {
    $time = $apc_time;
} 
 
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
            <div id="head_left" class="wraptocenter"><div><h2>';
            echo $SITENAME;
            echo '</h2></div></div>
        </div>
        <div id="section_curr_status">
            <div class="date floatright" id="psp_last_update">';
                print date("M, j Y H:i:s T", $time['servertime']);echo '
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
                        print "<td><img title=\"Last Checked: " . date("M, j Y H:i:s T", $check['lasttesttime']) . ", Checked Every: " . $check['resolution'] . " minute(s)\" src=\"./images/" . $check['status'] . "-status.png\"></td>";
                        print "<td><span title=\"" . $check['hostname'] . "\">" . $check['name'] . "</span></td>";
                        print "<td>" . $check['status'] . "</td>";
                        if ($check['lastresponsetime'] >= 1000) {
                            $lastresponsetime = $check['lastresponsetime'] / 1000;
                            print "<td>" . $lastresponsetime . " s</td>";
                        } else {
                            print "<td>" . $check['lastresponsetime'] . " ms</td>";
                        }
                        if (isset($check['lasterrortime'])) {
                            print "<td><span title=\"" . date_diff(date($check['lasterrortime']), date($time['servertime'])) . "\">" . date("Y-m-d H:i:s", $check['lasterrortime']) . "</span></td>";
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
