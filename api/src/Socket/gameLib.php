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
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-"
    ],
    [
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-"
    ],
    [
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-"
    ],
    [
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-",
        "-"
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

const DEFAULT_HAS_MOVED = [
    "white" => [
        "king"      => false,
        "rookLeft"  => false,
        "rookRight" => false
    ],
    "black" => [
        "king"      => false,
        "rookLeft"  => false,
        "rookRight" => false
    ],
];

/**
 * @param string $turn
 * @return array Array with "from", "to", "newPiece" and "rookCol" keys
 */
function turnToCords(string $turn): array
{
    $cords = [
        "newPiece" => null,
        "rookCol"  => null,
    ];

    if (in_array($turn, [
        "b00",
        "b000",
        "w00",
        "w000"
    ])) {
        $row = $turn[0] === "b" ? 0 : 7;
        $long = strlen($turn) === 4;
        $cords["from"] = [
            "row" => $row,
            "col" => 4,
        ];
        $cords["to"] = [
            "row" => $row,
            "col" => $long ? 2 : 6,
        ];
        $cords["rookCol"] = $long ? 0 : 7;
        return $cords;
    }

    $charCodeShift = ord("a");
    $numCodeShift = ord("1");
    $cords["from"] = [
        "row" => ord($turn[1]) - $numCodeShift,
        "col" => ord($turn[0]) - $charCodeShift,
    ];
    $cords["to"] = [
        "row" => ord($turn[3]) - $numCodeShift,
        "col" => ord($turn[2]) - $charCodeShift,
    ];

    if (strlen($turn) === 5) {
        $cords["newPiece"] = $turn[4];
    }

    return $cords;
}

/**
 * @param array $cords
 * @return string Turn notation in "a1b2" or "b000" format
 */
function cordsToTurn(array $cords): string
{
    if ($cords["rookCol"] !== null) {
        $color = $cords["from"]["row"] === 0 ? "b" : "w";
        $zeros = $cords["rookCol"] === 0 ? "000" : "00";
        return $color . $zeros;
    }

    $charCodeShift = ord("a");
    $numCodeShift = ord("1");
    $turn = chr($cords["from"]["col"] + $charCodeShift) .
        chr($cords["from"]["row"] + $numCodeShift) .
        chr($cords["to"]["col"] + $charCodeShift) .
        chr($cords["to"]["row"] + $numCodeShift);

    if ($cords["newPiece"] !== null) {
        $turn .= $cords["newPiece"];
    }

    return $turn;
}

function copyBoard(array $board): array
{
    return array_map(fn($row) => array_slice($row, 0), $board);
}

function pieceColor(string $piece): string
{
    if (str_contains("PNBRQK", $piece)) {
        return "white";
    }
    if (str_contains("pnbqrk", $piece)) {
        return "black";
    }
    return "-";
}

function enemyColor(string $color): string
{
    return $color === "white" ? "black" : "white";
}

/**
 * @param array $turns
 * @param array $board
 * @param string $currentPlayer
 * @param array $hasMoved
 * @return array Array with "board" and "hasMoved" keys
 */
function applyTurns(array $turns, array $board = DEFAULT_BOARD, string $currentPlayer = "white", array $hasMoved = DEFAULT_HAS_MOVED): array
{
    if (empty($turns)) {
        return [
            "board"    => $board,
            "hasMoved" => $hasMoved
        ];
    }

    $board = copyBoard($board);
    $hasMoved = (new ArrayObject($hasMoved))->getArrayCopy();

    $turn = array_shift($turns);

    $from = $turn["from"];
    $to = $turn["to"];
    $rookCol = $turn["rookCol"];
    $newPiece = $turn["newPiece"];
    $piece = $board[$from["row"]][$from["col"]];
    $color = pieceColor($piece);

    if ($turn["rookCol"] !== null) {
        $board[$from["row"]][$from["col"]] = "-";
        $board[$to["row"]][$to["col"]] = $piece;
        $board[$from["row"]][$rookCol === 0 ? 3 : 5] = $board[$from["row"]][$rookCol];
        $board[$from["row"]][$rookCol] = "-";

        $hasMoved[$color]["king"] = true;
        $hasMoved[$color][$rookCol === 0 ? "rookLeft" : "rookRight"] = true;

        return applyTurns($turns, $board, enemyColor($color), $hasMoved);
    }

    if (!$hasMoved[$color]["king"] && str_contains("kK", $piece)) {
        $hasMoved[$color]["king"] = true;
    }

    if (str_contains("rR", $piece)) {
        if ($from["row"] === ($color === "white" ? 7 : 0)) {
            if ($from["col"] === 0) {
                $hasMoved[$color]["rookLeft"] = true;
            } else if ($from["col"] === 7) {
                $hasMoved[$color]["rookRight"] = true;
            }
        }
    }

    if (str_contains("rR", $board[$to["row"]][$to["col"]])) {
        if ($to["row"] === (enemyColor($color) === "white" ? 7 : 0)) {
            if ($to["col"] === 0) {
                $hasMoved[enemyColor($color)]["rookLeft"] = true;
            } else if ($to["col"] === 7) {
                $hasMoved[enemyColor($color)]["rookRight"] = true;
            }
        }
    }

    $board[$to["row"]][$to["col"]] = $newPiece ?? $piece;
    $board[$from["row"]][$from["col"]] = "-";

    return applyTurns($turns, $board, $currentPlayer === "white" ? "black" : "white", $hasMoved);
}

function isCordsValid(array $cords): bool
{
    return $cords["row"] >= 0 && $cords["row"] < 8 && $cords["col"] >= 0 && $cords["col"] < 8;
}

function isCordsBusy(array $from, array $to, array $board): bool
{
    return pieceColor($board[$from["row"]][$from["col"]]) === pieceColor($board[$to["row"]][$to["col"]]);
}

function getPossibleMoves(array $board, array $cords): array
{
    $piece = $board[$cords["row"]][$cords["col"]];

    if (!str_contains("PNBRQK", strtoupper($piece))) {
        return [];
    }

    $possibleMovesFns = [
        "P" => "getPossibleMovesP",
        "N" => "getPossibleMovesN",
        "B" => "getPossibleMovesB",
        "R" => "getPossibleMovesR",
        "Q" => "getPossibleMovesQ",
        "K" => "getPossibleMovesK"
    ];

    if ($piece === "p" || $piece === "k") {
        $cords["row"] = 7 - $cords["row"];
        return array_map(fn($c) => [
            "row" => 7 - $c["row"],
            "col" => $c["col"]
        ], $possibleMovesFns[strtoupper($piece)]($cords, array_reverse($board)));
    }

    return $possibleMovesFns[strtoupper($piece)]($cords, $board);
}

function getPossibleMovesP(array $cords, array $board): array
{
    [
        "row" => $row,
        "col" => $col
    ] = $cords;
    $possibleMoves = [];

    $turn = [
        'row' => $row - 1,
        'col' => $col - 1
    ];
    if (isCordsValid($turn) && pieceColor($board[$row - 1][$col - 1]) === enemyColor(pieceColor($board[$row][$col]))) {
        $possibleMoves[] = $turn;
    }

    $turn = [
        'row' => $row - 1,
        'col' => $col + 1
    ];
    if (isCordsValid($turn) && pieceColor($board[$row - 1][$col + 1]) === enemyColor(pieceColor($board[$row][$col]))) {
        $possibleMoves[] = $turn;
    }

    if ($board[$row - 1][$col] === "-") {
        $possibleMoves[] = [
            'row' => $row - 1,
            'col' => $col
        ];
        if ($row === 6 && $board[4][$col] === "-") {
            $possibleMoves[] = [
                'row' => 4,
                'col' => $col
            ];
        }
    }

    return $possibleMoves;
}

function getPossibleMovesN(array $cords, array $board): array
{
    [
        "row" => $row,
        "col" => $col
    ] = $cords;
    $possibleMoves = [
        [
            'row' => $row - 2,
            'col' => $col - 1
        ],
        [
            'row' => $row - 2,
            'col' => $col + 1
        ],
        [
            'row' => $row - 1,
            'col' => $col - 2
        ],
        [
            'row' => $row - 1,
            'col' => $col + 2
        ],
        [
            'row' => $row + 1,
            'col' => $col - 2
        ],
        [
            'row' => $row + 1,
            'col' => $col + 2
        ],
        [
            'row' => $row + 2,
            'col' => $col - 1
        ],
        [
            'row' => $row + 2,
            'col' => $col + 1
        ]
    ];

    $possibleMoves = array_filter($possibleMoves, 'isCordsValid');
    return array_filter($possibleMoves, fn($to) => !isCordsBusy($cords, $to, $board));
}

function getPossibleMovesB(array $cords, array $board): array
{
    [
        "row" => $row,
        "col" => $col
    ] = $cords;
    $possibleMoves = array();

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row + $i,
            'col' => $col + $i
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row + $i][$col + $i] !== "-") break;
    }

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row - $i,
            'col' => $col + $i
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row - $i][$col + $i] !== "-") break;
    }

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row + $i,
            'col' => $col - $i
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row + $i][$col - $i] !== "-") break;
    }

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row - $i,
            'col' => $col - $i
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row - $i][$col - $i] !== "-") break;
    }

    return $possibleMoves;
}

function getPossibleMovesR(array $cords, array $board): array
{
    [
        "row" => $row,
        "col" => $col
    ] = $cords;
    $possibleMoves = array();

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row + $i,
            'col' => $col
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row + $i][$col] !== "-") break;
    }

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row - $i,
            'col' => $col
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row - $i][$col] !== "-") break;
    }

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row,
            'col' => $col + $i
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row][$col + $i] !== "-") break;
    }

    for ($i = 1; $i < 8; $i++) {
        $to = array(
            'row' => $row,
            'col' => $col - $i
        );
        if (!isCordsValid($to)) continue;
        $possibleMoves[] = $to;
        if ($board[$row][$col - $i] !== "-") break;
    }

    return $possibleMoves;
}

function getPossibleMovesQ(array $cords, array $board): array
{
    return array_merge(getPossibleMovesR($cords, $board), getPossibleMovesB($cords, $board));
}

function getPossibleMovesK(array $cords, array $board): array
{
    [
        "row" => $row,
        "col" => $col
    ] = $cords;
    $possibleMoves = [
        [
            'row' => $row - 1,
            'col' => $col - 1
        ],
        [
            'row' => $row - 1,
            'col' => $col
        ],
        [
            'row' => $row - 1,
            'col' => $col + 1
        ],
        [
            'row' => $row,
            'col' => $col - 1
        ],
        [
            'row' => $row,
            'col' => $col + 1
        ],
        [
            'row' => $row + 1,
            'col' => $col - 1
        ],
        [
            'row' => $row + 1,
            'col' => $col
        ],
        [
            'row' => $row + 1,
            'col' => $col + 1
        ]
    ];

    if ($row === 7 && $col === 4) {
        $possibleMoves[] = [
            'row' => 7,
            'col' => 2
        ];
        $possibleMoves[] = [
            'row' => 7,
            'col' => 6
        ];
    }

    $possibleMoves = array_filter($possibleMoves, 'isCordsValid');
    return array_filter($possibleMoves, fn($to) => !isCordsBusy($cords, $to, $board));
}

function validateMove(array $board, array $from, array $to, array $hasMoved): bool
{
    if (!in_array($to, getPossibleMoves($board, $from))) {
        return false;
    }

    $piece = $board[$from["row"]][$from["col"]];
    $target = $board[$to["row"]][$to["col"]];

    if (pieceColor($piece) === pieceColor($target)) {
        return false;
    }

    if (str_contains("pP", $piece) && $from["col"] !== $to["col"] && $target === "-") {
        return false;
    }

    if (str_contains("kK", $piece) && abs($from["col"] !== $to["col"]) === 2) {
        return canCastle($board, pieceColor($piece), $to["col"] === 2 ? 0 : 7, $hasMoved);
    }

    $turn = [
        "from"     => $from,
        "to"       => $to,
        "newPiece" => null,
        "rookCol"  => null,
    ];

    [
        "board"    => $newBoard,
        "hasMoved" => $newHasMoved
    ] = applyTurns([$turn], $board, pieceColor($piece), $hasMoved);

    return !isCheck($newBoard, pieceColor($piece), $newHasMoved);
}

function isCheck(array $board, string $color, array $hasMoved): bool
{
    $enemyPieces = [];
    $kingCords = [];
    $kingPiece = $color === "white" ? "K" : "k";
    for ($i = 0; $i < 8; $i += 1) {
        for ($j = 0; $j < 8; $j += 1) {
            $piece = $board[$i][$j];
            if ($piece === $kingPiece) {
                $kingCords = [
                    "row" => $i,
                    "col" => $j,
                ];
            } else if (pieceColor($piece) === enemyColor($color)) {
                $enemyPieces[] = [
                    "row" => $i,
                    "col" => $j,
                ];
            }
        }
    }

    foreach ($enemyPieces as $enemyPiece) {
        if (validateMove($board, $enemyPiece, $kingCords, $hasMoved)) {
            return true;
        }
    }

    return false;
}

function canCastle(array $board, string $color, int $rookCol, array $hasMoved): bool
{
    if ($hasMoved[$color]["king"] || $hasMoved[$color][$rookCol === 0 ? "rookLeft" : "rookRight"]) {
        return false;
    }

    $row = $color === "white" ? 7 : 0;
    $colsToCheck = $rookCol === 0 ? [
        1,
        2,
        3
    ] : [
        5,
        6
    ];
    foreach ($colsToCheck as $col) {
        if ($board[$row][$col] !== "-") {
            return false;
        }
    }

    if (isCheck($board, $color, $hasMoved)) {
        return false;
    }

    $turn = [
        "from"     => [
            "row" => $row,
            "col" => 4,
        ],
        "to"       => [
            "row" => $row,
            "col" => $rookCol === 0 ? 2 : 6,
        ],
        "newPiece" => null,
        "rookCol"  => $rookCol,
    ];

    [
        "board"    => $newBoard,
        "hasMoved" => $newHasMoved
    ] = applyTurns([$turn], $board, $color, $hasMoved);

    return !isCheck($newBoard, $color, $newHasMoved);
}

function getValidMoves(array $board, array $from, array $hasMoved): array
{
    return array_filter(getPossibleMoves($board, $from), fn($to) => validateMove($board, $from, $to, $hasMoved));
}

function canPlayerMove(string $color, array $board, array $hasMoved): bool
{
    for ($i = 0; $i < 8; $i++) {
        for ($j = 0; $j < 8; $j++) {
            $from = [
                "row" => $i,
                "col" => $j
            ];
            if (pieceColor($board[$i][$j]) === $color && !empty(getValidMoves($board, $from, $hasMoved))) {
                $b = getValidMoves($board, $from, $hasMoved);
                return true;
            }
        }
    }
    return false;
}