<?php
	if (isset($_GET['action'])){
		switch($_GET['action']) {
			case 'borrow' :
				$database->borrowMovie($movie_id,$uid);
			break;
			case 'return' :
				$database->returnMovie($movie_id);
			break;
		}
	} 
?>

<script>
    // This is the first thing we add ------------------------------------------
    $(document).ready(function() {
        
        $('.rate_widget').each(function(i) {
            var widget = this;
            var out_data = {
                widget_id : $(widget).attr('id'),
                fetch: 1
            };
            $.post(
                'ratings.php',
                out_data,
                function(INFO) {
                    $(widget).data( 'fsr', INFO );
                    set_votes(widget);
                },
                'json'
            );
        });
    

        $('.ratings_stars').hover(
            // Handles the mouseover
            function() {
                $(this).prevAll().andSelf().addClass('ratings_over');
                $(this).nextAll().removeClass('ratings_vote'); 
            },
            // Handles the mouseout
            function() {
                $(this).prevAll().andSelf().removeClass('ratings_over');
                // can't use 'this' because it wont contain the updated data
                set_votes($(this).parent());
            }
        );
        
        
        // This actually records the vote
        $('.ratings_stars').bind('click', function() {
            var star = this;
            var widget = $(this).parent();
            
            var clicked_data = {
                clicked_on : $(star).attr('class'),
                widget_id : $(star).parent().attr('id')
            };
            $.post(
                'ratings.php',
                clicked_data,
                function(INFO) {
                    widget.data( 'fsr', INFO );
                    set_votes(widget);
                },
                'json'
            ); 
        });
        
        
        
    });

    function set_votes(widget) {

        var avg = $(widget).data('fsr').whole_avg;
        var votes = $(widget).data('fsr').number_votes;
        var exact = $(widget).data('fsr').dec_avg;
    
        window.console && console.log('and now in set_votes, it thinks the fsr is ' + $(widget).data('fsr').number_votes);
        
        $(widget).find('.star_' + avg).prevAll().andSelf().addClass('ratings_vote');
        $(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote'); 
        $(widget).find('.total_votes').text( votes + ' votes recorded (' + exact + ' rating)' );
    }
    function set_votes2(widget,avg) {        
        $(widget).find('.star_' + avg).prevAll().andSelf().addClass('ratings_vote');
        $(widget).find('.star_' + avg).nextAll().removeClass('ratings_vote'); 
        $(widget).find('.total_votes').text(avg+' stars rating');
    }
    function set_votes3(widget,count,avg,star) {        
        $(widget).find('.star_' + star).prevAll().andSelf().addClass('ratings_vote');
        $(widget).find('.star_' + star).nextAll().removeClass('ratings_vote'); 
        $(widget).find('.total_votes').text(count+' users (average rating '+avg+')');
    }



    
    
    
    
    </script>
<div class="video-single">
<div class="videoframe">
   <?php echo '<iframe width="800" height="450" src="'.$movie->getTrailerUrl().'"></iframe>'; ?>
</div>
<div class="clear"></div>

<div class="title">
   <?php echo '<h2><a href="" rel="bookmark">'.$movie->getTitle().'</a></h2>'; ?>
</div>
<div class="postmeta">
   <?php echo '<span class="clock">'.$movie->getPremiereDate().'</span>'; ?>
</div>
<?php
		$quality = $database->getMovieReviewAvg($movie_id);
		if ($quality[2] != 0) { 
			echo '	  <div class="movie_choice">';
			echo '     <div id="r_arg" class="rate_widget_show">';
			echo '        <div class="star_1 ratings_stars_show"></div>';
			echo '        <div class="star_2 ratings_stars_show"></div>';
			echo '        <div class="star_3 ratings_stars_show"></div>';
			echo '        <div class="star_4 ratings_stars_show"></div>';
			echo '        <div class="star_5 ratings_stars_show"></div>';
			echo '        <div class="total_votes"></div>';
			echo '     </div>';
			echo '	  </div>';
		}
	echo '<script>set_votes3("#r_arg",'.$quality[0].','.$quality[1].','.$quality[2].')</script>';
?>
<div class="entry">
   <?php echo '<p>'.$movie->getMovieActor().'</p>'; ?>
   <div class="clear"></div>
</div>
<div class="postmeta">
   <span class="genretag clearfix">
   <?php 
      $genres = $database->getMovieGenre($movie_id);
      foreach ($genres as $genre) { 
      	echo '<a href="'.$base.'/index.php?genres='.$genre[1].'" rel="tag">';
      	echo $genre[0];
      	echo '</a>';
      }
    ?>
   </span>
</div>

<div class="entry">
   <span class="genretag clearfix">
	<h2>
<?php 
	if(isset($_SESSION['uid'])) {
		echo '<a href="'.$base.'/index.php/movie_review?movie_id='.$movie->getId().'" rel="bookmark"> Dodaj opinię</a>'; 
		if (!$database->isBorrowed($movie_id)) {
			echo '<a href="'.$base.'/index.php/movie?movie_id='.$movie->getId().'&action=borrow" rel="bookmark"> Wypożycz film</a>'; 
		}
		else
		{
			$whoHas = $database->whoBorrowed($movie_id);
			if ($whoHas == $uid) {
				echo '<a href="'.$base.'/index.php/movie?movie_id='.$movie->getId().'&action=return" rel="bookmark"> Zwróć film</a>'; 
			} else
			{
				echo ' Movie is borrowed by "'.$whoHas.'"';
			}
		}
	} else
	{
		echo ' Aby wypożyczyć film lub dodać opinię musisz się <a href="'.$base.'/index.php/login" rel="bookmark">zalogować</a>'; 		
	};
?>
</h2>
</span>
</div>

<div class="clear"></div>

<div id="main">
   <div id="content">
      <?php 
	$reviews = $database->getMovieReviews($movie_id); 
	foreach ($reviews as $index=>$review) {
		echo '<div class="post">';
		echo '	<div class="title"><h2><a href="" rel="bookmark">'.$review[4].'</a></h2></div>';
		echo '	<div class="postmeta"><span>Posted by <a href="" rel="author">'.$review[2].'</a></span> | <span>'.$review[6].'</span></div>';
		echo '	<a href=""><img class="post-image" width="130px" src="https://e-uczelnia.uek.krakow.pl/pluginfile.php/7599/user/icon/ergo/f1?rev=467257"></a>';	
		echo '	<p>'.$review[5].'</p>';
		echo '  <div id="sidebar">';
		echo '	  <div class="movie_choice">';
		echo '     <div id="r'.$index.'" class="rate_widget_show">';
		echo '        <div class="star_1 ratings_stars_show"></div>';
		echo '        <div class="star_2 ratings_stars_show"></div>';
		echo '        <div class="star_3 ratings_stars_show"></div>';
		echo '        <div class="star_4 ratings_stars_show"></div>';
		echo '        <div class="star_5 ratings_stars_show"></div>';
		echo '        <div class="total_votes"></div>';
		echo '     </div>';
		echo '	  </div>';
		echo '  </div>';	
		echo '</div>';
		echo '<script>set_votes2("#r'.$index.'",'.$review[3].')</script>';
	}
      ?>

   </div>
   <div class="clear"></div>
</div>
