<?php
include_once "OpenLDBWS.php";
include_once "conf/token.php";
$SERVICES_MAX = 14;
$TIME_OFFSET = 10; // min
$TIME_WINDOW = 10; // min
$OpenLDBWS = new OpenLDBWS($LDBWS_TOKEN);
$dep = isset($_REQUEST["dep"]) ? $_REQUEST["dep"] : "MAN";

// preload some board info
$result = $OpenLDBWS->GetDepBoardWithDetails(1, $dep);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="gr__realtime_nationalrail_co_uk">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="refresh" content="120">
    <title><?php $result->GetStationBoardResult->locationName;?></title>
    <script type="text/javascript" src="./Scroller.js"></script>
    <link href="./4k-portrait.css" rel="stylesheet" type="text/css">
</head>

<body onload="Scroller();" data-gr-c-s-loaded="true">
    <div class="heading" id="stationHeader">
        <table cellpadding="0" cellspacing="0" id="tableHeader">
            <tbody>
                <tr>
                    <td class="col1">
                    </td>
                    <td class="col2" id="colHeaderText">
                        <?php echo $result->GetStationBoardResult->locationName; ?>
                    </td>
                    <td class="col3"><img src="./images/poweredbynretruecolour.gif"/>
                    </td>
                </tr>
            </tbody>
            </table>
    </div>

    <div class="contents" id="trainServices">

        <table class="depboard" cellpadding="0" cellspacing="0" id="depboardTableHeader">
            <tbody>
                <tr id="depboardHeader">
                    <th>Destination</th>
                    <th class="left_col plat_col">Plat.</th>
                    <th class="left_col sched_col">Departs</th>
                    <th class="left_col exp_col">Expected</th>
                </tr>

<?php
$dep = isset($_REQUEST["dep"]) ? $_REQUEST["dep"] : "MAN";

$page = true;
$offset = $TIME_OFFSET;
$s = 0;
while ($s < $SERVICES_MAX && $offset < $SERVICES_MAX*$TIME_WINDOW) {
    $result = $OpenLDBWS->GetDepBoardWithDetails(0, $dep, "", "", $offset, $TIME_WINDOW);
    $board = $result->GetStationBoardResult;
    
    foreach ($board->trainServices->service as $service) {
        $destination = $service->destination->location->locationName;
        $platform = isset($service->platform) ? $service->platform : "";
        $std = $service->std;
        $etd = $service->etd;

        // check for cancelled delays
        $status = "";
        $status_reason = "";
        $calling_at = "Calling at:";

        if ($etd == "Cancelled") {
            $status = "cancelled";
            $status_reason = "<div class=\"canc_reason\">$service->cancelReason</div>";
            $calling_at = "This was the train calling at:";
        } elseif ($etd == "Delayed") {
            $status = "delayed";
            $status_reason = "<div class=\"delay_reason\">$service->delayReason</div>";
        } elseif ($etd != "On time") {
            $status = "forecast_late";
        }

        echo <<<HTML
                <tr id="trainStation${s}" class="altrow ${status}">
                    <td class="no_border">${destination}</td>
                    <td class="left_col plat_col no_border platform">${platform}</td>
                    <td class="left_col sched_col no_border">${std}</td>
                    <td class="left_col exp_col no_border ${status}">${etd}</td>
                </tr>
                <tr id="callingPoints${s}" class="altrow ${status}">
                    <td class="calling_list" colspan="4">
                        <div id="scroll${s}" class="scrollable">
                        ${status_reason}
                        <span class="cp_header">${calling_at}</span>

HTML;

        if (is_array($service->subsequentCallingPoints->callingPointList->callingPoint)) {
            $cp = 0;

            foreach ($service->subsequentCallingPoints->callingPointList->callingPoint as $callingPoint) {
                $via = $callingPoint->locationName;
                $via_time = $callingPoint->et == "On time" ? $callingPoint->st : $callingPoint->et;
                $cp_dest = $via == $destination ? 'class="cp_dest"' : '';
                echo <<<HTML
                        <span $cp_dest>$via</span>

HTML;

                if ($status != "cancelled") {
                    echo <<<HTML
                        <span $cp_dest>($via_time)</span>

HTML;
                }
                $cp++;
            }
        } else {
            $callingPoint = $service->subsequentCallingPoints->callingPointList->callingPoint;
            $via = $callingPoint->locationName;
            $via_time = $callingPoint->et == "On time" ? $callingPoint->st : $callingPoint->et;
            $cp_dest = $via == $destination ? 'class="cp_dest"' : '';
            echo <<<HTML
                        <span $cp_dest>$via</span>

HTML;

            if ($status != "cancelled") {
                echo <<<HTML
                        <span $cp_dest>($via_time)</span>

HTML;
            }
        }

        echo <<<HTML
                        <div>
                    </td>
                </tr>

HTML;
        $s++;
        if ($s >= $SERVICES_MAX) {
            break;
        }

    }

    $offset = $offset + $TIME_WINDOW;

?>
    <!--div id="dump">
    ### DEPARTURES JSON ####
    <?php print_r($board);?>
    </div-->
<?php
} // while
?>

            </tbody>
        </table>

    </div>
    <div class="NRESpaceContainer">
        <div class="NRESpaceHeader">Messages:</div>
<?php
    if (isset($board->nrccMessages)) {
        $m = 0;
        echo <<<HTML
    <div id="NRESpace" class="nre_space">
HTML;
        if (!is_array($board->nrccMessages->message)) {
            $messages = array($board->nrccMessages->message);
        } else {
            $messages = $board->nrccMessages->message;
        }
        
        foreach($messages as $message) {
            $message_text = $message->_;
            $cut = strpos($message_text, " More ");
            if ($cut > 0) {
                $message_text = substr($message_text, 0, $cut);
            }
            echo <<<HTML
    
        <div id="message${m}" class="message">${message_text}</div>
HTML;
        }
        $time = date("H:i");
        echo <<<HTML
        <div class="last_updated">Last updated: ${time}</div>
    </div>
HTML;

    }
?>
    </div>   

    <!--div id="dump">
### DEPARTURES JSON ####
<?php print_r($board);?>
    </div-->

</body>

</html>
