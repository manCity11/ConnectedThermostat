<!DOCTYPE html>
<html>
	<head>
		<title>Planning Thermostat</title>
		<style type="text/css">
        	caption /* Titre du tableau */
			{
				margin: auto; /* Centre le titre du tableau */
					font-family: Arial, Times, "Times New Roman", serif;
					font-weight: bold;
					font-size: 1.2em;
					color: #009900;
					margin-bottom: 20px; /* Pour Ã©viter que le titre ne soit trop collÃ© au tableau en-dessous */
			}
																							   
			table /* Le tableau en lui-mÃªme */
			{
				margin: auto; /* Centre le tableau */
					border: 4px outset green; /* Bordure du tableau avec effet 3D (outset) */
					border-collapse: collapse; /* Colle les bordures entre elles */
					width:100%;
			}
			
			th /* Les cellules d'en-tÃªte */
			{
				background-color: #006600;
					color: white;
					font-size: 1.1em;
					font-family: Arial, "Arial Black", Times, "Times New Roman", serif;
					border:1px solid red;
			}
																																																												   
			td /* Les cellules normales */
			{
				border: 1px solid black;
					font-family: "Comic Sans MS", "Trebuchet MS", Times, "Times New Roman", serif;
					text-align: center; /* Tous les textes des cellules seront centrÃ©s*/
					padding: 5px; /* Petite marge intÃ©rieure aux cellules pour Ã©viter que le texte touche les bordures */
			}
			
			td.time
			{
				width:5%;
			}
		</style>
		
		<script type="text/javascript" src="zepto.min.js"></script>

		<script type="text/javascript">
		function ledon()
		{
		$('#content').load('/arduino/ledon');
		}
		function ledoff()
		{
		$('#content').load('/arduino/ledoff');
		}
		function tempmoins()
		{
		$('#content').load('/arduino/tempmoins');
		}
		function tempplus()
		{
		$('#content').load('/arduino/tempplus');
		}
		</script>
	</head>

	<body onload="setInterval(refresh, 2000);">
		<table border=1>
			<tr>
				<td>Thermostat</td>				
				<td><button onclick="ledon()">ON</button></td>
				<td><button onclick="ledoff()">OFF</button></td>
			</tr>
			<tr>
				<td>Temperature</td>
				<td><button onclick="tempmoins()">-</button></td>
				<td><button onclick="tempplus()">+</button></td>
			</tr>
		</table>
		<table>
        <?php
        	$jour = array(null, "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
                
                $db = new SQLite3("/mnt/sda1/Test/calendrier.db");
                $result = $db->query('SELECT * FROM Calendrier');
                
                while($row = $result->fetchArray())
                {
                	$rdv[$row['jour']][$row['heure']] = $row['etat'];
                }
                         
                echo "<tr><th>Heure</th>";
                
                for($x = 1; $x < 8; $x++)
                	echo "<th>".$jour[$x]."</th>";
                echo "</tr>";
                
                for($j = 0; $j < 24; $j ++) {
                	echo "<tr>";
                        for($i = 0; $i < 7; $i++) {
                        	if($i == 0) {
                                	$heure = str_replace(".5", ":30", $j);
                                        echo "<td class=\"time\">".$heure."</td>";
                                }
                                echo "<td>";
                                if(isset($rdv[$jour[$i+1]][$heure])) {
                                	echo $rdv[$jour[$i+1]][$heure];
                                }
                                echo "</td>";
                        }
                        echo "</tr>";
                }
        ?>
		</table>
	</body>
</html>

