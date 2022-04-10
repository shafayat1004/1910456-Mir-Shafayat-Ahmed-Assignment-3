<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$dhaka = ['23.8103', '90.4125'];

$city = $dhaka;
$url  = 'https://api.openweathermap.org/data/2.5/onecall?lat='.$city[0].'&lon='.$city[1].'&exclude=hourly,alerts,minutely&units=metric&appid='.$_ENV["API_KEY"];
 
$contents = file_get_contents($url);
$climate = json_decode($contents);
// echo $contents;
$current = $climate->current;
$nextFiveDay = array_slice($climate->daily, 0, 6);
date_default_timezone_set('Asia/Dhaka');
// var_dump($nextFiveDay);
// print("<pre>".print_r($current,true)."</pre>");
// print("<pre>".print_r($nextFiveDay,true)."</pre>");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather</title>
    <link rel="stylesheet" href="static/css/bootstrap.css">
    <link rel="stylesheet" href="static/css/my.css">
    <link rel="stylesheet" href="static/css/projects.css">
</head>
<body>
    <img src="http://openweathermap.org/img/w/<?php echo $current->weather[0]->icon ?>.png" alt="">
    <img src="http://openweathermap.org/img/w/<?php echo $nextFiveDay[0]->weather[0]->icon ?>.png" alt="">

    <section class="card project">
      <div class="card-header">
        <h3>5 Day Forecast</h3>
        <div class="btn-group">
          <button class="btn" type="button" data-bs-target="#project-1" data-bs-slide="prev"><img src="static/images/previous.svg" alt=""></button>
          <button class="btn" type="button" data-bs-target="#project-1" data-bs-slide="next"><img src="static/images/next.svg" alt=""></button>
        </div>
      </div>
      <hr>
      <div class="card-content">
        <div id="project-1" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php 
              foreach (array_values($nextFiveDay) as $i => $day):
                $emojiWeather = "http://openweathermap.org/img/w/".$day->weather[0]->icon.".png";
            ?>
            <div class="carousel-item <?php if ($i === 0){echo "active";}?>">
                <h4 class="text-center">
                    <?php 
                        $date = new DateTime();
                        $date->setTimestamp($time=$day->dt);
                        if ($i === 0){echo "Today: ";}
                        echo gmdate('d M Y ', strtotime($date->format('Y-m-d H:i:s'))); 
                        if ($i === 0){echo $current->temp."&deg C";}
                    ?>
                    <img class="inline-block" src=<?php echo $emojiWeather ?> class="d-block w-100" alt="...">
                </h4>
                <hr>
                <?php
                    $sunrise = new DateTime();
                    $sunrise->setTimestamp( $time=$day->sunrise );
                    $sunset = new DateTime();
                    $sunset->setTimestamp( $time=$day->sunset );  
                    echo "Sunrise: ".$sunrise->format('h:i a')."<br>";
                    echo "Sunset: ".$sunset->format('h:i a')."<br>";
                    echo "Max: ".$day->temp->max."&deg C<br>";
                    echo "Min: ".$day->temp->min."&deg C<br>";
                    ?>
            </div>
            <?php endforeach ?>
          </div>
        </div>
      </div>
    </section>




    <script src="bootstrap.js"></script>
    <script src="main.js"></script>
    <!-- <script>
        let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        window.location.href=weather.php?offset=timezone;
    </script> -->
</body>
</html>