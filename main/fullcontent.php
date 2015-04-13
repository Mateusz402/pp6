<div class="entry">
   <span class="genretag" style="margin-left:40px">Sortuj według:
   <a href="<?php echo $base.'?genres='.$selected_genres.'&order=id'?>">Ostatnio dodane</a>
   <a href="<?php echo $base.'?genres='.$selected_genres.'&order=premiereDate'?>">Nowości</a>
   <a href="<?php echo $base.'?genres='.$selected_genres.'&order=popularity'?>">Najczęściej wypożyczane</a>
   <a href="<?php echo $base.'?genres='.$selected_genres.'&order=reviews'?>">Najczęściej komentowane</a>
   </span>
</div>
<div id="fullcontent">
   <?php 
      $database = Database::getInstance();
      $movies = $database->getFullContentMovies($selected_genres,$selected_order);
      foreach ($movies as $movie) {
        echo '<div class="video-post">';
      	echo '<div class="video-title">';
    	echo '<h2><a href="'.$base.'/index.php/movie?movie_id='.$movie->getId().'" rel="bookmark">'.$movie->getTitle().'</a></h2>';			
    	echo '</div>';
    	echo '<a href="'.$base.'/index.php/movie?movie_id='.$movie->getId().'"><img class="video-image" width=380 height=200 src="'.$movie->getThumbnailUrl().'"></a>';
      	echo '</div>';
      };
    ?>
   <div class="clear"></div>
</div>
<div class="clear"></div>