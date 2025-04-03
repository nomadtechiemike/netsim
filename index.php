<?php

require_once 'login.inc.php';

// Redirect to level listing if logged in and no specific level is set
if (LOGGEDIN && !isset($_GET['level'])) {
	include 'listing.inc.php';
	exit();
} else if (LOGGEDIN) {
	$l = (int)$_GET['level'];
	$q = $db->query("SELECT * FROM level WHERE id = ".$l);
	$leveldata = $q->fetchArray();
	if (!$leveldata) {
		include 'header.inc.php';
		?>
		<div style="text-align:center; min-height:100%;">
			<h3>Congratulations!</h3>
			<p>You beat the last level in the game!</p>

			<p><img src="./includes/fireworks.gif"></p>

			<p>You may want to go <a href="./">look over the levels</a> to make sure you didn't miss any.</p>

			<p>Otherwise, give yourself a pat on the back! You're a real hacker now!</p>

			<div style="height:150px;"></div>
		</div>
		<?php
		include 'footer.inc.php';
		exit();
	}
    include 'listing.inc.php';
    exit();
} elseif (LOGGEDIN) {
    $l = isset($_GET['level']) ? (int)$_GET['level'] : 0;

    // Securely fetch level data using prepared statements
    $stmt = $db->prepare("SELECT * FROM level WHERE id = :level");
    $stmt->bindValue(':level', $l, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $leveldata = $result->fetchArray(SQLITE3_ASSOC);

    // If level doesn't exist, show a congratulatory message
    if (!$leveldata) {
        include 'header.inc.php'; ?>
        <div class="center-content">
            <h3>Congratulations!</h3>
            <p>You have completed the final level!</p>
            <img src="./includes/fireworks.gif" alt="Celebration Fireworks">
            <p><a href="./">Review previous levels</a> to ensure you didn't miss any.</p>
            <p>Well done! You're officially a hacker now! 🎉</p>
        </div>
        <?php
        include 'footer.inc.php';
        exit();
    }
} else {
	$leveldata = array('id' => -1, 'filename' => 'login/login', 'name' => 'CS4G Netsim');
    // Default level data for non-logged-in users
    $leveldata = [
        'id' => -1,
        'filename' => 'login/login',
        'name' => 'CS4G Netsim'
    ];
}

?><!doctype html> 
<html lang="en"> 
<head> 
	<meta charset="UTF-8" />
	<title>CS4G Network Simulator</title>
	
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/phaser.min.js"></script>
	<script src="js/ui.js"></script>
	<script src="js/bindings.js"></script>
	<script src="js/devicescripts.js"></script>

	<link href="css/jquery-ui.min.css" rel="stylesheet">
	<style type="text/css">
		* { font-family:Arial, Helvetica, sans-serif; }
		body { margin: 0; overflow:hidden; }
	</style>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS4G Network Simulator</title>
    
    <!-- Stylesheets -->
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            margin: 0;
            overflow: hidden;
            background-color: #f4f4f4;
        }
        .center-content {
            text-align: center;
            min-height: 100%;
            padding: 20px;
        }
        #game {
            float: left;
            width: 100%;
            max-width: 800px;
        }
        #pane {
            position: absolute;
            padding: 20px;
            overflow: auto;
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #footer {
            position: absolute;
            bottom: 0.5em;
            right: 0.5em;
            font-size: 0.8em;
            text-align: right;
        }
        #loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #DDD;
            color: #222;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        @media (max-width: 768px) {
            #pane {
                position: relative;
                max-width: 100%;
            }
            #game {
                float: none;
            }
        }
    </style>

    <!-- JavaScript Libraries -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/phaser.min.js"></script>
    <script src="js/ui.js"></script>
    <script src="js/bindings.js"></script>
    <script src="js/devicescripts.js"></script>
</head>
<body>

<script type="text/javascript">
<script>
<?php include 'phaser.inc.php'; ?>
</script>

<div id="game" style="float:left"></div>
<div id="game"></div>

<div id="pane" style="position:absolute;padding:20px;overflow:auto;">
	<h1><?=$leveldata['name']?></h1>
<div id="pane">
    <h1><?= htmlspecialchars($leveldata['name']) ?></h1>

<?php if (LOGGEDIN) { ?>
	<input type="button" value="Level list" onclick="location.href='./'">
<?php } ?>
    <?php if (LOGGEDIN) { ?>
        <button onclick="location.href='./'">Level List</button>
    <?php } ?>

	<div id="leveldescrip" style="overflow:auto;">
	<?php include 'levels/'.$leveldata['filename'].'.html'; ?>
	</div>
    <div id="leveldescrip">
        <?php include 'levels/' . htmlspecialchars($leveldata['filename']) . '.html'; ?>
    </div>

	<input type="button" id="subpane_close" style="display:none" value="Level info" onclick="onSubpaneClose()">
	<div id="subpane" style="display:none"></div>
    <button id="subpane_close" style="display:none" onclick="onSubpaneClose()">Level Info</button>
    <div id="subpane" style="display:none"></div>
</div>

<div id="editor" style="display:none;">
	Sent from: 
	<select id="pktFrom">
		<option>Alice</option>
		<option>Bob</option>
		<option>Carol</option>
	</select><br>
	<fieldset>
		<legend>Network Layer</legend>
		srcip: <input type="text" id="srcip"></input><br>
		dstip: <input type="text" id="dstip"></input>
	</fieldset>
	<fieldset>
		<legend>Transport Layer</legend>
		payload: <input type="text" id="payload"></input><br>
		proto: <input type="text" id="other"></input>
	</fieldset>
    <p>Sent from:</p>
    <select id="pktFrom">
        <option>Alice</option>
        <option>Bob</option>
        <option>Carol</option>
    </select>
    <fieldset>
        <legend>Network Layer</legend>
        <label>srcip: <input type="text" id="srcip"></label><br>
        <label>dstip: <input type="text" id="dstip"></label>
    </fieldset>
    <fieldset>
        <legend>Transport Layer</legend>
        <label>payload: <input type="text" id="payload"></label><br>
        <label>proto: <input type="text" id="other"></label>
    </fieldset>
</div>

<div id="winner" style="display:none;">
	<p>You won the level! Congrats!</p>
    <p>You won the level! 🎉</p>
</div>

<div id="footer" style="position:absolute;bottom:0.5em;right:0.5em;font-size:0.5em">
	created by 
	<a href="https://erinn.io/">erinn atwater</a> and
	<a href="https://cs.uwaterloo.ca/~cbocovic">cecylia bocovich</a> | 
	device images designed by 
	<a href="http://www.flaticon.com/authors/madebyoliver">madebyoliver</a> from Flaticon
<div id="footer">
    Created by
    Erinn Atwater and Cecylia Bocovich |
    Updated by <a href="https://techiemike.com">Techie Mike</a> |
    Device images by
    <a href="http://www.flaticon.com/authors/madebyoliver">madebyoliver</a>

</div>

<div id="loading" style="position:absolute;top:0;left:0;right:0;bottom:0;background-color:#DDD;color:#222;text-align:center">
	<h2>Netsim</h3>
	<p>Loading...<p>
<div id="loading">
    <h2>Netsim</h2>
    <p>Loading...</p>
</div>

</body>
</html>
