<?php
session_start();
$uid = isset($_POST['uid']) ? $_POST['uid'] : $_SESSION['uid'];
$pwd = isset($_POST['pwd']) ? $_POST['pwd'] : $_SESSION['pwd'];

$database = Database::getInstance();

if($database->validateUserPassword($uid,$pwd)) {
	$_SESSION['uid'] = $uid;
	$_SESSION['pwd'] = $pwd;
	
	?>
<div class="post">
<BR/>
			<h2>Logowanie powiodło się</h2>
			<span class="genretag clearfix"><a href="../index.php">Powrót do listy filmów</a></span>
</div><BR/>
	<?php

} else {
	?>

<div class="post">
<br/>		
			<?php
			if ($uid || $pwd) {
				echo("<p>Zła nazwa użytkownika lub hasło. Spróbuj ponownie.</p>");
			};
			
			?>
			<h2>Logowanie</h2>
			<form method="post" action="<?=$_SERVER['PHP_SELF']?>"
				<p>Użytkownik: <input type="text" class="field" name="uid" id="uid"></p>
				<p>Hasło: <input type="password" class="field" name="pwd" id="pwd"></p>
				<input type="submit" text="Log in"></input>
			</form>
</div><BR/>
	<?php

}
?>