<?php

	if (!isset($_POST['x'], $_POST['y']))
	{
		exit();
	}

	session_start();
	
	require_once('othelloLogic.php');
	
	$x = ($_POST['x']-1);
	$y = ($_POST['y']-1);
	
	sessionRotation($x, $y);
?>

<?php 
	if(!$_SESSION['whiteAI']) $wIcon = 'style=\'background-image:url(./Images/playerIconW.png)\'';
	else $wIcon = 'style=\'background-image:url(./Images/aiIconW.png)\'';
		
	if(!$_SESSION['blackAI']) $bIcon = 'style=\'background-image:url(./Images/playerIconB.png)\'';
	else $bIcon = 'style=\'background-image:url(./Images/aiIconB.png)\'';
?>

<?php if ($_SESSION['gameOver'])
{
?>
<div class='overlay'>
	<div>
	Game Over<br>
	<?php echo $_SESSION['gOMessage']; ?>
	
	<button onclick='location.href="./"'>New Game</button>
	</div>
</div>
<?php
}
?>

<div class="wInfo">
	<div class='pIcon' <?php echo $wIcon; ?>></div>
	<div class="score" <?php if ($_SESSION['myColor'] === 'W') echo 'style=\'box-shadow: 0px 0px 20px 5px cyan;\''?> >
		<div class="tile"></div>
		<div><?php echo "{$_SESSION['whiteScore']}" ?></div>
	</div>
	<?php 
		if ($_SESSION['whiteAI'] && $_SESSION['opponentColor'] == "W" && $_SESSION['DEBUGFLAG']) 
			echo "<div class='aiDiv'>{$_SESSION['aiMsg']}</div>";
		echo $_SESSION['W'];
		if(aiTurn('B') && $_SESSION['myColor'] === 'B' && $_SESSION['DEBUGFLAG'])
		{	
			echo "<button class='aiPlay' onclick='aiPlay()'>Let AI play</button>";	
		}
	?>
</div>

<div class="bInfo">
	<div class='pIcon' <?php echo $bIcon; ?>></div>
	<div class="score" <?php if ($_SESSION['myColor'] === 'B') echo 'style=\'box-shadow: 0px 0px 20px 5px blue;\''?>>
		<div class="tile"></div>
		<div><?php echo "{$_SESSION['blackScore']}" ?></div>
	</div>
	<?php 
		if ($_SESSION['blackAI'] && $_SESSION['opponentColor'] == "B" && $_SESSION['DEBUGFLAG']) 
			echo "<div class='aiDiv'>{$_SESSION['aiMsg']}</div>";
		echo $_SESSION['B'];
		if(aiTurn('W') && $_SESSION['myColor'] === 'W' && $_SESSION['DEBUGFLAG'])
		{	
			echo "<button class='aiPlay' onclick='aiPlay();'>Let AI play</button>";	
		}
	?>
</div>

<?php
	if(aiTurn($_SESSION['myColor'])) echo '<div class=\'coverTableWrapper\'><div class=\'coverTable\'></div></div>';
?>

<script>
	var toFlip = [];
	var validMoves = [];
	
	<?php
	
		foreach ($_SESSION['toFlip'] as $thing)
		{
			echo "toFlip.push([$thing[0], $thing[1]]);\n";
		}
		echo "sessionStorage.setItem('toFlip', JSON.stringify(toFlip));\n";
		foreach ($_SESSION['validMoves'] as $thing)
		{
			echo "validMoves.push([$thing[0], $thing[1]]);\n";
		}
		echo "sessionStorage.setItem('validMoves', JSON.stringify(validMoves));";
		echo 'flipPieces();';
		if ($_SESSION['HINTFLAG'])
		{	
			echo 'getHint();';
		}
		if(aiTurn($_SESSION['myColor']) && !$_SESSION['gameOver'] && !$_SESSION['DEBUGFLAG'])
		{
			echo 'aiPlay();';
		}
		
	?>
	
	$(window).load(function()
	{
		$(".aiPlay").click(function()
		{
			this.disabled = true;
		});
    	
	});
</script>