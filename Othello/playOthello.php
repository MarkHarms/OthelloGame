<?php

	if (!isset($_POST['whiteAI'], $_POST['blackAI'], $_POST['depth']))
	{
		header("Location: ./");
		exit();	
	}

	if (!isset($_POST['debugflag']))
		$_POST['debugflag'] = false;
	if (!isset($_POST['pruneflag']))
		$_POST['pruneflag'] = false;
	if (!isset($_POST['hintflag']))
		$_POST['hintflag'] = false;

	require_once('othelloLogic.php');
	
	startNewGame();
	$_SESSION['whiteAI'] = filter_var($_POST['whiteAI'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['blackAI'] = filter_var($_POST['blackAI'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['DEBUGFLAG'] = filter_var($_POST['debugflag'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['PRUNEFLAG'] = filter_var($_POST['pruneflag'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['HINTFLAG'] = filter_var($_POST['hintflag'], FILTER_VALIDATE_BOOLEAN);
	$_SESSION['depth'] = $_POST['depth']; // Please choose an even number
	
	
	if(!$_SESSION['whiteAI']) $wIcon = 'style=\'background-image:url(./Images/playerIconW.png)\'';
	else $wIcon = 'style=\'background-image:url(./Images/aiIconW.png)\'';
		
	if(!$_SESSION['blackAI']) $bIcon = 'style=\'background-image:url(./Images/playerIconB.png)\'';
	else $bIcon = 'style=\'background-image:url(./Images/aiIconB.png)\'';
	
?>
<html>
<head>

<link rel='stylesheet' href='./CSS/othello.css'>  <!-- Page Specific Styling -->

<script src='http://code.jquery.com/jquery-1.7.1.min.js'></script> <!-- Source jQuery -->

<script>
	var validMoves = [];
<?php
foreach ($_SESSION['validMoves'] as $thing)
{
	echo "validMoves.push([$thing[0], $thing[1]]);\n";
}
echo 'sessionStorage.setItem(\'validMoves\', JSON.stringify(validMoves));';
?>
sessionStorage.setItem('myColor', 'B');
</script>

<script>
$(window).load(function()
{
	
	var table, tr, tdArray, i, j;
	table = document.getElementById('othelloBoard'); // Table ID
	tr = table.getElementsByTagName('tr');
	// Loop through all table rows, and hide those who don\'t match the search query
	for (i = 1; i < tr.length; i++) // Start i at 1, 0th index is the Table header.
	{
		tdArray = tr[i].getElementsByTagName('td');
		for (j = 1; j < tdArray.length; j++)
		{
			if ( (i == 4 && j == 5) || (j == 4 && i == 5) )
			{
				tdArray[j].innerHTML = 'B';	
				tdArray[j].classList.add('circle');
				tdArray[j].classList.add('circleB');
			}
			else if ( (j == i && j == 4) || (j == i  && j == 5) )
			{
				tdArray[j].innerHTML = 'W';
				tdArray[j].classList.add('circle');
				tdArray[j].classList.add('circleW');
			}
		}
	}
});
</script>
<script>
$(window).load(function()
{
	$('td').click(function()
	{
		if (!this.classList.contains('numAttribute'))
		{
			var row = $(this).parent().parent().children().index($(this).parent()) + 1; // Since we start counting at 1 we add 1
			var col = $(this).parent().children().index($(this));
			
			
			var moves = JSON.parse(sessionStorage.getItem('validMoves'));
			var color = sessionStorage.getItem('myColor');
			
			var valid = false;
			for (var i = 0; i < moves.length; i++) 
			{
				if (moves[i][0] === row && moves[i][1] === col)
				{
					valid = true;
					break;
				}
			}
			if (valid)
			{
				//alert('This is a valid move');
				this.innerHTML = color;
				this.classList.add('circle');
				if (color === 'W')
				{	
					this.classList.add('circleW');
					sessionStorage.setItem('myColor', 'B');
				}
				else
				{
					this.classList.add('circleB');
					sessionStorage.setItem('myColor', 'W');
				}	
				var x = document.getElementsByClassName('validMove')
				for (var i = 0; i < x.length;) // We dont increment i because x dynamically updates, IDK why.
				{
					x[i].classList.remove('validMove');
				}
				$('.info').load(('./makeMove.php'), { x:row, y:col });
			}
			else
				alert('This is not a valid move');
		}
	});
});
</script>
<script>
$(window).load(function()
{
	$('#hintButton').click(function()
	{
		var table, tr, moves, row, col;
		table = document.getElementById('othelloBoard'); // Table ID
		tr = table.getElementsByTagName('tr');
		moves = JSON.parse(sessionStorage.getItem('validMoves'));
		
		for (var i = 0; i < moves.length; i++)
		{
			row = moves[i][0];
			col = moves[i][1];
			tr[row].getElementsByTagName('td')[col].classList.add('validMove');
		}
	});
});
</script>
<script>
function checkStorage()
{
	var moves = JSON.parse(sessionStorage.getItem('toFlip'));
	
	for (var i = 0; i < moves.length; i++) 
	{
		alert(moves[i]);
	}
}
</script>

<script>

function placePiece(row, col)
{
	//alert("Trying: " + row + ", " + col);
	
	var table = document.getElementById('othelloBoard'); // Table ID
	
	var element = table.getElementsByTagName('tr')[row].getElementsByTagName('td')[col];
	
	var moves = JSON.parse(sessionStorage.getItem('validMoves'));
	var color = sessionStorage.getItem('myColor');
	
	var valid = false;
	for (var i = 0; i < moves.length; i++) 
	{
		if (moves[i][0] === row && moves[i][1] === col)
		{
			valid = true;
			break;
		}
	}
	if (valid)
	{
		//alert('This is a valid move');
		element.innerHTML = color;
		element.classList.add('circle');
		if (color === 'W')
		{	
			element.classList.add('circleW');
			sessionStorage.setItem('myColor', 'B');
		}
		else
		{
			element.classList.add('circleB');
			sessionStorage.setItem('myColor', 'W');
		}	
		var x = document.getElementsByClassName('validMove')
		for (var i = 0; i < x.length;) // We dont increment i because x dynamically updates, IDK why.
		{
			x[i].classList.remove('validMove');
		}
		$('.info').load(('./makeMove.php'), { x:row, y:col });
	}
	else
		alert('This is not a valid move');
}

function flipPieces()
{
	var table, tr, td, moves, row, col, color;
	table = document.getElementById('othelloBoard'); // Table ID
	tr = table.getElementsByTagName('tr');
	moves = JSON.parse(sessionStorage.getItem('toFlip'));
	
	for (var i = 0; i < moves.length; i++)
	{
		row = moves[i][0];
		col = moves[i][1];
		td = tr[row].getElementsByTagName('td')[col];
		//alert("row:" + row + " col:" + col);
		color = td.innerHTML;
		if (color === 'W')
		{
			td.innerHTML = 'B';
			td.classList.remove('circleW');
			td.classList.add('circleB');
		}	
		else
		{
			td.innerHTML = 'W';
			td.classList.remove('circleB');
			td.classList.add('circleW');
		}
	}
}

function getHint()
{
	var table, tr, moves, row, col;
	table = document.getElementById('othelloBoard'); // Table ID
	tr = table.getElementsByTagName('tr');
	moves = JSON.parse(sessionStorage.getItem('validMoves'));
	
	for (var i = 0; i < moves.length; i++)
	{
		row = moves[i][0];
		col = moves[i][1];
		tr[row].getElementsByTagName('td')[col].classList.add('validMove');
	}
}

function aiPlay()
{
	$('div.utilityDiv').load(('./aiThink.php'));
}

function updateValues()
{
	var wAI, bAI, dflag, pflag, hflag, dpth
	
	wAI = document.getElementById('whiteAI').value;
	bAI = document.getElementById('blackAI').value;
	dflag = document.getElementById('debugflag').checked;
	pflag = document.getElementById('pruneflag').checked;
	hflag = document.getElementById('hintflag').checked;
	dpth = document.getElementById('depth').value;
	
	$('div.utilityDiv').load(('./updateValues.php'), { whiteAI:wAI, blackAI:bAI, debugflag:dflag, pruneflag:pflag, hintflag:hflag, depth:dpth });	
}
</script>

<script>
	$(window).load(function()
	{
		$("#whiteAI").change(function() { updateValues(); });
		$("#blackAI").change(function() { updateValues(); });
		$("#debugflag").change(function() { updateValues(); });
		$("#pruneflag").change(function() { updateValues(); });
		$("#hintflag").change(function() { updateValues(); });
		$("#depth").change(function() { updateValues(); });
    	
	});
</script>        

<script>
	$(window).load(function()
	{
		$('div').on('click', 'button.aiPlay', function()
		{
			this.disabled = true;
		});
    	
	});
</script>

</head>
<body>
	<div id='optionHeader'>
		<div>White
		<select id='whiteAI' name='whiteAI'>
			<?php 
			if ($_SESSION['whiteAI'])
			{
				?>
				<option value='true'>AI</option>
				<option value='false'>Human</option>
				<?php
			}
			else
			{
				?>
				<option value='false'>Human</option>
				<option value='true'>AI</option>
				<?php
			}
			?>
		</select></div>
		<div>Black
		<select id='blackAI' name='blackAI'>
			<?php	
			if ($_SESSION['blackAI'])
			{
				?>
				<option value='true'>AI</option>
				<option value='false'>Human</option>
				<?php
			}
			else
			{
				?>
				<option value='false'>Human</option>
				<option value='true'>AI</option>
				<?php
			}
			?>
		</select></div>
		<div><span>Debug</span><input id='debugflag' type='checkbox' name='debugflag' <?php if ($_SESSION['DEBUGFLAG']) echo 'checked'?>></div>
		<div><span>Pruning</span><input id='pruneflag' type='checkbox' name='pruneflag' <?php if ($_SESSION['PRUNEFLAG']) echo 'checked'?>></div>
		<div><span>Hints</span><input id='hintflag' type='checkbox' name='hintflag' <?php if ($_SESSION['HINTFLAG']) echo 'checked'?>></div>
		<div style=''><span>Depth</span><input id='depth' type='number' name='depth' step=2 value=<?php echo $_SESSION['depth']; ?>></div>
	</div>


	<div class='utilityDiv' style'display:hidden;'></div>
	<div class='info'>
		<?php if(aiTurn($_SESSION['myColor'])) echo '<div class=\'coverTableWrapper\'><div class=\'coverTable\'></div></div>'; ?>
		<div class='wInfo'>
		<div class='pIcon' <?php echo $wIcon; ?>></div>
			<div class='score'>
				<div class='tile'></div>
				<div><?php echo "{$_SESSION['whiteScore']}" ?></div>
			</div>
			<?php
			if(aiTurn('B') && $_SESSION['myColor'] === 'B' && $_SESSION['DEBUGFLAG'])
			{	
				echo "<button class='aiPlay' onclick='aiPlay()'>Let AI play</button>";	
			}
			?>
		</div>
		
		<div class='bInfo'>
		<div class='pIcon' <?php echo $bIcon; ?>></div>
			<div class='score' style='box-shadow: 0px 0px 20px 5px cyan;'>
				<div class='tile'></div>
				<div><?php echo "{$_SESSION['blackScore']}" ?></div>
			</div>
			<?php
			if(aiTurn('W') && $_SESSION['myColor'] === 'W' && $_SESSION['DEBUGFLAG'])
			{	
				echo "<button class='aiPlay' onclick='aiPlay()'>Let AI play</button>";	
			}
			?>
		</div>
	</div>
	<div class='tableWrapper'>
		<table id='othelloBoard'>
			<colgroup>
				<col style="width: 22px;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
				<col style="width: 11vh;">
			</colgroup>
				<caption>Board</caption>
				<thead>
					<tr>
						<th class='emptyspot'></th>
						<th>A</th>
						<th>B</th>
						<th>C</th>
						<th>D</th>
						<th>E</th>
						<th>F</th>
						<th>G</th>
						<th>H</th>
					</tr>
				</thead>
				<tbody>
				<?php
					for($i=1; $i<=8; $i++)
					{
						echo
						"<tr>
							<td class='numAttribute'>$i</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
						</tr>";
					}
				?>
				</tbody>
		</table>
	</div>
</body>
<?php
if ($_SESSION['HINTFLAG']) echo '<script>getHint();</script>';
if(aiTurn($_SESSION['myColor']) && !$_SESSION['DEBUGFLAG'])
{	
	echo '<script>aiPlay();</script>';	
}
?>