<?php

/**
 * Vairāku mērķu optimizācijas uzdevuma algoritms
 *
 * PHP version 5
 *
 * @author     Dāvis Krēgers <davis@image.lv>
 * @copyright  2015 Dāvis Krēgers
 * @license    https://creativecommons.org/publicdomain/zero/1.0/ CC0 1.0 Universal (CC0 1.0) 
 * @version    SVN: $Id$
 * @link       http://faili.deiveris.lv/genetiskais-algoritms1/
 */

require_once 'rand.class.php';

Class Algoritms {
	private $sakuma_populacija,
			$populacija, $berni,
			$mutacijas_varbutiba, 
			$intervals, $precizitate, $max_vertiba, $max_vertiba_binary, $dalijuma_skaitlis, $populacijas_info,
			$selekcija, $generacijas, $individiem_intervali, $rand, $krustosanas_intervali, $krustosanas_pari,
			$mutacijas_elementi, $mutacijas_genu_intervali, $selekcijas_elementi, $selekcijas_intervali, $selekcijas_elementi_rezultats,
			$distancu_matrica, $pilsetas, $p_keys, $realizacijas;

	public function __construct($options) {
		foreach($options as $key => $option) {
			$this->$key = $option;
		}
		$this->populacijas_info = array('sum' => 0,'max' => array('key' => 0, 'val' => 0),'avg' => 0);
		$this->krustosanas_intervali = array();

		$this->selekcija = 'turnirs';

		$this->rand = new RND_Skaitlis();
		$this->loop();

	}

	protected function loop() {

		for($i = 0; $i < $this->generacijas; $i++) {
			
			if($i != 0) echo "<h1 id=\"generacija-".$i."\">".($i+1).". Ģenerācija</h1>";
			else {
				?>
				<h1>Sākums</h1>
				<!-- <p>Intervāls: [<?php echo $this->intervals[0]; ?>; <?php echo $this->intervals[1]; ?>]</p>
				<p>Precizitāte: <?php echo $this->precizitate; ?></p>
				<p>MAX vērtība: <?php echo $this->max_vertiba; ?> => <?php echo $this->max_vertiba_binary; ?></p>
				<p>Dalījumu skaits: <?php echo $this->dalijuma_skaitlis; ?></p>
				<p>Sākuma vērtības populācijai: <?php echo count($this->sakuma_populacija); ?></p> -->

				<?php
				echo $this->fx_realizacijas();
				?>


				<h1 id="generacija-0">Sākuma populācija</h1>
				<?php
			}

			if($i == 0) $this->populacija = $this->sakuma_populacija;

			$this->populacijas_piemerotiba();
			$this->populacijas_izvade();

			$this->individiem_aprekinatie_intervali();
			$this->individiem_aprekinatie_intervali_izvade();

			// if($this->selekcija == 'turnirs') $this->turnirs_paru_veidosana();
			// else $this->rulete_paru_veidosana();

			$this->turnirs_paru_veidosana();
			$this->individu_krustosana();

			$this->jaunas_paaudzes_selekcija();
			$this->jaunas_paaudzes_selekcija_intervali();

			if($this->selekcija == 'turnirs') $this->turnirs_jaunas_paaudzes_selekcija();
			else $this->rulete_jaunas_paaudzes_selekcija();

			$this->jauna_paaudze();



		}

		echo "<h1 id=\"generacija-".($i)."\">".($i+1).". Ģenerācija</h1>";
		$this->populacijas_piemerotiba();
		$this->populacijas_izvade();

	}

	protected function fx_realizacijas() {
		?>
		<h1>Funkcijas f(x) realizācijas:</h1>
		<table border=1>
			<tr>
				<th>X=</th>
				<?php for($i = 1; $i <= 20; $i++): ?>
					<th><?php echo $i ?></th>
				<?php endfor ?>
			</tr>
			<?php for($i = 0; $i < count($this->realizacijas); $i++): ?>
				<tr>
					<td><?php echo $i ?></td>
					<?php for($j = 0; $j < count($this->realizacijas[$i]); $j++): ?>
						<td><?php echo $this->realizacijas[$i][$j] ?></td>
					<?php endfor; ?>
				</tr>
			<?php endfor; ?>
		</table>
		<?php
	}

	protected function jauna_paaudze() {
		$paaudze = array();
		foreach($this->selekcijas_elementi_rezultats as $key => $val) {
			$paaudze[] = $this->selekcijas_elementi[$val[1]];
		}
		$this->berni = array();
		$this->populacija = $paaudze;

	}

	protected function turnirs_jaunas_paaudzes_selekcija() {
		?>

			<h5>Turnīra selekcija jaunajai ģenerācijai</h5>
			<table border="1">
				<tr>
					<th>Gadījuma skaitlis no tabulas</th>
					<th>Izvēlētie indivīdi un piemērotība</th>
					<th>Uzvarētājs</th>
				</tr>
				
				<?php 
				$c = 0; $ParuVeidosana = array();
				for($i = 1; $i <= count($this->selekcijas_elementi); $i++) {
					$randVal = floatval($this->rand->generate()); $randKeys = array_keys($this->selekcijas_intervali); $randEl = 0;
					for($j = 0; $j < count($this->selekcijas_intervali); $j++) {
						if($randVal < floatval($randKeys[$j])) {
							$randKeys[$j];
							if($j > 0) $randEl = $this->selekcijas_intervali[$randKeys[$j-1]];
							else $randEl = $this->selekcijas_intervali[$randKeys[0]];
							break;
						}
					}
					$ParuVeidosana[] = array($randVal, $randEl);
				}

				$KrustosanasPari = $ParuVeidosana;
				for($i = 0; $i < count($ParuVeidosana); $i++):  ?>
				<tr>
					<td><?php echo $ParuVeidosana[$i][0]; ?></td>
					<td><?php echo ($ParuVeidosana[$i][1]+1). " (".skaitlis($this->selekcijas_elementi[$ParuVeidosana[$i][1]]['piemerotiba']).")"; ?></td>

					<?php if($i % 2 == 0): ?>
						<?php if($this->selekcijas_elementi[$ParuVeidosana[$i][1]]['piemerotiba'] < $this->selekcijas_elementi[$ParuVeidosana[$i+1][1]]['piemerotiba']): ?>
							<td rowspan=2><?php echo $ParuVeidosana[$i][1]+1; ?></td>
							<?php unset($KrustosanasPari[$i+1]); ?>
						<?php else: ?>
							<td rowspan=2><?php echo $ParuVeidosana[$i+1][1]+1; ?></td>
							<?php unset($KrustosanasPari[$i]); ?>
						<?php endif; ?>
					<?php endif; ?>

				</tr>
				<?php endfor; ?>


			</table>
		<?php
		$krustosana = array();
		foreach($KrustosanasPari as $val) { // atslegas neiet viena pec otras, reset
			$krustosana[] = $val;
		}
		$this->selekcijas_elementi_rezultats = $krustosana;
	}

	protected function jaunas_paaudzes_selekcija_intervali() {
		if($this->selekcija == 'turnirs'):
		?>

		<h5>Indivīdiem aprēķinātie intervāli</h5>

		<table border=1>
			<tr>
				<th>Indivīds</th>
				<th>Varbūtība tikt izvēlētam</th>
				<th>Kumulatīvā varbūtība</th>
				<th>Intervāls</th>
			</tr>
			<?php 
			$kumul = 0;
			for($i = 1; $i <= count($this->selekcijas_elementi); $i++):
				

				
					$probability = 1 / count($this->selekcijas_elementi);
					$prev = $kumul;
					$kumul += $probability;
					$slekc_int[''.$kumul] = $i;
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
					<?php

			endfor; ?>
		</table>
		<?php

		else:
			
			$piem_summa = 0;
			for ($i=0; $i < count($this->selekcijas_elementi); $i++) { 
				$piem_summa += $this->selekcijas_elementi[$i]['piemerotiba'];
			}

			$probability = $this->selekcijas_elementi[$i-1]['piemerotiba'] / $piem_summa;
			$kumul = 0;

			for($i = 1; $i <= count($this->selekcijas_elementi); $i++):
			
				$probability = 1 / count($this->selekcijas_elementi);
				$prev = $kumul;
				$kumul += $probability;
				$slekc_int[''.$kumul] = $i;

			endfor; 
		endif;

		$this->selekcijas_intervali = $slekc_int;
	}

	protected function jaunas_paaudzes_selekcija() {
		$this->populacijas_piemerotiba(true);
		?>
		<h4>Tekošā populācija</h4>

		<table border="1">
			<tr>
				<th>Indivīds</th>
				<th>Funkcija</th>
				<th>Piemērotība</th>
			</tr>
			<?php $i = 0;
			foreach($this->populacija as $key => $val): $i++; $selekcija[] = $val; $i++; ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo render_tree($val); ?></td>
				<td><?php echo $val['piemerotiba']; ?></td>
			</tr>
			<?php endforeach; ?>
			<?php foreach($this->berni as $key => $val): $i++; ?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo render_tree($val); ?></td>
					<td><?php echo $val['piemerotiba']; ?></td>
				</tr>
				<?php 
				 $selekcija[] = $val;
			endforeach; ?>
		</table>

		<?php
		$this->selekcijas_elementi = $selekcija;
	}

	protected function rnd_int_piemeklesana($rnd, $not_allowed = -1) {
		$randKeys = array_keys($this->mutacijas_genu_intervali);
		for($j = 0; $j < count($this->mutacijas_genu_intervali); $j++) {
			if($rnd < floatval($randKeys[$j])) {

				$randKeys[$j];
				if($j > 0) $gens = $this->mutacijas_genu_intervali[$randKeys[$j-1]];
				else $gens = $this->mutacijas_genu_intervali[$randKeys[0]];

				if($gens == $not_allowed && $not_allowed != -1) {
					return $this->rnd_int_piemeklesana($this->rand->generate(), $rnd);
				}

				return $gens;
			}
		}
	}

	protected function individu_krustosana() {


		$kumul = 0; 
		$krustosana = $this->krustosanas_pari; $paris = array();

		$populacija = $this->populacija;
		for($i = 0; $i < count($populacija); $i++) {
			unset($populacija[$i]['abs']);
			unset($populacija[$i]['piemerotiba']);
			unset($populacija[$i]['calc']);
		}


		// 1. solis - tiek izvēlēts indivīds un noteikts iespējami krustošanās punktu skaits, kas ir vienāds ar zaru skaitu;

		$pari_krusosanai = (count($krustosana) % 2 > 0) ? (count($krustosana) - 1) / 2 : (count($krustosana)) / 2;
		for($i = 0; $i < $pari_krusosanai; $i++) {

			$dimensions1 = count_branches($populacija[$krustosana[2*$i][1]]); 
			$dimensions2 = count_branches($populacija[$krustosana[2*$i+1][1]]); 

			// 2. solis - Tiek ģenerēts gadījuma skaitlis

			$paris[$i] = array($this->rand->generate(), $this->rand->generate());

			// 3. solis - Zaru skaits tiek reizināts ar gadījuma skaitli un noapaļots uz augšu līdz veselam skaitlim, kas arī būs krustošanās punkts

			$paris[$i][2] = ceil($paris[$i][0] * $dimensions1);
			$paris[$i][3] = ceil($paris[$i][1] * $dimensions2);

			// Krustojam

			$paris[$i][4] = koka_krustosana($paris[$i][2], $paris[$i][3], $populacija[$krustosana[2*$i][1]], $populacija[$krustosana[2*$i+1][1]]);

			$this->berni[] = $paris[$i][4][0];
			$this->berni[] = $paris[$i][4][1];

		}

		?>
		<h4>Indivīdu krustošana</h4>
		<table border=1>
			<tr>
				<th rowspan=2>Pāris</th>
				<th rowspan=2>Indivīdi</th>
				<th colspan=4>Krustošanās punkti</th>
			</tr>
			<tr>
				<th>G.sk.</th>
				<th>1. punkts</th>
				<th>G.sk.</th>
				<th>2. punkts</th>
			</tr>
			<?php 
			$c = 0;
			for($i = 0; $i < $pari_krusosanai * 2; $i++): ?>
				<tr>
					<?php if($i % 2 == 0): $c++;  ?>
						<td rowspan=2><?php echo $c; ?></td>
					<?php endif; ?>

					<td>
						<?php echo $krustosana[$i][1] +1; ?>
					</td>

					<?php if($i % 2 == 0): ?>

						<td rowspan=2><?php echo $paris[$c-1][0]; ?></td>
						<?php if($paris[$c-1][2] != '-'): ?><td rowspan=2><?php echo $paris[$c-1][2]; ?></td>
						<?php else: ?><td rowspan=2>-</td><?php endif; ?>

						<td rowspan=2><?php echo $paris[$c-1][1]; ?></td>
						<?php if($paris[$c-1][3] != '-'): ?><td rowspan=2><?php echo $paris[$c-1][3]; ?></td>
						<?php else: ?><td rowspan=2>-</td><?php endif; ?>

					<?php endif; ?>
				</tr>
			<?php endfor; ?>
		</table>
		<h3>Krustošanās rezultāts</h3>

			<?php for($i = 0; $i < count($paris); $i++): 

				require_once('./test/classes/GDRenderer.php');

				echo "<br>";
				echo render_tree($paris[$i][4][0])."<br>";
				echo render_tree($paris[$i][4][1])."<br>";
				
				?>

				<!-- <img src='./image.php?img=<?php echo json_encode(serialize($this->populacija[$krustosana[2*$i][1]])); ?>' alt=""> -->
				<img src='./image.php?img=<?php echo json_encode(serialize($paris[$i][4][0])); ?>' alt=""><br />
				<!-- <img src='./image.php?img=<?php echo json_encode(serialize($this->populacija[$krustosana[2*$i+1][1]])); ?>' alt=""> -->
				<img src='./image.php?img=<?php echo json_encode(serialize($paris[$i][4][1])); ?>' alt="">
				
			<?php endfor; ?>

		<?php
	}

	protected function turnirs_paru_veidosana() {
		?>
		<h4>Pāru Veidošana</h4>
		<table border="1">
			<tr>
				<th>Gadījuma skaitlis no tabulas</th>
				<th>Izvēlētie indivīdi un piemērotība</th>
				<th>Uzvarētājs</th>
				<th>Pāris</th>
			</tr>
			
			<?php 
			$c = 0; $ParuVeidosana = array();
			for($i = 1; $i <= 2*count($this->populacija); $i++) {
				$randVal = floatval($this->rand->generate()); $randKeys = array_keys($this->individiem_intervali); $randEl = 0;
				for($j = 0; $j < count($this->individiem_intervali); $j++) {
					if($randVal < floatval($randKeys[$j])) {
						$randKeys[$j];
						if($j > 0) $randEl = $this->individiem_intervali[$randKeys[$j-1]];
						else $randEl = $this->individiem_intervali[$randKeys[0]];
						break;
					}
				}
				$ParuVeidosana[] = array($randVal, $randEl);
			}

			$KrustosanasPari = $ParuVeidosana;
			for($i = 0; $i < count($ParuVeidosana); $i++):  ?>
			<tr>
				<td><?php echo $ParuVeidosana[$i][0]; ?></td>
				<td><?php echo ($ParuVeidosana[$i][1]+1). " (".skaitlis($this->populacija[$ParuVeidosana[$i][1]]['piemerotiba']).")"; ?></td>

				<?php if($i % 2 == 0): ?>
					<?php if($this->populacija[$ParuVeidosana[$i][1]]['piemerotiba'] < $this->populacija[$ParuVeidosana[$i+1][1]]['piemerotiba']): ?>
						<td rowspan=2><?php echo $ParuVeidosana[$i][1]+1; ?></td>
						<?php unset($KrustosanasPari[$i+1]); ?>
					<?php else: ?>
						<td rowspan=2><?php echo $ParuVeidosana[$i+1][1]+1; ?></td>
						<?php unset($KrustosanasPari[$i]); ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php if($i % 4 == 0 && $i != count($ParuVeidosana)): $c++;?>
					<td rowspan=4><?php echo $c; ?></td>
				<?php endif; ?>
			</tr>
			<?php endfor; ?>
		</table>
		<?php
		$krustosana = array();
		foreach($KrustosanasPari as $val) { // atslegas neiet viena pec otras, reset
			$krustosana[] = $val;
		}
		$this->krustosanas_pari = $krustosana;

	}

	protected function individiem_aprekinatie_intervali() {
		$kumul = 0;
		for($i = 1; $i <= count($this->populacija); $i++) {
			if($this->selekcija == 'turnirs'):
				$probability = 1 / count($this->populacija);
				$prev = $kumul;
				$kumul += $probability;
				$this->individiem_intervali[''.$kumul] = $i;
			else:
				$probability = $this->populacija[$i-1]['piemerotiba'] / $this->populacijas_info['sum'];
				$prev = $kumul;
				$kumul += $probability;
				$this->individiem_intervali[''.$kumul] = $i;
			endif;
		}
	}

	protected function individiem_aprekinatie_intervali_izvade() {
		?>
		<h4>Indivīdiem aprēķinātie intervāli</h4>

		<table border=1>
			<tr>
				<th>Indivīds</th>
				<th>Varbūtība tikt izvēlētam</th>
				<th>Kumulatīvā varbūtība</th>
				<th>Intervāls</th>
			</tr>
			<?php 
			$kumul = 0;
			for($i = 1; $i <= count($this->populacija); $i++):
				
				if($this->selekcija == 'turnirs'):
					$probability = 1 / count($this->populacija);
					$prev = $kumul;
					$kumul += $probability;
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
					<?php
				else:

					$probability = $this->populacija[$i-1]['piemerotiba'] / $this->populacijas_info['sum'];
					$prev = $kumul;
					$kumul += $probability;
					?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo prbsk($probability); ?></td>
						<td><?php echo prbsk($kumul); ?></td>
						<td>
							<?php 
								echo ($i == 0) ? 
								"[".prbsk($prev).";".prbsk($kumul)."]" : 
								"(".prbsk($prev).";".prbsk($kumul)."]"; 
							?>
						</td>
					</tr>
					<?php



				endif;

			endfor; ?>
		</table>
		<?php
	}

	protected function populacijas_piemerotiba($berni = false) {
		
		if($berni) {
			foreach($this->berni as $key => $individs) {
				
				$vid = 0; $calc = array(); $abs = array(); $sum = 0; $previous = 0;
				
				// Vertibas
				for($i = 1; $i <= 20; $i++) {
					$calc[$i-1] = calculate_tree($individs, $i);
				}

				// Abs. kļūdas
				for($i = 0; $i < count($this->realizacijas); $i++) {
					$abs[$i]['sum'] = 0;
					for($j = 0; $j < 20; $j++) {
						$abs[$i][$j] = abs(abs($calc[$j]) - abs($this->realizacijas[$i][$j]));
						$abs[$i]['sum'] += $abs[$i][$j];
					}
				}

				// Videja kļūda
				for($i = 0; $i < count($abs); $i++) {
					$vid += $abs[$i]['sum'];
				}


				$this->berni[$key]['calc'] = $calc; 
				$this->berni[$key]['abs'] = $abs; 
				$this->berni[$key]['piemerotiba'] = $vid; 
			}
		}
		else {
			$this->populacijas_info = false;
			$this->populacijas_info = array('sum' => 0,'max' => array('key' => 0, 'val' => false),'avg' => 0);
			foreach($this->populacija as $key => $individs) {
				
				$vid = 0; $calc = array(); $abs = array(); $sum = 0; $previous = 0; $sum = 0;
				
				// Vertibas
				for($i = 1; $i <= 20; $i++) {
					$calc[$i-1] = calculate_tree($individs, $i);
				}

				// Abs. kļūdas
				for($i = 0; $i < count($this->realizacijas); $i++) {
					$abs[$i]['sum'] = 0;
					for($j = 0; $j < 20; $j++) {
						$abs[$i][$j] = abs(abs($calc[$j]) - abs($this->realizacijas[$i][$j]));
						$abs[$i]['sum'] += $abs[$i][$j];
					}
				}

				?>

				<!-- <table border=1>
					<tr>
						<th>X=</th>
						<?php for($i = 1; $i <= 20; $i++): ?>
							<th><?php echo $i ?></th>
						<?php endfor ?>
					</tr>
					<?php for($i = 0; $i < count($abs); $i++): ?>
						<tr>
							<td><?php echo $i ?></td>
							<?php for($j = 0; $j < count($abs[$i]) - 1; $j++): ?>
								<td><?php echo $abs[$i][$j] ?></td>
							<?php endfor; ?>
						</tr>
					<?php endfor; ?>
					
					<tr>
						<td><?php echo $i ?></td>
						<?php for($j = 0; $j < count($calc); $j++): ?>
							<td><?php echo $calc[$j] ?></td>
						<?php endfor; ?>
					</tr>

				</table> -->

				<?php

				// Videja kļūda
				for($i = 0; $i < count($abs); $i++) {
					$sum += $abs[$i]['sum'];
				}

				$this->populacija[$key]['calc'] = $calc; 
				$this->populacija[$key]['abs'] = $abs; 
				$this->populacija[$key]['sum'] = $sum; 
				$this->populacija[$key]['piemerotiba'] = $sum / count($this->realizacijas); 

				/* populacijas info */
				$this->populacijas_info['sum'] += $this->populacija[$key]['piemerotiba']; // Piemērotības summa
				if($this->populacija[$key]['piemerotiba'] < $this->populacijas_info['max']['val'] || $this->populacijas_info['max']['val'] == false) { // meklējam maksimālo vērtību
					$this->populacijas_info['max']['val'] = $this->populacija[$key]['piemerotiba'];
					$this->populacijas_info['max']['key'] = $key;
				}
			}
			$this->populacijas_info['avg'] = $this->populacijas_info['sum'] / count($this->populacija);
			$this->populacijas_info = $this->populacijas_info;
		}


	}


	protected function populacijas_izvade() {
		foreach($this->populacija as $key => $individs): ?>

			
				<h3><?php echo $key+1; ?></h3>
			
			 <?php

				require_once('./test/classes/GDRenderer.php');
				unset($individs['piemerotiba']);
				unset($individs['calc']);
				unset($individs['abs']);
				echo render_tree($individs);

				//create new GD renderer, optinal parameters: LevelSeparation,  SiblingSeparation, SubtreeSeparation, defaultNodeWidth, defaultNodeHeight
				
				?>

				<img src='./image.php?img=<?php echo json_encode(serialize($individs)); ?>' alt="">

		<?php endforeach; ?>

		<h3>ĢP koku funkcijas</h3>
		<table border="1">
			<tr>
				<th>Indivīds</th>
				<th>Funkcija</th>
				<th>Piemērotība</th>
			</tr>
			<?php for ($i=0; $i < count($this->populacija); $i++): ?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo render_tree($this->populacija[$i]); ?></td>
				<td><?php echo $this->populacija[$i]['piemerotiba']; ?></td>
			</tr>
			<?php endfor; ?>
		</table>
		<p>Kopējā piemērotība: <?php echo skaitlis($this->populacijas_info['sum']); ?></p>
		<p>Labākā piemērotība: <?php echo skaitlis($this->populacijas_info['max']['val']); ?></p>
		<p>Vidējā piemērotība: <?php echo skaitlis($this->populacijas_info['avg']); ?></p>
		<?php
	}

}