 <?php 
   $database = Database::getInstance();
   $movie_id = htmlspecialchars($_GET["movie_id"]); 
   $selected_genres = htmlspecialchars($_GET["genres"]); 
   $selected_order = htmlspecialchars($_GET["order"]); 
   $movie = $database->getMovieDetails($movie_id);
   session_start();
   $uid = isset($_POST['uid']) ? $_POST['uid'] : $_SESSION['uid'];
   $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : $_SESSION['pwd'];
     ?>
<div id="botmenu">
   <div id="submenu" class="menu-primary-container">
      <ul id="web2feel" class="sfmenu">

         <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-880">
            <?php 
               echo '<a href="'.$base.'">';
                  ?>
            Gatunek</a>
            <ul class="sub-menu">
               <?php 
                  echo '<li class="menu-item menu-item-type-taxonomy menu-item-object-genre"><a href='.$base.'>Wszytskie</a></li>';
                  $all_genres = $database->getAllGenres();
                  foreach ($all_genres as $genre) { 
                  	echo '<li class="menu-item menu-item-type-taxonomy menu-item-object-genre"><a href='.$base.'?genres='.$genre[0].'>';
                  	echo $genre[1];
                  	echo '</a></li>';
                  }
                  ?>
            </ul>
         </li>
         <li class="menu-item menu-item-type-post_type menu-item-object-page"><a href='<?php echo $base?>/index.php/account'>Konto</a></li>
         <li style="width: 520" class="menu-item menu-item-type-post_type menu-item-object-page">&nbsp;</li>
         <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-883">
            <?php 
               if (isset($_SESSION['uid'])) {
               	$uid = $_SESSION['uid']; 
               	echo 'Zalogowany:<br>'.$uid; 
               }
               ?>
         </li>
      </ul>
      <?php
         if (isset($_SESSION['uid'])) {
         	echo '<a href="'.$base.'/index.php/logout"><button type="button">Wyloguj</button></a>';
         } else {
         	echo '<a href="'.$base.'/index.php/login"><button type="button">Zaloguj</button></a>';
         }
         ?>
   </div>
</div>