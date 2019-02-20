<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<title>QU-Fileanalyzer</title>
</head>
<body>
	<nav class="navbar navbar-inverse">
	  	<div class="container">
			<div class="navbar-header">
		  		<a class="navbar-brand" href="#">QU Fileanalyzer</a>
			</div>
          	<form class="navbar-form navbar-left" method="post" enctype="multipart/form-data">
				<div class="form-group">
					<input type="file" class="form-control-file" id="fileToUpload" name="fileToUpload" aria-describedby="fileHelp" placeholder="Allen & Heath QU Scene">
				</div>
		   		<button type="submit" value="Upload QU Scene" name="submit" class="btn btn-default">Szene anzeigen</button>
		 	</form>
      	</div>
    </nav>
	<div class="container">             

	<?php
    include 'quFileCore.php';

    function printInputChannelLine($caption, $value, $phantom, $hpfen, $source, $muteGroups, $dcaGroups,$channelGrouped)
    {
        $p = $phantom ? "x" : "";
		$hpfe = $hpfen ? "x" : "";
        $mgr1 = ($muteGroups & 1) ? "x" : "";
        $mgr2 = ($muteGroups & 2) ? "x" : "";
        $mgr3 = ($muteGroups & 4) ? "x" : "";
        $mgr4 = ($muteGroups & 8) ? "x" : "";
        $dca1 = ($dcaGroups & 1) ? "x" : "";
        $dca2 = ($dcaGroups & 2) ? "x" : "";
        $dca3 = ($dcaGroups & 4) ? "x" : "";
        $dca4 = ($dcaGroups & 8) ? "x" : "";
        $stereo = ($channelGrouped) ? "x" : "";
        echo "<tr><td>".$caption."</td><td>".$value."</td><td>".$source."</td><td>".$p."</td><td>".$hpfe."</td><td>".$stereo."</td><td>".$mgr1."</td><td>".$mgr2."</td><td>".$mgr3."</td><td>".$mgr4."</td><td>".$dca1."</td><td>".$dca2."</td><td>".$dca3."</td><td>".$dca4."</td></tr>\r\n";
    }

    function printOutputChannelLine($caption,$value)
    {
        echo "<tr><td>".$caption."</td><td>".$value."</td></tr>\r\n";
    }

    function printInputChannelGroup($qu,$i,$caption,$count)
    {
        for($j = 1;$j<=$count;$j++){
            printInputChannelLine($caption." ".$j,$qu->getChannelName($i),false,$qu->getChannelHPFEnabled($i),$qu->getChannelSource($i),$qu->getAssignedMuteGroups($i),$qu->getAssignedDcaGroups($i),$qu->isChannelLinked($i));
            $i++;
        }
        return $i;
    }

    function printOutputChannelGroup($qu,$i,$caption,$count){
        for($j = 1;$j<=$count;$j++){
            printOutputChannelLine($caption." ".$j,$qu->getChannelName($i));
            $i++;
        }
        return $i;
    }


    //Logic
    if(isset($_POST["submit"])) 
    {
        if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK               //checks for errors
                && is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) 
        { //checks that file is uploaded
            $file = file_get_contents($_FILES['fileToUpload']['tmp_name']);
            $qu = new QuFileCore($file);

            $sceneName = $qu->getSceneName();

            echo "<h1>Szene: ".$sceneName."</h1>";

            ?>
<div class="panel panel-default">
<div class="panel-body">
    <h2>Eingänge</h2>
	<div class="table-responsive">
    	<table class="table table-striped">
			<colgroup>
				<col class=col-xs-1>
				<col class=col-xs-1>
				<col class=col-xs-6>
			</colgroup>
		<thead>
		  <tr>
			<th>Kanal</th>
			<th>Beschriftung</th>
            <th>Quelle</th>
			<th>48V</th>
			<th>HPF Enabled</th>
            <th>Stereo Link</th>
            <th>Mute 1</th>
            <th>Mute 2</th>
            <th>Mute 3</th>
            <th>Mute 4</th>
            <th>DCA 1</th>
            <th>DCA 2</th>
            <th>DCA 3</th>
            <th>DCA 4</th>
		  </tr>
		</thead>
		<tbody>
        <?php
            $i = 0;
            $channel = $qu->getChannelNumber($i);

            //Normale MIC Kanäle
            while($channel == $i)
            {
                printInputChannelLine($i+1, $qu->getChannelName($i), $qu->getChannelPhantom($i), $qu->getChannelHPFEnabled($i), $qu->getChannelSource($i),$qu->getAssignedMuteGroups($i),$qu->getAssignedDcaGroups($i),$qu->isChannelLinked($i));
                $i++;
                $channel = $qu->getChannelNumber($i);
            }

            //Stereo Kanäle
            $i = printInputChannelGroup($qu,$i,"St",3);

            //FX Returns
            $i = printInputChannelGroup($qu,$i,"FX",4);

        ?>
		</tbody>
	</table>
</div>
<h2>Ausgänge</h2>
<div class="table-responsive">
	<table class="table table-striped">
            <colgroup>
                <col class=col-xs-1>
                <col class=col-xs-7>
            </colgroup>
		<thead>
      		<tr>
			<th>Kanal</th>
			<th>Beschriftung</th>
		  </tr>
		</thead>
		<tbody>
        <?php
            //Aux 1 - 4
            $i = printOutputChannelGroup($qu,$i,"Aux",4);

            //Aux 5-10 (Stereo Aux)
            for($j = 1;$j<=3;$j++){
                printOutputChannelLine("Aux ".(3+$j*2)."-".(4+$j*2),$qu->getChannelName($i),false);
                $i++;
            }

            //LR
            printOutputChannelLine("LR",$qu->getChannelName($i),false);
            $i++;

            //Gruppe 1-4
            $i = printOutputChannelGroup($qu,$i,"Gruppe",4);

            //Matrix 1-4
            $i = printOutputChannelGroup($qu,$i,"Matrix",4);

            //FX Sends
            $i = printOutputChannelGroup($qu,$i,"FX Send",4);
        ?>
		</tbody>
    </table>
</div>
</div>

            <?php

        }
    }

?>
		
        <div class="panel-footer">
        	&copy; 2017 David Zingg
        </div>
    </div>		
</div>

</body>
</html>