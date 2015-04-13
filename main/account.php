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
        $(widget).find('.total_votes').text(count+' reviews (average rating '+avg+')');
    }



    
    
    
    
    </script>
<BR/>
<div class="post">
   <span class="clearfix">
	<h2>Historia
	<table>
		<tr>
			<th>Nr</th>
			<th>Użytkownik</th>
			<th>Film</th>
			<th>Data wypożyczenia</th>
			<th>Data zwrotu</th>
		</tr>
      		<?php 
			$borrows = $database->whatBorrowed($uid); 
			foreach ($borrows as $index=>$borrow) {
				echo '<tr>';
				echo '<td>'.($index+1).'</td>';
				echo '<td>'.$borrow[0].'</td>';
				echo '<td><a href="'.$base.'/index.php/movie?movie_id='.$borrow[2].'">'.$borrow[1].'</a></td>';
				echo '<td>'.$borrow[3].'</td>';
				if ($borrow[4]=='0000-00-00 00:00:00')
				{
					echo '<td></td>';
				} else {
					echo '<td>'.$borrow[4].'</td>';
				}
				echo '</tr>';
			}
		?>
	</table>
	</h2>
   </span>
</div>
<div class="clear"></div>

<div id="main">
   <div id="content">
<div class="post">
<h2>Twoje komentarze</h2>
<?php
		$quality = $database->getUserReviewAvg($uid);
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
</div>
      <?php 
	$reviews = $database->getUserReviews($uid); 
	foreach ($reviews as $index=>$review) {
		echo '<div class="post">';
		echo '	<div class="title"><h2><a href="" rel="bookmark">'.$review[4].'</a></h2></div>';
		echo '	<div class="postmeta"><span>Wysłane przez <a href="" rel="author">'.$review[2].'</a></span> | <span>'.$review[6].'</span></div>';
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