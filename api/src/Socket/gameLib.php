<?php
const DEFAULT_BOARD = [
    ["r", "n", "b", "q", "k", "b", "n", "r"],
    ["p", "p", "p", "p", "p", "p", "p", "p"],
    ["", "", "", "", "", "", "", ""],
    ["", "", "", "", "", "", "", ""],
    ["", "", "", "", "", "", "", ""],
    ["", "", "", "", "", "", "", ""],
    ["P", "P", "P", "P", "P", "P", "P", "P"],
    ["R", "N", "B", "Q", "K", "B", "N", "R"],
];

function turnToCords(string $turn) {
    if (in_array($turn, ["b00", "b000", "w00", "w000"])) {
        $row = $turn[0] === "b" ? 0 : 7;
        $long = strlen($turn) === 4;
        return [
            'fromRow' => $row,
            'fromCol' => 4,
            'toRow' => $row,
            'toCol' => $long ? 2 : 6,
            'castling' => true,
            'rookFromCol' => $long ? 0 : 7,
            'rookToCol' => $long ? 3 : 5,
        ];
    }

    $charCodeShift = ord('a');
    $numCodeShift = ord('1');
    $cords = [
        'fromCol' => ord($turn[0]) - $charCodeShift,
        'fromRow' => ord($turn[1]) - $numCodeShift,
        'toCol' => ord($turn[2]) - $charCodeShift,
        'toRow' => ord($turn[3]) - $numCodeShift,
    ];

    if (strlen($turn) === 5) {
        $cords['newPiece'] = $turn[4];
    }

    return $cords;
}

function cordsToTurn(array $cords) {
    if (isset($cords['castling']) && $cords['castling']) {
        $color = $cords['fromRow'] === 0 ? "b" : "w";
        $zeros = $cords['rookToCol'] === 3 ? "000" : "00";
        return $color . $zeros;
    }

    $charCodeShift = ord('a');
    $numCodeShift = ord('1');
    $turn = chr($cords['fromCol'] + $charCodeShift) .
        chr($cords['fromRow'] + $numCodeShift) .
        chr($cords['toCol'] + $charCodeShift) .
        chr($cords['toRow'] + $numCodeShift);

    if (isset($cords['newPiece'])) {
        $turn .= $cords['newPiece'];
    }

    return $turn;
}

function applyTurns($turns, $board = DEFAULT_BOARD, $currentPlayer = "white", $hasMoved = [
    "whiteKing" => false,
    "whiteRookLeft" => false,
    "whiteRookRight" => false,
    "blackKing" => false,
    "blackRookLeft" => false,
    "blackRookRight" => false
]) {
    if (empty($turns)) {
        return ["board" => $board, "hasMoved" => $hasMoved];
    }

    $board = array_map(function($row) {
        return array_slice($row, 0);
    }, $board);

    $turn = array_shift($turns);

    $fromRow = $turn['fromRow'];
    $fromCol = $turn['fromCol'];
    $toRow = $turn['toRow'];
    $toCol = $turn['toCol'];
    @$castling = $turn['castling'];
    @$rookFromCol = $turn['rookFromCol'];
    @$rookToCol = $turn['rookToCol'];
    $newPiece = isset($turn['newPiece']) ? $turn['newPiece'] : null;

    if ($castling) {
        $newBoard = array_map(function($row) {
            return array_slice($row, 0);
        }, $board);
        $piece = $newBoard[$fromRow][$fromCol];
        $newBoard[$fromRow][$fromCol] = "";
        $newBoard[$toRow][$toCol] = $piece;
        $newBoard[$fromRow][$rookFromCol] = "";
        $newBoard[$fromRow][$rookToCol] = $currentPlayer === "white" ? "R" : "r";
        $board = $newBoard;
    } else {
        $piece = $newPiece ?? $board[$fromRow][$fromCol];
        $board[$toRow][$toCol] = $piece;
        $board[$fromRow][$fromCol] = "";
    }

    if ($currentPlayer === "white") {
        if ($board[$toRow][$toCol] === "K") {
            $hasMoved['whiteKing'] = true;
        }
        if ($board[$toRow][$toCol] === "R") {
            if ($fromCol === 0) $hasMoved['whiteRookLeft'] = true;
            if ($fromCol === 7) $hasMoved['whiteRookRight'] = true;
        }
    } else {
        if ($board[$toRow][$toCol] === "k") {
            $hasMoved['blackKing'] = true;
        }
        if ($board[$toRow][$toCol] === "r") {
            if ($fromCol === 0) $hasMoved['blackRookLeft'] = true;
            if ($fromCol === 7) $hasMoved['blackRookRight'] = true;
        }
    }

    return applyTurns($turns, $board, $currentPlayer === "white" ? "black" : "white", $hasMoved);
}

function validateTurn($cords, $board, $player) {
    $fromRow = $cords['fromRow'];
    $fromCol = $cords['fromCol'];
    $toRow = $cords['toRow'];
    $toCol = $cords['toCol'];
    $piece = $board[$fromRow][$fromCol];
    $board = $player === "black" ? array_reverse($board) : $board;
    $fromRow = $player === "black" ? 7 - $fromRow : $fromRow;
    $toRow = $player === "black" ? 7 -$toRow : $toRow;
    return validate(strtoupper($piece), $fromRow, $fromCol, $toRow, $toCol, $board);
}

function canCaptureKing($row, $col, $newRow, $newCol, $board, $currentPlayer) {
    $piece = $board[$row][$col];
    $newBoard = array_map(function($row) {
        return array_slice($row, 0);
    }, $board);

    if ($newBoard[$newRow][$newCol] === "" ||
        ($currentPlayer === "white" && strtolower($newBoard[$newRow][$newCol]) === $newBoard[$newRow][$newCol]) ||
        ($currentPlayer === "black" && strtoupper($newBoard[$newRow][$newCol]) === $newBoard[$newRow][$newCol])) {
        $newBoard[$newRow][$newCol] = $piece;
        $newBoard[$row][$col] = "";
    } else {
        return false;
    }

    return !isCheck($currentPlayer, $newBoard);
}

function validate($piece, $row, $col, $newRow, $newCol, $board) {
    $validateFns = [
        "P" => "validatePawn",
        "N" => "validateKnight",
        "B" => "validateBishop",
        "R" => "validateRook",
        "Q" => "validateQueen",
        "K" => "validateKing"
    ];

    if (isset($validateFns[$piece])) {
        return call_user_func($validateFns[$piece], $row, $col, $newRow, $newCol, $board);
    }
    return false;
}

function validatePawn($row, $col, $newRow, $newCol, $board) {
    if ($col === $newCol) {
        if ($row === 6) {
            return ($newRow === $row - 1 || $newRow === $row - 2) && $board[$row - 1][$col] === "" && ($newRow === $row - 2 || $board[$newRow][$newCol] === "") && $board[$newRow][$newCol] === "";
        } else {
            return $newRow === $row - 1 && $board[$newRow][$newCol] === "";
        }
    } else {
        if (abs($newCol - $col) === 1) {
            if ($newRow === $row - 1 && $board[$newRow][$newCol] !== "") {
                return true;
            }
        }
        return false;
    }
}

function validateKnight($row, $col, $newRow, $newCol) {
    $dx = abs($newRow - $row);
    $dy = abs($newCol - $col);
    return ($dx === 2 && $dy === 1) || ($dx === 1 && $dy === 2);
}

function validateBishop($row, $col, $newRow, $newCol, $board) {
    if (abs($newRow - $row) === abs($newCol - $col)) {
        $directionX = $newRow > $row ? 1 : -1;
        $directionY = $newCol > $col ? 1 : -1;
        $i = $row + $directionX;
        $j = $col + $directionY;
        while ($i !== $newRow && $j !== $newCol) {
            if ($board[$i][$j] !== "") return false;
            $i += $directionX;
            $j += $directionY;
        }
        return true;
    }
    return false;
}

function validateRook($row, $col, $newRow, $newCol, $board) {
    if ($newRow === $row) {
        $direction = $newCol > $col ? 1 : -1;
        for ($i = $col + $direction; $i !== $newCol; $i += $direction) {
            if ($board[$row][$i] !== "") return false;
        }
        return true;
    } elseif ($newCol === $col) {
        $direction = $newRow > $row ? 1 : -1;
        for ($i = $row + $direction; $i !== $newRow; $i += $direction) {
            if ($board[$i][$col] !== "") return false;
        }
        return true;
    }
    return false;
}

function validateQueen($row, $col, $newRow, $newCol, $board) {
    if (abs($newRow - $row) === abs($newCol - $col)) {
        $directionX = $newRow > $row ? 1 : -1;
        $directionY = $newCol > $col ? 1 : -1;
        $i = max($row + $directionX, 0);
        $j = max($col + $directionY, 0);
        while ($i !== $newRow && $j !== $newCol) {
            if ($board[$i][$j] !== "") return false;
            $i += $directionX;
            $j += $directionY;
        }
        return true;
    } elseif ($newRow === $row) {
        $direction = $newCol > $col ? 1 : -1;
        for ($i = $col + $direction; $i !== $newCol; $i += $direction) {
            if ($board[$row][$i] !== "") return false;
        }
        return true;
    } elseif ($newCol === $col) {
        $direction = $newRow > $row ? 1 : -1;
        for ($i = $row + $direction; $i !== $newRow; $i += $direction) {
            if ($board[$i][$col] !== "") return false;
        }
        return true;
    }
    return false;
}

function validateKing($row, $col, $newRow, $newCol) {
    $dx = abs($newRow - $row);
    $dy = abs($newCol - $col);
    return ($dx <= 1 && $dy <= 1);
}

function isCheck($player, $board) {
    $kingRow = -1;
    $kingCol = -1;

    for ($i = 0; $i < 8; $i++) {
        for ($j = 0; $j < 8; $j++) {
            if ($board[$i][$j] === ($player === "white" ? "K" : "k")) {
                $kingRow = $i;
                $kingCol = $j;
                break 2;
            }
        }
    }

    for ($i = 0; $i < 8; $i++) {
        for ($j = 0; $j < 8; $j++) {
            if ($player === "white") {
                if ($board[$i][$j] !== "" && strtolower($board[$i][$j]) === $board[$i][$j]) {
                    $validateFn = "validate" . strtoupper($board[$i][$j]);
                    $reversedBoard = array_reverse(array_map(function($row) {
                        return array_slice($row, 0);
                    }, $board));
                    if (call_user_func($validateFn, 7 - $i, $j, 7 - $kingRow, $kingCol, $reversedBoard)) {
                        return true;
                    }
                }
            } else {
                if ($board[$i][$j] !== "" && strtoupper($board[$i][$j]) === $board[$i][$j]) {
                    $validateFn = "validate" . strtoupper($board[$i][$j]);
                    if (call_user_func($validateFn, $i, $j, $kingRow, $kingCol, $board)) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}