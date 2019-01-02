<html>
<head>
<link rel='stylesheet' href='./CSS/index.css'>  <!-- Page Specific Styling -->
</head>

<body>
<form action='./playOthello.php' method='post'>
<div id="container" style='text-align:center;'>
<div>White
<select name='whiteAI'>
	<option value='false'>Human</option>
	<option value='true'>AI</option>
</select></div>
<div>Black
<select name='blackAI'>
	<option value='false'>Human</option>
	<option value='true'>AI</option>
</select></div>
<div><span>Debug</span><input type='checkbox' name='debugflag'></div>
<div><span>Pruning</span><input type='checkbox' name='pruneflag' checked></div>
<div><span>Hints</span><input id='hintflag' type='checkbox' name='hintflag' checked></div>
<div style=''><span>Depth</span><input type='number' name='depth' step=2 value=6></div>
<button type='submit'>Play</button>
</div></form>

</body>

</html>