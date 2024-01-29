<?php

function helpPage() {
    printf("Enjoy to play 2048!\n\n");
    printf("Click Enter to start . . .\n\n");
    $input = trim(fgets(STDIN));
}


// Function to initialize the game board
function initializeBoard($size)
{
    $board = array();

    for ($i = 0; $i < $size; $i++) {
        $board[$i] = array_fill(0, $size, 0);
    }

    addRandomTile($board);
    addRandomTile($board);

    return $board;
}

// Function to add a random tile (either 2 or 4) to the board
function addRandomTile(&$board)
{
    $emptyCells = getEmptyCells($board);

    if (count($emptyCells) > 0) {
        $randomIndex = array_rand($emptyCells);
        $randomCell = $emptyCells[$randomIndex];

        $value = (rand(0, 1) == 0) ? 2 : 4;

        $board[$randomCell['row']][$randomCell['col']] = $value;
    }
}

// Function to get all empty cells on the board
function getEmptyCells($board)
{
    $emptyCells = array();

    foreach ($board as $row => $rowData) {
        foreach ($rowData as $col => $value) {
            if ($value == 0) {
                $emptyCells[] = array('row' => $row, 'col' => $col);
            }
        }
    }

    return $emptyCells;
}

// Function to display the game board
function displayBoard($board)
{
    echo "----------------------\n";

    foreach ($board as $row) {
        echo "|";
        foreach ($row as $cell) {
            if ($cell == 0) {
                echo "    |";
            } else {
                printf("%4d|", $cell);
            }
        }
        echo "\n----------------------\n";
    }
}

// Function to move tiles in a specified direction
function moveTiles(&$board, $direction)
{
    echo "switch menu:\n\n";
    echo $direction . "\n\n";
    switch ($direction) {
        case 'left':
        case 'a':
            moveLeft($board);
            break;
        case 'right':
        case 'd':
            moveRight($board);
            break;
        case 'up':
        case 'w':
            echo "up!\n\n";
            moveUp($board);
            break;
        case 'down':
        case 's' :
            moveDown($board);
            break;
        default:
            echo "Invalid direction\n";
    }

    addRandomTile($board);
}

// Function to move tiles to the left
function moveLeft(&$board)
{
    foreach ($board as &$row) {
        $row = slideLeft($row);
        $row = mergeTiles($row);
        $row = slideLeft($row);
    }
}


// Function to slide tiles to the left
function slideLeft($row)
{
    //echo "im here!\n\n";
    $row = array_filter($row, function ($value) {
        return $value != 0;
    });

    $numEmptyCells = 4 - count($row);
    $row = array_merge($row, array_fill(0, $numEmptyCells, 0));

    return $row;
}


// Function to move tiles to the right
function moveRight(&$board)
{
    foreach ($board as &$row) {
        //$row = array_reverse($row);
        $row = slideRight($row);
        $row = mergeTiles($row);
        $row = slideRight($row);
        //$row = array_reverse($row);
    }
}
// Function to slide tiles to the right
function slideRight($row)
{
    $row = array_reverse($row);
    $row = slideLeft($row);
    $row = array_reverse($row);
    return $row;
}

// Function to move tiles up
function moveUp(&$board)
{
    $transposedBoard = transposeBoard($board);
    moveLeft($transposedBoard);
    $board = transposeBoard($transposedBoard);
}

// Function to move tiles down
function moveDown(&$board)
{
    $transposedBoard = transposeBoard($board);
    moveRight($transposedBoard);
    $board = transposeBoard($transposedBoard);
}

// Function to merge adjacent tiles with the same value
function mergeTiles($row)
{
    for ($i = 0; $i < count($row) - 1; $i++) {
        if ($row[$i] == $row[$i + 1] && $row[$i] != 0) {
            $row[$i] *= 2;
            $row[$i + 1] = 0;
        }
    }
    return $row;
}

// Function to transpose the game board
//map 
function transposeBoard($board)
{
    $transposedBoard = array();

    for ($i = 0; $i < count($board); $i++) {
        for ($j = 0; $j < count($board[$i]); $j++) {
            $transposedBoard[$j][$i] = $board[$i][$j];
        }
    }

    return $transposedBoard;
}
// Function to get user input for the next move
function getUserMove()
{
    echo "Enter your move (left - a, right - d, up - w, down - s): ";
    $input = trim(fgets(STDIN));
    return strtolower($input);
}

// Function to check if the game is over
function isGameOver($board)
{
    // Check if there are any empty cells
    if (count(getEmptyCells($board)) > 0) {
        return false;
    }

    // Check if there are any adjacent tiles with the same value
    for ($i = 0; $i < count($board); $i++) {
        for ($j = 0; $j < count($board[$i]) - 1; $j++) {
            if ($board[$i][$j] == $board[$i][$j + 1]) {
                return false;
            }
        }
    }

    for ($i = 0; $i < count($board) - 1; $i++) {
        for ($j = 0; $j < count($board[$i]); $j++) {
            if ($board[$i][$j] == $board[$i + 1][$j]) {
                return false;
            }
        }
    }

    // If no empty cells and no adjacent tiles with the same value, the game is over
    return true;
}

// Function to check if the player has won
function hasPlayerWon($board)
{
    foreach ($board as $row) {
        foreach ($row as $cell) {
            if ($cell == 2048) {
                return true;
            }
        }
    }

    return false;
}

// Example usage:

// Set the board size (e.g., 4x4)
$boardSize = 4;

// Initialize the game board
$board = initializeBoard($boardSize);
helpPage();
// Display the initial board
displayBoard($board);

// Game loop
while (true) {
   
    // Get user input for the next move
    $direction = getUserMove();

    // Move tiles based on user input
    moveTiles($board, $direction);

    // Display the updated board
    displayBoard($board);


    // Check for game over
    if (isGameOver($board)) {
        echo "Game Over! No more valid moves.\n";
        break;
    }



// Check for a win condition
if (hasPlayerWon($board)) {
    echo "Congratulations! You won!\n";
    break;
}
}

?>