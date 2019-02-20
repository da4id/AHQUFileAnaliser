<!DOCTYPE html>

<html>
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
		
		<script>
			$(function() {
			  // We can attach the `fileselect` event to all file inputs on the page
			  $(document).on('change', ':file', function() {
				var input = $(this),
					numFiles = input.get(0).files ? input.get(0).files.length : 1,
					label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
				input.trigger('fileselect', [numFiles, label]);
			  });

			  // We can watch for our custom `fileselect` event like this
			  $(document).ready( function() {
				  $(':file').on('fileselect', function(event, numFiles, label) {

					  var input = $(this).parents('.input-group').find(':text'),
						  log = numFiles > 1 ? numFiles + ' files selected' : label;

					  if( input.length ) {
						  input.val(log);
					  } else {
						  if( log ) alert(log);
					  }

				  });
			  });

			});
		</script>
	</head>
    
    <body>
		<nav class="navbar navbar-inverse">
	  	<div class="container">
			<div class="navbar-header">
		  		<a class="navbar-brand" href="#">QU Fileanalyzer</a>
			</div>
      	</div>
    </nav>
	<div class="container"> 
	<div class="panel panel-default">
		<div class="panel-body">
			<h1>Szenen zum vergleichen auswählen</h1>
			<form method="post" enctype="multipart/form-data">
				<div class="input-group">
					<label class="input-group-btn">
						<span class="btn btn-default">
							Szene 1&hellip; <input type="file" style="display: none;" name="file1" id="file1">
						</span>
					</label>
					<input type="text" class="form-control" readonly>
				</div>
				<div class="input-group">
					<label class="input-group-btn">
						<span class="btn btn-default">
							Szene 2&hellip; <input type="file" style="display: none;" name="file2" id="file2">
						</span>
					</label>
					<input type="text" class="form-control" readonly>
				</div>
				<input type="submit" class="btn btn-default" value="Upload Image" name="submit">
			</form>

<?php

    function printDiff($position,$oldval,$newval)
    {
        echo "<tr><td>0x".dechex($position)."</td><td>".dechex($oldval)."</td><td>".dechex($newval)."</td><td>".chr($oldval)."</td><td>".chr($newval)."</td></tr>\r\n";
    }

	define("scenenNamePosition",0x0c);

    function getByte($file,$pos)
    {
        $byte = $file[$pos];
        return hexdec(bin2hex($byte));
    }

    if(isset($_POST["submit"])) 
    {
        if ($_FILES['file1']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file1']['tmp_name']) && $_FILES['file2']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file1']['tmp_name']))
        {
			$file1 = file_get_contents($_FILES['file1']['tmp_name']);
            $file2 = file_get_contents($_FILES['file2']['tmp_name']);
			
			$scene1Name = substr($file1,scenenNamePosition,14);
			$scene2Name = substr($file2,scenenNamePosition,14);
			
			echo "<h2>Szene 1: ".$scene1Name."</h2>";
			echo "<h2>Szene 2: ".$scene2Name."</h2>";
?>
			<div class="table-responsive">
				<table class="table table-striped">
				<tr><th>Byte</th><th>Szene 1 Wert</th><th>Szene 2 Wert</th><th>Szene 1 Zeichen</th><th>Szene 2 Zeichen</th></tr>
<?php
            

            echo "checking differences in Files<br>";
            for($i = 0;$i < $_FILES['file1']['size'];$i++)
            {
                $byte1 = getByte($file1,$i);
                $byte2 = getByte($file2,$i);

                $diff = $byte1 - $byte2;
                if($diff != 0)
                {
                    printDiff($i,$byte1,$byte2);
                }
            }

        }
    }

?>
				</table>
			</div>
		</div>
			<div class="panel-footer">
        		&copy; 2017 David Zingg
        	</div>
		</div>
		</div>
    </body>
</html>