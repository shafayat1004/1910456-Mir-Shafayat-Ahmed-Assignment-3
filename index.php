<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$dhaka = '{"lat":23.8103,"long":90.4125}';
$chittagong = '{"lat":22.3569,"long":91.7832}';
$sylhet = '{"lat":24.8949,"long":91.8687}';



$city = json_decode($_GET["location"]??$dhaka);
$url  = 'https://api.openweathermap.org/data/2.5/onecall?lat='.$city->lat.'&lon='.$city->long.'&exclude=hourly,alerts,minutely&units=metric&appid='.$_ENV["API_KEY"];
$contents = file_get_contents($url);
$climate = json_decode($contents);

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
    <script src="chart.js"></script>
</head>
<body>
    <video autoplay muted loop id="myVideo">
        <source src="static/videos/<?php 
          switch ($current->weather[0]->main) {
            case 'Clear':
              echo "clear_sky";
              break;
            case 'Clouds':
              echo "scattered_clouds";
              break;
            case 'Rain':
              echo "rain";
              break;
            case 'Thunderstorm':
              echo "thunderstorm";
              break;
            case 'Snow':
              echo "snow";
              break;
            case 'Mist':
              echo "mist";
              break;
            case 'Haze':
              echo "haze";
              break;
            
            default:
              echo "clear_sky";
              break;
          }
        ?>.mp4" type="video/mp4">
    </video>
    <section>
        <form id="location" action="index.php" method="get">
            <select class="form-select" aria-label="" name="location">
                <option value=''>Your Location</option>
                <option <?php if($_GET["location"] === $dhaka){echo "selected";}?> value='<?php echo $dhaka ?>'>Dhaka</option>
                <option <?php if($_GET["location"] === $chittagong){echo "selected";} ?> value='<?php echo $chittagong ?>'>Chittagong</option>
                <option <?php if($_GET["location"] === $sylhet){echo "selected";} ?> value='<?php echo $sylhet ?>'>Sylhet</option>
              </select>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>   
    </section>

    <section>
        <nav class="navbar sticky-top navbar-expand-lg navbar-light">
            <div class="container-fluid">
              <a class="navbar-brand justify-content-center" href="#">5 Day Forecast</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Nav tabs -->
                <ul class="nav nav-pills" id="myTabs" role="tablist">
                  <?php 
                    foreach (array_values($nextFiveDay) as $i => $day):
                      $emojiWeather = "http://openweathermap.org/img/w/".$day->weather[0]->icon.".png";
                  ?>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link <?php if ($i === 0){echo "active";}?>" id="day-<?php echo $i ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo "day-".$i ?>" type="button" role="tab" aria-controls="<?php echo "day-".$i ?>" aria-selected="true">
                      
                      <img src="<?php echo $emojiWeather ?>" alt="">
                      <?php
                        $date = new DateTime();
                        $date->setTimestamp($time=$day->dt);
                        echo gmdate('D', strtotime($date->format('Y-m-d H:i:s')));
                        echo "<br>";
                        echo gmdate('M d', strtotime($date->format('Y-m-d H:i:s')));
                      ?> 
                    </button>
                  </li>
                  <?php endforeach ?>
                </ul>
              </div>
            </div>
          </nav>
    </section>
    <section>
        <div class="tab-content">
          <?php 
            foreach (array_values($nextFiveDay) as $i => $day):
            $emojiWeather = "http://openweathermap.org/img/w/".$day->weather[0]->icon.".png";
          ?>     
          <div class="tab-pane <?php if ($i === 0){echo "active";}?>" id="day-<?php echo $i ?>" role="tabpanel" aria-labelledby="day-<?php echo $i ?>-tab">
            <?php if ($i === 0): ?>
              <div id="currentTemp">
                <h1>
                  <?php 
                    echo $current->temp."&deg C<br>"; 
                    echo $current->weather[0]->description;   
                  ?>
                </h1>
              </div>
            <?php endif ?>          
            <div class="detailedTemp">
              <div class="sunTimes">
                <div class="text">
                  <?php
                      $sunrise = new DateTime();
                      $sunrise->setTimestamp( $time=$day->sunrise );
                      $sunset = new DateTime();
                      $sunset->setTimestamp( $time=$day->sunset );
                      echo "Sunrise: ".$sunrise->format('h:i a')."<br>";
                      echo "Sunset: ".$sunset->format('h:i a')."<br>";
                  ?>
                </div>
              </div>
              <div class="moonTimes">
                <div class="text">
                  <?php
                      $moonrise = new DateTime();
                      $moonrise->setTimestamp( $time=$day->moonrise );
                      $moonset = new DateTime();
                      $moonset->setTimestamp( $time=$day->moonset );
                      echo "Moonrise: ".$moonrise->format('h:i a')."<br>";
                      echo "Moonset: ".$moonset->format('h:i a')."<br>";
                      echo "Moon Phase: ".$day->moon_phase."<br>";
                  ?>
                </div>
              </div>
              <div class="temperatures">
                <div class="chartDiv">
                  <canvas id="tempChangeChart<?php echo $i ?>"></canvas>
                </div>
                <div class="text">
                  <?php
                    echo "Max: ".$day->temp->max."&deg C<br>";
                    echo "Min: ".$day->temp->min."&deg C<br>";
                  ?>
                </div>
              </div>
              <div class="feelsLike">
                <div class="text">
                  <?php
                    echo "Day: ".$day->feels_like->day."&deg C<br>";
                    echo "Night: ".$day->feels_like->night."&deg C<br>";
                    echo "Evening: ".$day->feels_like->eve."&deg C<br>";
                    echo "Morning: ".$day->feels_like->morn."&deg C<br>";
                  ?>
                </div>
              </div>
              <div class="wind">
                <img style="transform: rotate(
                    <?php 
                      $wind_deg = ($i===0) ? $current->wind_deg : $day->wind_deg;
                      echo $wind_deg;
                    ?>deg
                  )" src="static/images/arrow.svg" alt="">
                <div class="text">
                  <?php
                    $wind_speed = ($i===0) ? $current->wind_speed : $day->wind_speed;
                    echo "Wind Speed: ".$wind_speed." m/s<br>";
                    echo "Wind Direction: ".$wind_deg."&deg<br>";
                    echo "Wind Gust: ".$day->wind_gust." m/s<br>";
                    ?>
                </div>
              </div>
              <div class="atmosphere">
                <div class="text">
                  <?php
                    echo "Pressure: ".$day->pressure." hPa<br>";
                    echo "Humidity: ".$day->humidity."%<br>";
                    echo "Dew Point: ".$day->dew_point."&deg<br>";
                    $pop = $day->pop * 100;
                    echo "Probability of Precipitation: ".$pop."%<br>";
                    $clouds = ($i===0)?$current->clouds:$day->clouds;
                    echo "Clouds: ".$clouds."%<br>";
                    if($day->rain){echo "Rain: ".$day->rain." mm<br>";}
                    if($day->snow){echo "Snow: ".$day->snow." mm<br>";}
                  ?>
                </div>
              </div>
            </div>
            <script>
              let x<?php echo $i ?>Values = ["Morning", "Day", "Evening", "Night"];
              let y<?php echo $i ?>Values = [<?php echo $day->temp->morn.','.$day->temp->day.','.$day->temp->eve.','.$day->temp->night ?>];
              Chart.defaults.font.size = 20;
              Chart.defaults.font.weight = 800;
              Chart.defaults.color = "rgba(3,3,3,0.8)";
              Chart.defaults.scale.grid.color = "rgba(3,3,3,0.8)"; 
              new Chart("tempChangeChart<?php echo $i ?>", {
                type: "line",
                data: {
                  labels: x<?php echo $i ?>Values,
                  datasets: [{
                    data: y<?php echo $i ?>Values,
                    fill: false,
                    backgroundColor: "rgba(0,0,0,1.0)",
                    borderColor: "rgba(0,0,0,0.5)",
                    cubicInterpolationMode: 'monotone',
                    tension: 0.4
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false, 
                  plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                      display: true,
                    },
                  },
                  interaction: {
                    intersect: false,
                  },
                  scales: {
                    x: [{
                      display: true,
                      grid: {
                        color: "#FFFFFF"
                      }, 
                      title: {
                        display: true
                      }, 
                    }],
                    y: [{
                      display: true,
                      grid: {
                        color: "#FFFFFF"
                      },
                      title: {
                        display: true,
                        text: 'Temperature'
                      },
                      suggestedMin: <?php echo $day->temp->min ?>,
                      suggestedMax: <?php echo $day->temp->max ?>,
                    }]
                  }
                }
              });
            </script> 
          </div>
          <?php endforeach ?>
            
        </div>
    </section>
    <script src="bootstrap.js"></script>
    <script src="main.js"></script>
</body>
</html>