<!--
Author: Mark Harms
CWID: 102-44-042
Date: 11/01/2018
Assignment#: 3

Description: This solution implements an HTML based Othello game, with Minimax based AI.
The Minimax AI implements Alpha-Beta pruning and allows for a dynamic depth count as well as
a debug mode to output the analysis of each tree branch as well as a flag for disabling pruning.
The webpage game is dynamic in that all user interaction is done through clicks.

-->

<?php



// Set our memory limit higher, with no time limit on execution
ini_set('memory_limit', '1024M');
set_time_limit(0);
// Declare an array now to be used for user input
$letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');

// Function to begin a new game, instantiates all the necessary variables to their starting states
function startNewGame()
{
	// Begin a session, used for $_SESSION variables
	session_start();
	
	// Instantiate the board
	$_SESSION['othelloBoard'] = 
	array(
		array('', '', '',  '',  '', '', '', ''),
		array('', '', '',  '',  '', '', '', ''),
		array('', '', '',  '',  '', '', '', ''),
		array('', '', '', 'W', 'B', '', '', ''),
		array('', '', '', 'B', 'W', '', '', ''),
		array('', '', '',  '',  '', '', '', ''),
		array('', '', '',  '',  '', '', '', ''),
		array('', '', '',  '',  '', '', '', '')
	);
	
	// Instantiate the board value array
	$_SESSION['placeValues'] =
	array(
		array(1000, -400, 10, 10, 10, 10,   -400,  1000),
		array(-400, -800,  3,  3,  3,  3, -800,  -400),
		array(10,  3,  5,  5,  5,  5,   3, 10),
		array(10,  3,  5,  5,  5,  5,   3, 10),
		array(10,  3,  5,  5,  5,  5,   3, 10),
		array(10,  3,  5,  5,  5,  5,   3, 10),
		array(-400, -800,  3,  3,  3,  3, -800,  -400),
		array(100, -400, 10, 10, 10, 10,   -400,  1000),
	);
	
	// Black goes first
	$_SESSION['myColor'] = 'B';
	$_SESSION['opponentColor'] = 'W';
	$_SESSION['moves'] = 0;
	$_SESSION['gameOver'] = false;
	checkValidMoves($_SESSION['othelloBoard'], $_SESSION['myColor'], true);
	getScores($_SESSION['othelloBoard'], true);
	$_SESSION['lastMove'] = 'None';
}

// Function for a 'rotation' of gameplay e.g. a player playing a move
function sessionRotation($x, $y)
{
	$_SESSION['W'] = '';
	$_SESSION['B'] = '';
	
	placePiece($x, $y, $_SESSION['othelloBoard'], $_SESSION['myColor'], true); // Place the piece
	getScores($_SESSION['othelloBoard'], true); // Get the scores after placing the piece
	$_SESSION['moves']++; // Increment our move counter
	$_SESSION['lastMove'] = getPlacement($x, $y); // Record the move we made
	swapSessionColor(); // Pass control to the other player
	checkValidMoves($_SESSION['othelloBoard'], $_SESSION['myColor'], true); // Check for other players possible moves
	
	if (empty($_SESSION['validMoves'])) // Other player has no moves to make
	{
		$_SESSION[$_SESSION['myColor']] .= 'No moves, skipping turn<br>'; // Notify other player
		swapSessionColor(); // Swap control back to us
		echo '<script>sessionStorage.setItem("myColor", "', $_SESSION['myColor'], '");</script>'; // Notify webpage of color swap (through JS)
		checkValidMoves($_SESSION['othelloBoard'], $_SESSION['myColor'], true); // Check if we have any moves
		if (empty($_SESSION['validMoves'])) // If we don't have moves (right after our opponent doesn't), the game is over.
		{
			if ($_SESSION['whiteScore'] > $_SESSION['blackScore']) // If White wins
				$_SESSION['gOMessage'] = "White wins {$_SESSION['whiteScore']}-{$_SESSION['blackScore']}!<br>";
			else if ($_SESSION['whiteScore'] < $_SESSION['blackScore']) // If Black wins
				$_SESSION['gOMessage'] = "Black wins {$_SESSION['blackScore']}-{$_SESSION['whiteScore']}!<br>";
			else // Otherwise its a tie
				$_SESSION['gOMessage'] = 'It\'s a tie!<br>';
			
			$_SESSION['gameOver'] = true; // Set our gameover flag
		}
	}
}

// Utility Function for checking if the board is filled
function boardFull($board)
{
	for($i=0; $i<count($board); $i++)
	{
		for($j=0; $j<count($board[$i]); $j++)
		{
			if ($board[$i][$j] === '')
				return false;
		}
	}
	return true;
}

// Function that checks for all valid moves given a board and player color
function checkValidMoves($board, $myColor, $sessionFlag)
{
	$validMoves = array();
	
	for($i=0; $i<count($board); $i++)
	{
		for($j=0; $j<count($board[$i]); $j++)
		{
			if (checkValidSpot($i, $j, $board, $myColor))
			{
				array_push($validMoves, array($i+1, $j+1));
			}
		}
	}
	if ($sessionFlag)
		$_SESSION['validMoves'] = $validMoves;
	else
		return $validMoves;
}

// Function that checks if a specific spot on the board is a valid move
function checkValidSpot($i, $j, $board, $myColor)
{
	if ($board[$i][$j] != '')
		return false;
	
	if ($myColor === 'W')
		$opponentColor = 'B';
	else
		$opponentColor = 'W';
	
	$len = count($board) - 1;
	
	for($x=-1; $x<=1; $x++)
	{
		if (($i+$x) < 0 || ($i+$x) > $len)
			continue;				

		for($y=-1; $y<=1; $y++)
		{
			if ($x == 0 && $y == 0) // No slope at all, skip case
				continue;
			
			if (($j+$y) < 0 || ($j+$y) > $len) // If invalid array index
				continue;	
			
			if ($board[($i+$x)][($j+$y)] === $myColor || $board[($i+$x)][($j+$y)] === '') // More edge cases
				continue;	
			
			$testI = $i + $x;
			$testJ = $j + $y;
			
			if ($testI > $len || $testJ > $len || $testI < 0 || $testJ < 0)
					continue;
			
			while ($board[$testI][$testJ] === $opponentColor)
			{
				$testI += $x;
				$testJ += $y;
				if ($testI > $len || $testJ > $len || $testI < 0 || $testJ < 0)
					break;
			}
			if ($testI > $len || $testJ > $len || $testI < 0 || $testJ < 0)
					continue;
			
			if ($board[$testI][$testJ] == $myColor)
			{
				return true;
			}
		}	
	}
	return false;
}

// Function used to 'place' a piece on the board, updating all necessary values
function placePiece($i, $j, $board, $myColor, $sessionFlag)
{
	$toFlip = array();
	
	if ($sessionFlag)
		$_SESSION['othelloBoard'][$i][$j] = $myColor;
	$board[$i][$j] = $myColor;
	
	if ($myColor === 'W')
		$opponentColor = 'B';
	else
		$opponentColor = 'W';
	
	$len = count($board) - 1;

	for($x=-1; $x<=1; $x++)
	{
		if ( ($i+$x) < 0 || ($i+$x) > $len )
			continue;				

		for($y=-1; $y<=1; $y++)
		{
			if ($x == 0 && $y == 0) // No slope at all, skip case
				continue;
			
			if (($j+$y) < 0 || ($j+$y) > $len) // If invalid array index
				continue;	
			
			if ($board[($i+$x)][($j+$y)] === $myColor || $board[($i+$x)][($j+$y)] === '') // More edge cases
				continue;	
			
			$flipHolder = array();
			$testI = $i + $x;
			$testJ = $j + $y;
			
			if ($testI > $len || $testJ > $len || $testI < 0 || $testJ < 0)
					continue;
			
			while ($board[$testI][$testJ] === $opponentColor)
			{
				array_push($flipHolder, array($testI, $testJ));
				$testI += $x;
				$testJ += $y;
				if ($testI > $len || $testJ > $len || $testI < 0 || $testJ < 0)
					break;
			}
			if ($testI > $len || $testJ > $len || $testI < 0 || $testJ < 0)
					continue;
			
			if ($board[$testI][$testJ] === $myColor)
			{
				for($num=0; $num<count($flipHolder); $num++)
				{
					if ($sessionFlag)
						$_SESSION['othelloBoard'][$flipHolder[$num][0]][$flipHolder[$num][1]] = $myColor;
					$board[$flipHolder[$num][0]][$flipHolder[$num][1]] = $myColor;	
					array_push($toFlip, array($flipHolder[$num][0]+1, $flipHolder[$num][1]+1));
				}
			}
		}	
	}
	if ($sessionFlag)
		$_SESSION['toFlip'] = $toFlip;
	else
		return $board;
}

// Swaps control to other player
function swapSessionColor()
{
	if ($_SESSION['myColor'] === 'W')
	{
		$_SESSION['myColor'] = 'B';
		$_SESSION['opponentColor'] = 'W';
	}
	else
	{
		$_SESSION['myColor'] = 'W';
		$_SESSION['opponentColor'] = 'B';
	}
}

// Utility function to return whether it is the AI's turn
function aiTurn($myColor)
{
	if ($myColor === 'W' && $_SESSION['whiteAI'])
	{
		return true;
	}
	else if ($myColor === 'B' && $_SESSION['blackAI'])
	{
		return true;
	}
	else
		return false;
}

// Returns the board score of the game
function getScores($board, $sessionFlag) // Returns array [whiteScore, blackScore]
{
	$whiteScore = 0;
	$blackScore = 0;
	
	for($i=0; $i<count($board); $i++)
	{
		for($j=0; $j<count($board[$i]); $j++)
		{
			$color = $board[$i][$j];
			if ($color === 'W')
				$whiteScore += 1;
			else if ($color === 'B')
				$blackScore += 1;
		}
	}
	if ($sessionFlag)
	{
		$_SESSION['whiteScore'] = $whiteScore;
		$_SESSION['blackScore'] = $blackScore;
	}
	else
		return array($whiteScore, $blackScore);
}

// Utility function for printing the board
function printBoard($board)
{
	echo '---------------';
	for ($i=0; $i<count($board); $i++)
	{
		echo '<br>';
		for ($j=0; $j<count($board[$i]); $j++)
		{
			if ($board[$i][$j] === '')
				echo 'O ';
			else
				echo "{$board[$i][$j]} ";	
		}
		echo '<br>';
	}
	echo '---------------';
}

// Utility function to get the location a piece was played e.g. 'C4'
function getPlacement($x, $y)
{
	global $letters;
	
	$placement = "$letters[$y]" . ($x+1);
	
	return $placement;
}

// Heuristic function that evalutaes the value of a given board state
function getStateValue($board, $myColor)
{
	$scores = getScores($board, false);
	
	if (boardFull($board))
	{
		if ($scores[0] > $scores[1])
		{
			if ($myColor === 'W')
				return 10000;
			else
				return -10000;
		}
		else if ($scores[0] < $scores[1])
		{
			if ($myColor === 'B')
				return 10000;
			else
				return -10000;
		}
		else
			return 0;
	}

	if ($myColor === 'W')
	{
		if ($scores[1] === 0)
			return 10000;
		if ($scores[0] === 0)
			return -10000;
		$value = ($scores[0] - $scores[1]);
		
		$moveCount = count(checkValidMoves($board, 'W', false));
	}
	else
	{
		if ($scores[0] === 0)
			return 10000;
		if ($scores[1] === 0)
			return -10000;
		$value = ($scores[1] - $scores[0]);
		
		$moveCount = count(checkValidMoves($board, 'B', false));
	}
	
	$value += 50 * $moveCount;
	
	for($i=0; $i<count($board); $i++)
	{
		for($j=0; $j<count($board[$i]); $j++)
		{
			$place = $board[$i][$j];
			if ($place != '')
			{
				$placeValue = $_SESSION['placeValues'][$i][$j];
				
				if ($place === $myColor)                                                                          
					$value += $placeValue;
				else if ($place != '')
					
					$value -= $placeValue;	
			}
		}
	}
	return $value;
}

// Utility function to print all moves in an array of moves
function printMoveList($moveList, $value)
{
	foreach ($moveList as $move)
	{
		if (is_string($move))
			$_SESSION['aiMsg'] .= $move. ' ';
		else
			$_SESSION['aiMsg'] .= getPlacement($move[0]-1, $move[1]-1). ' ';	
	}
	$_SESSION['aiMsg'] .= "Value: $value<br>";
}

// Begin AI logic
function aiPlay($depth)
{
	$board = $_SESSION['othelloBoard'];
	$alpha = -100000;
	$beta = 100000;
	
	$_SESSION['miniMaxNum'] = 0;

	return miniMax($board, $_SESSION['depth'], $alpha, $beta, true, array());
	
}

// miniMax algorithm function
function miniMax($board, $depth, $alpha, $beta, $maximizeFlag, $moveList)
{
	$_SESSION['miniMaxNum']++;
	
	if ($depth == 0 || boardFull($board))
	{
		if ($maximizeFlag)
			$value = getStateValue($board, $_SESSION['myColor']);
		else
			$value = getStateValue($board, $_SESSION['opponentColor']);
		
		if ($_SESSION['DEBUGFLAG'])
			printMoveList($moveList, $value);
		return array($value, '');
	}
	
	$bestMove = 'None';
	
	if ($maximizeFlag)
	{
		$maxEval = -100000; // Set max to an initially very low number
		
		$validMoves = checkValidMoves($board, $_SESSION['myColor'], false);
		
		if(empty($validMoves)) // If we have no moves then we skip our turn
		{
			array_push($moveList, "{$_SESSION['myColor']}-SKIP");
			return miniMax($board, $depth-1, $alpha, $beta, false, $moveList);
		}	
		
		foreach ($validMoves as $move) // Loop through all the moves
		{
			$newBoard = placePiece($move[0]-1, $move[1]-1, $board, $_SESSION['myColor'], false); // Get the resulting board
			
			array_push($moveList, $move); // Append move to movelist
			
			list($eval, $moves) = miniMax($newBoard, $depth-1, $alpha, $beta, false, $moveList); // Recursively go into miniMax
			
			array_pop($moveList); // Pop the move off
			
			if ($eval > $maxEval) // Update maxEval if we found a new max
			{
				$maxEval = $eval;
				$bestMove = $move;
			}
			if ($eval > $alpha) // Update alpha if its our new max
				$alpha = $eval;
			
			if ($_SESSION['PRUNEFLAG'])
			{
				if ($beta <= $alpha) // If beta is less than alpha we should alpha prune this tree
				{
					if ($_SESSION['DEBUGFLAG'])
						$_SESSION['aiMsg'] .= 'Alpha Pruning<br>';
					break;
				}
			}
		}
		return array($maxEval, $bestMove);	
	}
	else
	{
		$minEval = 100000; // Set min to an intially very high number
		$validMoves = checkValidMoves($board, $_SESSION['opponentColor'], false);
		
		if(empty($validMoves)) // If we have no moves then we skip our turn
		{
			array_push($moveList, "{$_SESSION['opponentColor']}-SKIP");
			return miniMax($board, $depth-1, $alpha, $beta, true, $moveList);
		}
		
		foreach ($validMoves as $move) // Loop through all the moves
		{
			$newBoard = placePiece($move[0]-1, $move[1]-1, $board, $_SESSION['opponentColor'], false); // Get the resulting board
			
			array_push($moveList, $move); // Append move to movelist
			
			list($eval, $moves) = miniMax($newBoard, $depth-1, $alpha, $beta, true, $moveList); // Recursively go into miniMax
			
			array_pop($moveList); // Pop move off
			
			if ($minEval > $eval) // Update minEval if we found a new min
			{
				$minEval = $eval;
				$bestMove = $move;
			}
			
			if ($beta > $eval) // Update beta if its our new min
				$beta = $eval;
			
			if ($_SESSION['PRUNEFLAG'])
			{
				if ($beta <= $alpha) // If beta is less than alpha we should beta prune this tree
				{
					if ($_SESSION['DEBUGFLAG'])
						$_SESSION['aiMsg'] .= 'Beta Pruning<br>';
					break;
				}
			}
		}
		return array($minEval, $bestMove);
	}
}
