<?php
$rec_title = $_POST['rec_title'];
$rec_stars = $_POST['rec_stars'];
$rec_text = $_POST['rec_text'];

if (isset($rec_title) && isset($rec_stars) && isset($rec_text)) {
	$database = Database::getInstance();
	$database->addReview($movie->getId(),$_SESSION['uid'], $rec_stars, $rec_title, $rec_text, date("Y-m-d"));
	?>
<div class="post">
<br />
	<h2>Twój komentarz</h2>
	<p>Twoja opinia została przesłana</p>
	<p>
   	<span class="genretag clearfix">
	<?php echo '<a href="../index.php/movie?movie_id='.$movie->getId().'">Return</a>' ?>
	</span>
	</p>
</div><br />
	<?php
} else {
	?>
<div class="post">
<br />
	<h2>Twój komentarz</h2>
	  <p>Wypełnij Formularz</p>
	  <form action="" method="post">
	  <label>Tytuł: <br />
	  <input name="rec_title" type="text" value="" class="field"/><br /></label>
	  <label>Ocena: <br />
	  <select name="rec_stars">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3" selected>3</option>
		<option value="4">4</option>
		<option value="5">5</option>
	  </select><br />
	  </label>
	  <label>Komentarz: <br />
	  <textarea name="rec_text" cols="30" rows="5"></textarea>
	  <p>
	  	<input name="" type="reset" value="Wyczyść" /><input name="submitted" type="submit" value="Zatwierdź" /><br/>
	  </p>
	  </label>
	  </form>

</div><br />

	<?php
}  

?>