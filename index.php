<?php
set_time_limit(240);

include './RatingCalculator.php';

//Yeah... I know...
$connection = mysql_connect('localhost', 'root', '855220');
mysql_select_db('cloudcart');

$ratings = new RatingCalculator();
$ratings->init();

if (isset($_POST['mail'])) {
    $user = $ratings->getUserByMail($_POST['mail']);
} else if (isset($_POST['all'])) {
    $grouped = $ratings->getGroupedUsers();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Simple UI</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <h1>Please choose an option</h1>
            <form class="form-horizontal col-md-6" method="post">
                <fieldset>
                    <legend>Get Rating by Email</legend>
                    <div class="form-group">
                        <div class="col-md-9">
                            <input class="form-control" id="inputEmail" name="mail" placeholder="Email" type="email">
                        </div>
                        <div class="col-xs-3">
                            <input type="submit" class="btn btn-md btn-primary" value="Search!">
                        </div>
                    </div>
                </fieldset>
            </form>
            <form class="form-horizontal col-md-6" method="post">
                <fieldset>
                    <legend>Get Ratings by Year's Quarters</legend>
                    <div class="form-group">
                        <div class="col-xs-12 text-center">
                            <input type="submit" class="btn btn-md btn-primary" name="all" value="Get 'em!">
                        </div>
                    </div>
                </fieldset>
            </form>
            <div class="col-xs-12">
                <?php if (isset($user)): ?>
                    <h2><?php echo $user['username']; ?></h2>
                    <h4>Rating: <?php echo $user['rating']; ?></h4>
                    <h3>Criteria:</h3>
                    <stron>Points from country: </stron> <?php echo $user['pointsCountry']; ?><br>
                    <stron>Points from id: </stron> <?php echo $user['pointsId']; ?><br>
                    <stron>Points' multiplier from year's quarter: </stron> <?php echo $user['pointsYearQuarter']; ?><br>
                    <stron>Points reduction: </stron> <?php echo $user['pointsAverage']; ?><br>
                <?php endif; ?>
                <?php if (isset($grouped)): ?>
                    <?php foreach ($grouped as $key => $quarter): ?>
                        <h2><?php echo ucfirst($key); ?> Quarter Users</h2>
                        <div class="col-md-6">
                            <?php foreach ($quarter as $id => $user): ?>
                                <div><?php echo $user['username']; ?> <em>Rating: <?php echo $user['rating']; ?></em></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body>
</html>