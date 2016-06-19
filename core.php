<?php
date_default_timezone_set('Europe/Warsaw');
require_once 'config/teamspeak.php';
require_once 'include/ts3admin.class.php';
//-------------------------------------FUNKCJA PORÓWNUJĄCA DATY-------------------------------------//
function sameDay($ts1, $ts2)
{
   if (date("Y", $ts1) != date("Y", $ts2)) {
      return False;
   }
   if (date("m", $ts1) != date("m", $ts2)) {
      return False;
   }
   if (date("d", $ts1) != date("d", $ts2)) {
      return False;
   }
   return True;
}
$query = new ts3admin($teamspeak['address'], $teamspeak['tcp']);
if($query->getElement('success', $query->connect())) {
    $query->login($teamspeak['login'],$teamspeak['password']);
    $query->selectServer($teamspeak['udp']);
    $query->setName($bot['name']);
    while (true) { 
        $core = $query->getElement('data',$query->whoAmI());
        $query->clientMove($core['client_id'],$bot['default_channel']);  
        $users = $query->getElement('data',$query->clientList('-groups -voice -away -times'));
        foreach ($users as $client) {
            if ($client['client_nickname'] != $bot['name']) {
               //--------------CORE BOTA--------//
				if(($client['client_channel_group_id']==9)||($client['client_channel_group_id']==10)||($client['client_channel_group_id']==11)){
					$idchannel=$client['cid'];
					$channelinfos = $query->channelInfo($idchannel);
					if($channelinfos['success']==1){
						$topic = (string)$channelinfos['data']['channel_topic'];
						if(count(explode(".",$topic))==4){
							$extime = explode(".",$topic);
							if(!empty($extime[3])){
								$data = array();
								$data['channel_topic'] = date("d").".".date("m").".".date("Y").".";
								$query->channelEdit	($idchannel,$data);	
								$query->sendMessage(1, $client['clid'], "[b]Data Kanalu zostal zaktualizowana ");
							}else{
								$dzis = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
								$kiedys = mktime(0, 0, 0, (int)$extime[1], (int)$extime[0], (int)$extime[2]);
								if($dzis>$kiedys){
									$data = array();
									$data['channel_topic'] = date("d").".".date("m").".".date("Y").".";
									$query->channelEdit	($idchannel,$data);
									$query->sendMessage(1, $client['clid'], "[b]Data Kanalu zostal zaktualizowana ");
								}else if($dzis<$kiedys){
									$data = array();
									$data['channel_topic'] = date("d").".".date("m").".".date("Y").".";
									$query->channelEdit	($idchannel,$data);
									$query->sendMessage(1, $client['clid'], "[b]Data Kanalu zostal zaktualizowana ");

								}
							}
						}else{
							$data = array();
							$data['channel_topic'] = date("d").".".date("m").".".date("Y").".";
							$query->channelEdit	($idchannel,$data);	
							$query->sendMessage(1, $client['clid'], "[b]Data Kanalu zostal zaktualizowana ");
						}
					}
				} 
            }
        }
        sleep(1);
    }
}
?>