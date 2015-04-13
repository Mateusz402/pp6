<?php
   include('parameters.php');
   
   final class Database
   {
       
       private static $oInstance = false;
   			
       public static function getInstance()
       {
           if( self::$oInstance == false )
           {
               self::$oInstance = new Database();
           }
           return self::$oInstance;
       }
   
       public function getSliderMovies() 
       {
   	$query = "SELECT id serial, title, DATE_FORMAT(premiereDate,'%Y-%m-%d'), thumbnailUrl, movieActor, trailerUrl FROM movies";
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,new MovieEntity($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]));
   	}
   	return $results;
       }
   
       public function getFullContentMovies($genre = NULL,$order = NULL) 
       {
   	if ($order == NULL) {
   		$order="id";
   	}
   	if ($order == "reviews") {
   		$query = "SELECT movies.id serial, movies.title, DATE_FORMAT(premiereDate,'%Y-%m-%d'), thumbnailUrl, movieActor, trailerUrl FROM movies join reviews on movies.id=reviews.movie_id group by movie_id order by count(movie_id) desc";	
   
   	} else if ($order == "popularity") {
   		$query = "SELECT movies.id serial, movies.title, DATE_FORMAT(premiereDate,'%Y-%m-%d'), thumbnailUrl, movieActor, trailerUrl FROM movies join borrows on movies.id=borrows.movie_id group by movie_id order by count(movie_id) desc";
   	} else if ($genre == NULL) {
   		$query = "SELECT id serial, title, DATE_FORMAT(premiereDate,'%Y-%m-%d'), thumbnailUrl, movieActor, trailerUrl FROM movies ORDER BY ".$order." desc";
   	} else {
   		$query = "SELECT DISTINCT movies.* FROM movies inner join movie_genres on movie_genres.movie_id=movies.id where movie_genres.genre_id in (".$genre.")  ORDER BY ".$order." desc";
   	}
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,new MovieEntity($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]));
   	}
   	return $results;
       }
   
   
       public function getMovieDetails($movie_id) 
       {
   	$query = 'SELECT id serial, title, DATE_FORMAT(premiereDate,"%Y-%m-%d"), thumbnailUrl, movieActor, trailerUrl FROM movies where id='.$movie_id;
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,new MovieEntity($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]));
   	}
   	return $results[0];
       }
   
       public function getMovieGenre($movie_id) 
       {
   	$query = "SELECT genres.text,genres.id FROM movie_genres inner join genres on genres.id=movie_genres.genre_id where movie_genres.movie_id=".$movie_id;
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,$row);
   	}
   	return $results;
       }
   
       public function getMovieReviews($movie_id) 
       {
   	$query = "SELECT id, movie_id, (select username from users where id=reviews.user_id), stars, title, text, date FROM reviews where movie_id=".$movie_id;
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,$row);
   	}
   	return $results;
       }
   
       public function getUserReviews($user_id) 
       {
   	$query = "SELECT id, movie_id, (select username from users where id=reviews.user_id), stars, title, text, date FROM reviews where user_id=(select id from users where username='".$user_id."');";
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,$row);
   	}
   	return $results;
       }
   
       public function getMovieReviewAvg($movie_id) 
       {
   	$query = "SELECT count(*),ROUND(avg(stars),1),ROUND(avg(stars), 0) FROM reviews where movie_id=".$movie_id;
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,$row);
   	}
   	return $results[0];
       }
   
       public function getUserReviewAvg($user_id) 
       {
   	$query = "SELECT count(*),ROUND(avg(stars),1),ROUND(avg(stars), 0) FROM reviews where user_id=(select id from users where username='".$user_id."');";
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,$row);
   	}
   	return $results[0];
       }
   
       public function getAllGenres() 
       {
   	$query = "SELECT * FROM genres";
   	$sql_result = mysqli_query($this->conn, $query);
   	$results = array();
   	while($row = mysqli_fetch_array($sql_result))
   	{		
   		array_push($results,$row);
   	}
   	return $results;
       }
   
       public function borrowMovie($movie_id,$user_name) 
       {
   
   		$query = "INSERT INTO borrows (movie_id, user_id, borrow_date) VALUES (".$movie_id.",(select id from users where username='".$user_name."'),now());";
   		$sql_result = mysqli_query($this->conn, $query);
   	
       }
   
       public function returnMovie($movie_id) 
       {
   	$query = "UPDATE borrows SET return_date=now() where movie_id=".$movie_id." and return_date='0000-00-00 00:00:00';";
   	$sql_result = mysqli_query($this->conn, $query);
       }
   
   	public function isBorrowed($movie_id)
   	{
   		$query = "SELECT user_id from borrows where movie_id='".$movie_id."' and return_date='0000-00-00 00:00:00';";
   		$sql_result = mysqli_query($this->conn, $query);
   		if (mysqli_fetch_array($sql_result)) {
   			return true;
   		} else {
   			return false;
   		}
   	}
   
   
   	public function whoBorrowed($movie_id)
   	{
   		$query = "select username from users where id=(SELECT user_id from borrows where movie_id='".$movie_id."' and return_date='0000-00-00 00:00:00');";
   		$sql_result = mysqli_query($this->conn, $query);
   		$row = mysqli_fetch_array($sql_result);
   		return $row[0];
   	}
   	
   	public function whatBorrowed($user_id)
   	{
   		$query = "select users.username,movies.title,movies.id,borrows.borrow_date,borrows.return_date from borrows join users on users.id=borrows.user_id join movies on borrows.movie_id=movies.id where users.username='".$user_id."';";
   		$sql_result = mysqli_query($this->conn, $query);
   		$results = array();
   		while($row = mysqli_fetch_array($sql_result))
   		{		
   			array_push($results,$row);
   		}
   		return $results;
   	}
   	
   	public function validateUserPassword($user, $pass)
   	{
   		$query = "SELECT * from users where username='".$user."' and password='".$pass."';";
   		$sql_result = mysqli_query($this->conn, $query);
   		if (mysqli_fetch_array($sql_result)) {
   			return true;
   		} else {
   			return false;
   		}
   	}
   	
   	public function addReview($movie_id,$user_name,$stars,$title,$text,$date)
   	{
   		
   		$query = "insert into reviews(movie_id,user_id,stars,title,text,date) VALUES ('".$movie_id."',(select id from users where username='".$user_name."'),'".$stars."','".$title."', '".$text."', '".$date."');";
   		mysqli_query($this->conn, $query);
   	}
   
   
       public function createDbTables()
       {
    	$all_queries = "DROP TABLE movie_genres;".
   		"DROP TABLE reviews;".
   		"DROP TABLE borrows;".
   		"DROP TABLE movies;".
   		"DROP TABLE genres;".
   		"DROP TABLE users;".
   		"CREATE TABLE movies (id serial PRIMARY KEY, title VARCHAR (80) NOT NULL, premiereDate TIMESTAMP, thumbnailUrl VARCHAR (255), movieActor TEXT, trailerUrl VARCHAR (255));".
   		"CREATE TABLE genres (id serial PRIMARY KEY, text VARCHAR (25) NOT NULL);".
   		"INSERT INTO genres(text) VALUES ('Akcja');".
   		"INSERT INTO genres(text) VALUES ('Komedia');".
   		"INSERT INTO genres(text) VALUES ('Dokument');".
   		"INSERT INTO genres(text) VALUES ('Thriller');".
   		"INSERT INTO genres(text) VALUES ('Sci-Fi');".
   		"INSERT INTO genres(text) VALUES ('Animacja');".
   		"INSERT INTO genres(text) VALUES ('Biograficzny');".
   		"INSERT INTO genres(text) VALUES ('Horror');".
   		"INSERT INTO genres(text) VALUES ('Dramat');".
  		"CREATE TABLE movie_genres (id serial PRIMARY KEY, movie_id BIGINT UNSIGNED NOT NULL, genre_id BIGINT UNSIGNED, FOREIGN KEY(movie_id) REFERENCES movies(id), FOREIGN KEY(genre_id) REFERENCES genres(id));".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Paganini','2013-06-01','http://www.cyfraplus.pl/ms_galeria/fotobase/43655_c.jpg','David Garrnett, Jared Harris','https://www.youtube.com/embed/6fRsp0hY2Ps');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (1,9);".
		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Skubani','2014-01-10','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43322_c.jpg&656&w','Franek, Olo','https://www.youtube.com/embed/knIAFOe3hoQ');".
		"insert into movie_genres(movie_id,genre_id) VALUES (2,6);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Blue Jasmine','2013-07-23','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/42910_c.jpg&656&w','Cate Blanchett, Alec Boldwin','https://www.youtube.com/embed/iVMeFbsqO5g');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (3,2);".
   		"insert into movie_genres(movie_id,genre_id) VALUES (3,9);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Iceman','2012-08-30','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/42604_c.jpg&656&w','Michaell Shannon','https://www.youtube.com/embed/vHZ6dxR2EiQ');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (4,4);".
   		"insert into movie_genres(movie_id,genre_id) VALUES (4,7);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Maczeta zabija','2010-10-18','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43022_c.jpg&656&w','Danny Trejo, Alexa Vega','https://www.youtube.com/embed/_JW82VYA4E0');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (5,1);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Mundial. Gra o wszystko','2014-02-28','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/42876_c.jpg&656&w','Zbigniew Boniek, Dariusz Szpakowski','https://www.youtube.com/embed/tRnel-jT0t0');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (6,3);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Pianista','2002-05-24','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/240_c.jpg&656&w','Adrien Brody, Emilia Fox','https://www.youtube.com/embed/itR0-I9idXk');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (7,7);".
   		"insert into movie_genres(movie_id,genre_id) VALUES (7,9);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Transformers','2014-06-27','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43647_c.jpg&656&w','Mark Wahlberg, Stanley Tucci','https://www.youtube.com/embed/b2HCOJMa6gU');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (8,1);".
       		"insert into movie_genres(movie_id,genre_id) VALUES (8,5);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Dom bardzo nawiedzony','2013-01-11','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/42487_c.jpg&656&w','Marion Wayans, Essence Atkins','https://www.youtube.com/embed/rNO_-PmEYts');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (9,2);".
        	"insert into movie_genres(movie_id,genre_id) VALUES (9,8);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Grand Hotel Budapest','2014-03-28','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43353_c.jpg&656&w','Ralph Fiennes, Edward Norton','https://www.youtube.com/embed/1Fg5iWmQjwk');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (10,2);".
		"insert into movie_genres(movie_id,genre_id) VALUES (10,9);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Moj Nikifor','2004-09-16','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/3139_c.jpg&656&w','Krystyna Feldman, Roman Gancarczyk','https://www.youtube.com/embed/oFuI3HhINok');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (11,7);".
   		"insert into movie_genres(movie_id,genre_id) VALUES (11,9);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Kamienie na szaniec','2014-03-07','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43619_c.jpg&656&w','Kamil Szeptycki, Marcel Sabat','https://www.youtube.com/embed/Vpn9QLMk9Jw');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (12,9);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Wszystkie kobiety Mateusza','2012-09-15','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43039_c.jpg&656&w','Krzystof Globisz, Agata Kulesz','https://www.youtube.com/embed/HF63yiG2zxM');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (13,2);".
   		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Papusza','2013-11-15','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/42978_c.jpg&656&w','Jowita Budnik','https://www.youtube.com/embed/TLzwF6qDUow');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (14,7);".
		"insert into movie_genres(movie_id,genre_id) VALUES (14,9);".
		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Fast and Furious 7','2015-04-10','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/42934_c.jpg&656&w','Vin Disel, Paul Walker','https://www.youtube.com/embed/e2QxvySE94g');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (15,1);".
		"insert into movies(title, premiereDate, thumbnailUrl, movieActor, trailerUrl) VALUES ('Kumba','2013-10-11','http://www.canal-plus.pl/thumbnails/www.cyfraplus.pl/ms_galeria/fotobase/43295_c.jpg&656&w','Zebra Kumba','https://www.youtube.com/embed/fnD4sZuOU4I');".
   		"insert into movie_genres(movie_id,genre_id) VALUES (16,6);".
   		"CREATE TABLE users (id serial PRIMARY KEY, username VARCHAR (80) NOT NULL, password VARCHAR (80) NOT NULL);".
   		"insert into users (username, password) VALUES ('grabowski', '142682');".
   		"CREATE TABLE borrows (id serial PRIMARY KEY, movie_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NOT NULL, borrow_date TIMESTAMP NOT NULL, return_date TIMESTAMP, FOREIGN KEY(movie_id) REFERENCES movies(id), FOREIGN KEY(user_id) REFERENCES users(id));";
 		"CREATE TABLE reviews (id serial PRIMARY KEY, movie_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NOT NULL, stars SMALLINT, title TEXT, text TEXT, date DATE,  FOREIGN KEY(movie_id) REFERENCES movies(id));".
   
   
   	$queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $all_queries); 
   	foreach ($queries as $query){ 
      		mysqli_query($this->conn, $query);
   	} 
       }
   	
       private $conn;
   
       private function __construct() {
   	$servername = "sbazy.uek.krakow.pl";
   	$username = "s142682";
   	$password = "W4q5ThDX";
           $database = "s142682";
   
   	$this->conn = mysqli_connect($servername, $username, $password, $database);
 //  	$this->createDbTables();
   	if (!$this->conn) {
   	    die('<div class="alert alert-danger" role="alert">Connection failed: '.mysqli_connect_error().'</div>');
   	}
       }	
   }
   
   /**
    * @Entity
    */
   class MovieEntity
   {
          /**
        	* @ORM\Id()
        	* @ORM\Column(type="integer")
        	* @ORM\GeneratedValue(strategy="AUTO")
        	* @var int
        	*/
       	private $id;
   	public function getId() {
   		return $this->id;
   	}
   	
   	private $title;
   	public function getTitle() {
   		return $this->title;
   	}
   	
          /** 
   	*  @ORM\Column(type="datetime")
           *  @var datetime
           */
           private $premiereDate;
    
   	public function getPremiereDate() {
   		    {
					echo 'DATA PREMIERY: ';
				}
			return $this->premiereDate;
   	}
   
   	private $movieActor;
   
   	public function getMovieActor() {
             {
					echo 'W rolach głównych: ';
				}
            return $this->movieActor;
   	}   
   	
	private $trailerUrl;
   
   	public function getTrailerUrl() {
   		return $this->trailerUrl;
   	}
   
          /**
           * @ORM\Column(type="string", length=255)
           * @var string
           */
   	private $thumbnailUrl;
   	public function getThumbnailUrl() {
   		return $this->thumbnailUrl;
   	}
   
   	public function __construct($id, $title, $premiereDate, $thumbnailUrl, $movieActor, $trailerUrl) {
   		$this->id = $id;
   		$this->title = $title;
   		$this->premiereDate = $premiereDate;
   		$this->thumbnailUrl = $thumbnailUrl;
   		$this->movieActor = $movieActor;
   		$this->trailerUrl = $trailerUrl;
   	}
   
   	public function __toString() {
           	return $this->title;
       	}
   }
   
   /**
    * @Entity
    */
   class UserEntity
   {
   	/**
   	* @ORM\Id()
   	* @ORM\Column(type="integer")
   	* @ORM\GeneratedValue(strategy="AUTO")
   	* @var int
   	*/
   	private $id;
   	public function getId() {
   		return $this->id;
   	}
   	
   	private $username;
   	public function getUsername() {
   		return $this->username;
   	}
   	
   	private $password;
   	public function getPassword() {
   		return $this->password;
   	}
   	
   	public function __construct($id, $username, $password) {
   		$this->id = $id;
   		$this->username = $username;
   		$this->password = $password;
   	}
   }
   ?>
