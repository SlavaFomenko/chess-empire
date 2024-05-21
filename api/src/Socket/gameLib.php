<?php
const DEFAULT_BOARD = [
    [
        "r",
        "n",
        "b",
        "q",
        "k",
        "b",
        "n",
        "r"
    ],
    [
        "p",
        "p",
        "p",
        "p",
        "p",
        "p",
        "p",
        "p"
    ],
    [
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""
    ],
    [
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""
    ],
    [
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""
    ],
    [
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""
    ],
    [
        "P",
        "P",
        "P",
        "P",
        "P",
        "P",
        "P",
        "P"
    ],
    [
        "R",
        "N",
        "B",
        "Q",
        "K",
        "B",
        "N",
        "R"
    ],
];

function turnToCords(string $turn)
{
    if (in_array($turn, [
        "b00",
        "b000",
        "w00",
        "w000"
    ])) {
        $row = $turn[0] === "b" ? 0 : 7;
        $long = strlen($turn) === 4;
        return [
            'fromRow'     => $row,
            'fromCol'     => 4,
            'toRow'       => $row,
            'toCol'       => $long ? 2 : 6,
            'castling'    => true,
            'rookFromCol' => $long ? 0 : 7,
            'rookToCol'   => $long ? 3 : 5,
        ];
    }

    $charCodeShift = ord('a');
    $numCodeShift = ord('1');
    $cords = [
        'fromCol' => ord($turn[0]) - $charCodeShift,
        'fromRow' => ord($turn[1]) - $numCodeShift,
        'toCol'   => ord($turn[2]) - $charCodeShift,
        'toRow'   => ord($turn[3]) - $numCodeShift,
    ];

    if (strlen($turn) === 5) {
        $cords['newPiece'] = $turn[4];
    }

    return $cords;
}

function cordsToTurn(array $cords)
{
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
    "whiteKing"      => false,
    "whiteRookLeft"  => false,
    "whiteRookRight" => false,
    "blackKing"      => false,
    "blackRookLeft"  => false,
    "blackRookRight" => false
])
{
    if (empty($turns)) {
        return [
            "board"    => $board,
            "hasMoved" => $hasMoved
        ];
    }

    $board = array_map(function ($row) {
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
        $newBoard = array_map(function ($row) {
            return array_slice($row, 0);
        }, $board);
        $piece = $newBoard[$fromRow][$fromCol];
        $newBoard[$fromRow][$fromCol] = "";
        $newBoard[$toRow][$toCol] = $piece;
        $newBoard[$fromRow][$rookFromCol] = "";
        $newBoard[$fromRow][$rookToCol] = $currentPlayer === "white" ? "R" : "r";
        $board = $newBoard;
    } else {
        if ($board[$fromRow][$fromCol] === "k") {
            $a = 1;
        }
        $piece = $newPiece ?? $board[$fromRow][$fromCol];
        $board[$toRow][$toCol] = $piece;
        $board[$fromRow][$fromCol] = "";
    }


    if ($board[$toRow][$toCol] === "K") {
        $hasMoved['whiteKing'] = true;
    }
    if ($board[$toRow][$toCol] === "R") {
        if ($fromCol === 0) $hasMoved['whiteRookLeft'] = true;
        if ($fromCol === 7) $hasMoved['whiteRookRight'] = true;
    }

    if ($board[$toRow][$toCol] === "k") {
        $hasMoved['blackKing'] = true;
    }
    if ($board[$toRow][$toCol] === "r") {
        if ($fromCol === 0) $hasMoved['blackRookLeft'] = true;
        if ($fromCol === 7) $hasMoved['blackRookRight'] = true;
    }


    return applyTurns($turns, $board, $currentPlayer === "white" ? "black" : "white", $hasMoved);
}

function validateTurn($cords, $board, $player)
{
    $fromRow = $cords['fromRow'];
    $fromCol = $cords['fromCol'];
    $toRow = $cords['toRow'];
    $toCol = $cords['toCol'];
    $piece = $board[$fromRow][$fromCol];
    $board = $player === "black" ? array_reverse($board) : $board;
    $fromRow = $player === "black" ? 7 - $fromRow : $fromRow;
    $toRow = $player === "black" ? 7 - $toRow : $toRow;
    return validate(strtoupper($piece), $fromRow, $fromCol, $toRow, $toCol, $board);
}

function canCaptureKing($row, $col, $newRow, $newCol, $board, $currentPlayer, $hasMoved)
{
    $piece = $board[$row][$col];
    $newBoard = applyTurns([
        [
            "fromRow" => $row,
            "fromCol" => $col,
            "toRow"   => $newRow,
            "toCol"   => $newCol,
        ]
    ], $board, $hasMoved)["board"];

    if($row === 5 && $col === 6){
        $a = 1;
    }

    return !isCheck($currentPlayer, $newBoard);
}


function canCastle($board, $kingRow, $kingCol, $rookCol, $player)
{
    if (isCheck($player, $board)) return false;

    $direction = $rookCol > $kingCol ? 1 : -1;
    for ($col = $kingCol + $direction; $col !== $rookCol; $col += $direction) {
        if ($board[$kingRow][$col] !== "") return false;
        $newBoard = array_map(function ($row) {
            return $row;
        }, $board);
        $newBoard[$kingRow][$col] = $player === "white" ? "K" : "k";
        $newBoard[$kingRow][$kingCol] = "";
        if (isCheck($player, $newBoard)) {
            return false;
        }
    }
    return true;
}

function canPlayerMove($player, $board, $hasMoved)
{
    $playerFigures = $player === "white" ? "PNBRQK" : "pnbrqk";

    $possibleMoves = [];
    for ($i = 0; $i < 8; $i++) {
        for ($j = 0; $j < 8; $j++) {
            if ($board[$i][$j] === "" || !str_contains($playerFigures, $board[$i][$j])) {
                continue;
            }

            $possibleMoves = array_merge($possibleMoves, getPossibleMoves($i, $j, $board, $hasMoved));
        }
    }

    return !empty($possibleMoves);
}

function getPossibleMoves($row, $col, $board, $hasMoved)
{
    $piece = $board[$row][$col];

    $possibleMoves = [];

    if (strtoupper($piece) === $piece) { // white piece
        for ($i = 0; $i < 8; $i++) {
            for ($j = 0; $j < 8; $j++) {
                if (validate($piece, $row, $col, $i, $j, $board) && canCaptureKing($row, $col, $i, $j, $board, "white", $hasMoved)) {
                    if ($board[$i][$j] === "" || strtolower($board[$i][$j]) === $board[$i][$j]) {
                        $possibleMoves[] = [
                            'row' => $i,
                            'col' => $j
                        ];
                    }
                }
            }
        }

        if ($piece === "K" && !$hasMoved['whiteKing']) {
            if (!$hasMoved['whiteRookLeft'] && canCastle($board, $row, $col, 0, "white")) {
                $possibleMoves[] = [
                    'row' => $row,
                    'col' => $col - 2
                ];
            }
            if (!$hasMoved['whiteRookRight'] && canCastle($board, $row, $col, 7, "white")) {
                $possibleMoves[] = [
                    'row' => $row,
                    'col' => $col + 2
                ];
            }
        }
    } else {
        $reversedBoard = array_reverse(array_map(function ($row) {
            return array_slice($row, 0);
        }, $board));

        for ($i = 0; $i < 8; $i++) {
            for ($j = 0; $j < 8; $j++) {
                $valid = validate(strtoupper($piece), 7 - $row, $col, 7 - $i, $j, $reversedBoard);
                $canCapture = canCaptureKing(7 - $row, $col, 7 - $i, $j, $reversedBoard, "black", $hasMoved);
                if ($valid && $canCapture) {
                    if ($board[$i][$j] === "" || strtoupper($board[$i][$j]) === $board[$i][$j]) {
                        $possibleMoves[] = [
                            'row' => $i,
                            'col' => $j
                        ];
                    }
                }
            }
        }

        if ($piece === "k" && !$hasMoved['blackKing']) {
            if (!$hasMoved['blackRookLeft'] && canCastle($board, $row, $col, 0, "black")) {
                $possibleMoves[] = [
                    'row' => $row,
                    'col' => $col - 2
                ];
            }
            if (!$hasMoved['blackRookRight'] && canCastle($board, $row, $col, 7, "black")) {
                $possibleMoves[] = [
                    'row' => $row,
                    'col' => $col + 2
                ];
            }
        }
    }

    return $possibleMoves;
}


function validate($piece, $row, $col, $newRow, $newCol, $board)
{
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

function validatePawn($row, $col, $newRow, $newCol, $board)
{
    if ($col === $newCol) {
        if ($row === 6) {
            $a = ($newRow === $row - 1 || $newRow === $row - 2) && $board[$row - 1][$col] === "" && ($newRow === $row - 2 || $board[$newRow][$newCol] === "") && $board[$newRow][$newCol] === "";
        } else {
            $a = $newRow === $row - 1 && $board[$newRow][$newCol] === "";
        }
        return $a;
    } else {
        if (abs($newCol - $col) === 1) {
            if ($newRow === $row - 1 && $board[$newRow][$newCol] !== "") {
                return true;
            }
        }
        return false;
    }
}

function validateKnight($row, $col, $newRow, $newCol)
{
    $dx = abs($newRow - $row);
    $dy = abs($newCol - $col);
    return ($dx === 2 && $dy === 1) || ($dx === 1 && $dy === 2);
}

function validateBishop($row, $col, $newRow, $newCol, $board)
{
    if (abs($newRow - $row) === abs($newCol - $col)) {
        $directionX = $newRow > $row ? 1 : -1;
        $directionY = $newCol > $col ? 1 : -1;
        $i = $row + $directionX;
        $j = $col + $directionY;
        while ($i !== $newRow && $j !== $newCol) {
            if ($i < 0 || $i >= count($board) || $j < 0 || $j >= count($board[0])) return false;
            if ($board[$i][$j] !== "") return false;
            $i += $directionX;
            $j += $directionY;
        }
        return true;
    }
    return false;
}

function validateRook($row, $col, $newRow, $newCol, $board)
{
    if ($newRow === $row) {
        $direction = $newCol > $col ? 1 : -1;
        for ($i = $col + $direction; $i !== $newCol; $i += $direction) {
            if (@$board[$row][$i] !== "") return false;
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

function validateQueen($row, $col, $newRow, $newCol, $board)
{
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

function validateKing($row, $col, $newRow, $newCol, $board)
{
    $enemyFigures = $board[$row][$col] === strtoupper($board[$row][$col]) ? "pnbrqk" : "PNBRQK";
    $dx = abs($newRow - $row);
    $dy = abs($newCol - $col);
    return ($dx <= 1 && $dy <= 1) && str_contains($enemyFigures, $board[$newRow][$newCol]);
}

function getKing($player, $board)
{
    for ($i = 0; $i < 8; $i++) {
        for ($j = 0; $j < 8; $j++) {
            if ($board[$i][$j] === ($player === "white" ? "K" : "k")) {
                return [
                    "row" => $i,
                    "col" => $j
                ];
            }
        }
    }
    return [
        "row" => -1,
        "col" => -1
    ];
}

function isCheck($player, $board)
{
    $kingCords = getKing($player, $board);
    $kingRow = $kingCords["row"];
    $kingCol = $kingCords["col"];

    for ($i = 0; $i < 8; $i++) {
        for ($j = 0; $j < 8; $j++) {
            if ($player === "white") {
                if ($board[$i][$j] !== "" && strtoupper($board[$i][$j]) !== $board[$i][$j]) {
                    $reversedBoard = array_reverse(array_map(function ($row) {
                        return array_slice($row, 0);
                    }, $board));
                    if (validate(strtoupper($board[$i][$j]), 7 - $i, $j, 7 - $kingRow, $kingCol, $reversedBoard)) {
                        return true;
                    }
                }
            } else {
                if ($board[$i][$j] !== "" && strtolower($board[$i][$j]) !== $board[$i][$j]) {
                    if ($board[$i][$j] === "P") {
                        $reversedBoard = array_reverse(array_map(function ($row) {
                            return array_slice($row, 0);
                        }, $board));
                        if (validate($board[$i][$j], 7 - $i, $j, 7 - $kingRow, $kingCol, $reversedBoard)) {
                            return true;
                        }
                    }

                    if (validate($board[$i][$j], $i, $j, $kingRow, $kingCol, $board)) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}