<?php
require_once 'login.inc.php';

if (isset($_POST['reg_username']) && isset($_POST['reg_password'])) {
	$userq->bindValue(':name', $_POST['reg_username']);
	$res = $userq->execute();

	if ($res === false || $res->fetchArray() === false) {
		$q = $db->prepare("INSERT INTO user (name, password) VALUES (:name, :password)");
		$q->bindValue(':name', $_POST['reg_username']);
		$q->bindValue(':password', password_hash($_POST['reg_password'], PASSWORD_DEFAULT));
		if ($q->execute()) {
			$res->finalize();
			$userq->bindValue(':name', $_POST['reg_username']);
			$res = $userq->execute();
			$row = $res->fetchArray();
			$_SESSION['cs4g_user_id'] = $row['id'];
			header('Location: ./');
			exit('Registration successful! <a href="./">Continue</a>');
		}
		else $login_error = "Hrm, something happened. Try again! (".$db->lastErrorMsg().")";
	} else {
		$login_error = "Someone has that username already...";
	}
$login_error = "";

// Handle form submission
if (!empty($_POST['reg_username']) && !empty($_POST['reg_password']) && !empty($_POST['confirm_password'])) {
    $username = trim($_POST['reg_username']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if username is at least 5 characters
    if (strlen($username) < 5) {
        $login_error = "Username must be at least 5 characters long.";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $login_error = "Passwords do not match.";
    } else {
        // Check if username exists
        $userq = $db->prepare("SELECT id FROM user WHERE name = :name");
        $userq->bindValue(':name', $username, SQLITE3_TEXT);
        $res = $userq->execute();
        $existingUser = $res->fetchArray(SQLITE3_ASSOC);

        if ($existingUser) {
            $login_error = "That username is already taken.";
        } else {
            // Insert new user
            $q = $db->prepare("INSERT INTO user (name, password) VALUES (:name, :password)");
            $q->bindValue(':name', $username, SQLITE3_TEXT);
            $q->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), SQLITE3_TEXT);

            if ($q->execute()) {
                // Retrieve newly inserted user ID
                $userq = $db->prepare("SELECT id FROM user WHERE name = :name");
                $userq->bindValue(':name', $username, SQLITE3_TEXT);
                $res = $userq->execute();
                $row = $res->fetchArray(SQLITE3_ASSOC);

                if ($row) {
                    $_SESSION['cs4g_user_id'] = $row['id'];
                    header('Location: ./');
                    exit();
                } else {
                    $login_error = "Registration successful, but login failed. Please try logging in.";
                }
            } else {
                $login_error = "Error registering user. Try again later.";
            }
        }
    }
}

include 'header.inc.php';

?>

<h3>Register</h3>

<p>User accounts are only used to track your progress through levels. Please note that Netsim is still in <strong>beta</strong>, so we may need to reset the user database from time to time.</p>
<p>User accounts track your progress through levels. Note that Netsim is in <strong>beta</strong>, so some data may be reset.</p>

<!-- Styled Error Message -->
<?php if (!empty($login_error)) : ?>
    <div class="ui-widget">
        <div class="ui-state-error ui-corner-all" style="padding: 0.7em;">
            <p>
                <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                <?= htmlspecialchars($login_error) ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<!-- Styled Registration Form -->
<form method="post" action="register.php" onsubmit="return validateForm();" class="ui-widget-content ui-corner-all" style="padding: 15px; max-width: 300px;">
    <p><label for="reg_username">Username:</label><br>
        <input type="text" name="reg_username" id="reg_username" required minlength="5" class="ui-widget-content ui-corner-all" style="width: 100%; padding: 5px;"></p>

<?=(isset($login_error) ? "<p>".$login_error."</p>\n" : "")?>
    <p><label for="reg_password">Password:</label><br>
        <input type="password" name="reg_password" id="reg_password" required class="ui-widget-content ui-corner-all" style="width: 100%; padding: 5px;"></p>

<form method="post" action="register.php" onsubmit="if (document.getElementById('reg_password').value == document.getElementById('confirm_password').value) return true; else { alert('Passwords don\'t match!'); return false; }">
	<p>Username:<br>
	<input type="text" name="reg_username"></p>
	<p>Password:<br>
	<input type="password" name="reg_password" id="reg_password"></p>
	<p>Confirm password:<br>
	<input type="password" id="confirm_password"></p>
	<p><input type="submit" value="Register"></p>
    <p><label for="confirm_password">Confirm password:</label><br>
        <input type="password" name="confirm_password" id="confirm_password" required class="ui-widget-content ui-corner-all" style="width: 100%; padding: 5px;"></p>

    <p><input type="submit" value="Register" class="ui-button ui-widget ui-corner-all"></p>
</form>

<div style="height:150px;"></div>

<!-- Validation Script -->
<script>
    function validateForm() {
        let password = document.getElementById('reg_password').value;
        let confirmPassword = document.getElementById('confirm_password').value;
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }

    document.getElementById('reg_username').focus();
</script>

<?php include 'footer.inc.php'; ?>
