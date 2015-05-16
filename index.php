<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dāvis Krēgers &mdash; REGRESIJAS UZDEVUMS</title>
	<style>
		body {
			padding-top: 50px;
		}
		nav {
			width: 100%;
			background: #cdcdcd;
			position: fixed;
			height: 50px;
			margin-top: -50px;
		}
		nav ul li {
			display: inline;
			margin-left: 20px;
		}
		nav ul li a {
			color: #000;
			font-weight: bold;
			text-decoration: none;
		}
		.graph-container {
			max-width: 400px;
			height: 400px;
			margin: auto;
		}
	</style>
</head>
<body>


<?php 

require 'helpers.php';
require 'rand.class.php';
require 'algoritms.class.php';

$rand = new RND_Skaitlis();

// /* Sākuma mainīgie */

$funkciju_kopa = ['+', '-', '*', '/', 'sin', 'cos', 'exp'];
$populacijas_izmers = 6;

/* 	Piemerotiba tiek aprēķināta kā vidējā summārā absolūtā kļūda, salīdzinot ĢP iegūtā koka rezultātu 
	ar esošām f(x) realizācijām. Jo mazāka kļūda, jo labāks ir rezultāts */



$sakuma_populacija = array(
array ( 'value' => '/', 'children' => array ( 0 => array ( 'value' => 'cos', 'children' => array ( 0 => array ( 'value' => 'X', ), ), ), 1 => array ( 'value' => 'cos', 'children' => array ( 0 => array ( 'value' => 'X', ), ), ), ), ),
array ( 'value' => '-', 'children' => array ( 0 => array ( 'value' => 'cos', 'children' => array ( 0 => array ( 'value' => '+', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => 'X', ), ), ), ), ), 1 => array ( 'value' => '-', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => 'X', ), ), ), ), ),
array ( 'value' => '/', 'children' => array ( 0 => array ( 'value' => 'exp', 'children' => array ( 0 => array ( 'value' => 'X', ), ), ), 1 => array ( 'value' => 'cos', 'children' => array ( 0 => array ( 'value' => '/', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => 'X', ), ), ), ), ), ), ),
array ( 'value' => 'exp', 'children' => array ( 0 => array ( 'value' => '-', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => 'X', ), ), ), ), ),
array ( 'value' => '-', 'children' => array ( 0 => array ( 'value' => '-', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => '*', 'children' => array ( 0 => array ( 'value' => 'cos', 'children' => array ( 0 => array ( 'value' => 'X', ), ), ), 1 => array ( 'value' => 'X', ), ), ), ), ), 1 => array ( 'value' => '*', 'children' => array ( 0 => array ( 'value' => '*', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => 'X', ), ), ), 1 => array ( 'value' => 'X', ), ), ), ), ),
array ( 'value' => '*', 'children' => array ( 0 => array ( 'value' => 'cos', 'children' => array ( 0 => array ( 'value' => 'sin', 'children' => array ( 0 => array ( 'value' => 'X', ), ), ), ), ), 1 => array ( 'value' => '+', 'children' => array ( 0 => array ( 'value' => 'X', ), 1 => array ( 'value' => 'X', ), ), ), ), ),
);

$realizacijas = array(
	array(3.961023029, 29.1774403, 118.274924, 340.7891595, 817.0718568, 1482.127068, 2806.756661, 4471.923127, 7087.731604, 11553.62322, 16595.18774, 22858.27617, 30442.53232, 39479.21662, 52988.61082, 71766.13841, 87777.15924, 109816.4793, 139721.6756, 163602.5411),
	array(3.928025223, 31.36989803, 118.7383531, 342.5846994, 808.7307667, 1573.974988, 2849.444181, 4838.002927, 7645.608295, 10901.75991, 15668.04699, 22592.0378, 32078.2808, 41639.63916, 56240.86242, 68357.61102, 90957.65047, 113473.5538, 141933.2742, 163613.3819),
	array(4.083716778, 28.80335294, 122.4385248, 347.6313487, 764.2005198, 1556.643895, 2777.002764, 4883.620151, 7703.218195, 10781.46695, 15605.25556, 21797.16405, 32004.02256, 42334.9996, 56322.53617, 69925.24869, 91836.02358, 107355.8199, 132581.665, 166663.9489)
);


$generacijas = 100;

$parametri = array(
	'selekcija' => 'turnīrs',
	'mutacijas_varbutiba' => 0,
	'sakuma_populacija' => $sakuma_populacija,
	'generacijas' => $generacijas,
	'funkciju_kopa' => $funkciju_kopa,
	'realizacijas' => $realizacijas
);
?>

<!-- <nav>
	<ul>
		<?php for($i = 0; $i <= $generacijas; $i++): ?>
			<li><a href="#generacija-<?php echo $i; ?>"><?php echo $i+1; ?>. ģenerācija</a></li>
		<?php endfor; ?>
	</ul>
</nav> -->

<?php $algoritms = new Algoritms($parametri); ?>

</body>
</html>