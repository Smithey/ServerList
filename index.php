<?php
require_once("resources/config.php");

require '/resources/SourceQuery/bootstrap.php';
use xPaw\SourceQuery\SourceQuery;

require_once("resources/TeamSpeak3/TeamSpeak3.php");
TeamSpeak3::init();
?>

<link rel="stylesheet" type="text/css" href="resources/style.css">

<table>
	<thead>
		<tr>
			<th scope="col" id="RoundLeft"></th>
			<th scope="col">Name</th>
			<th scope="col">IP:Port</th>
			<th scope="col">Players</th>
			<th scope="col">Status</th>
			<th scope="col" id="RoundRight">Link</th>
		</tr>
	</thead>
	
	<tbody>

		<?php
			foreach ($Servers as $k => $v) {

				if($Debug) {
					echo '<br>DEBUG: Started foreach for Servers';
				}

				$Query = null;
				$QInfo = null;
				$SS = null;
				$SN = null;
				$SA = null;
				$SP = null;
				$SSD = null;
				$SU = null;
				$Icon = null;

				$Icon = $GameIcons[$v[0]];
				$IP = $v[1];
				$Port = $v[2];
				$Timeout = $v[3];

				if($Debug) {
					echo '<br>DEBUG: Cleared all values';
				}

				$Query = new SourceQuery( );

				try {

					if($v[0] == 'TS3') {
						$Query = TeamSpeak3::factory("serverquery://" . $v[5] . ":" . $v[6] . "@" . $IP . ":" . $v[4] . "/?server_port=" . $Port);
						$SP = $Query->getProperty("virtualserver_clientsonline") - $Query->getProperty("virtualserver_queryclientsonline");
						$SPM = $Query->getProperty("virtualserver_maxclients");
						$SS = "Online";
					} else {
						$Query->Connect( $IP, $Port, $Timeout, SourceQuery::SOURCE );
						$QInfo = $Query->GetInfo( );
						$SS = "Online";
					}

					if($Debug) {
					echo '<br>DEBUG: Queried Server info';
					}

				} catch (Exception $e) {
					$SS = "Offline";

					if($Debug) {
					echo '<br>DEBUG: Unable to query server info: ' . $e;
					}

				} finally {
					if($v[0] != 'TS3') {
						$Query->Disconnect( );
					}

					if($Debug) {
					echo '<br>DEBUG: Disconnect query';
					}

				}

				if(isset($SS) && $SS == "Online") {
					
					if($Debug) {
					echo '<br>DEBUG: Server is seen as Online';
					}

					if($v[0] == 'TS3') {
						$SN = $v[7];
						$SA = $IP;
						$SP = $SP . "/" . $SPM;
						$SSD = "<span id='Online'>Online</span>";
						$SU =  "<a href='ts3server://" . $IP . ":" . $Port . "/?&addbookmark=" . $IP . ":" . $Port . "' id='Join'>Join</a>";
					} else {
						$SN = $QInfo["HostName"];
						$SA = $v[1] . ":" . $QInfo["GamePort"];
						$SP = $QInfo["Players"] . "/" . $QInfo["MaxPlayers"];
						$SSD = "<span id='Online'>Online</span>";
						$SU = "<a href='" . "steam://run/" . $QInfo["GameID"] . "/connect/" . $v[1] . ":" . $QInfo["GamePort"] . "' id='Join'>Join</a>";
					}

					if($Debug) {
					echo '<br>DEBUG: Set all server info variables';
					}

				} elseif(!isset($SS) || $SS == "Offline") {

					if($Debug) {
					echo '<br>DEBUG: Server is offline';
					}

					$SN = "Unknown";
					$SA = $v[1] . ":" . $v[2];
					$SP = "0/0";
					$SSD = "<span id='Offline'>Offline</span>";
					$SU = "<a href='#' id='Join'>Join</a>";
				}

				echo '<tr>';
				echo '<td><img src="' . $Icon . '" width="' . $IconSize . 'px" height="' . $IconSize . 'px"></td>';
				echo '<td>' . $SN . '</td>';
				echo '<td>' . $SA . '</td>';
				echo '<td>' . $SP . '</td>';
				echo '<td>' . $SSD . '</td>';
				echo '<td>' . $SU . '</td>';
				echo '</tr>';
			}
		?>
	</tbody>
</table>