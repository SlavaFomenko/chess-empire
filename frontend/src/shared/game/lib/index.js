import { DEFAULT_BOARD, DEFAULT_HAS_MOVED } from "../config/config";

export const turnToCords = (turn) => {
  const cords = {
    newPiece: null,
    rookCol: null
  };

  if (["b00", "b000", "w00", "w000"].includes(turn)) {
    const row = turn[0] === "b" ? 0 : 7;
    const long = turn.length === 4;
    cords.from = { row: row, col: 4 };
    cords.to = { row: row, col: long ? 2 : 6 };
    cords.rookCol = long ? 0 : 7;
    return cords;
  }

  const charCodeShift = "a".charCodeAt(0);
  const numCodeShift = "1".charCodeAt(0);
  cords.from = {
    row: turn[1].charCodeAt(0) - numCodeShift,
    col: turn[0].charCodeAt(0) - charCodeShift
  };
  cords.to = {
    row: turn[3].charCodeAt(0) - numCodeShift,
    col: turn[2].charCodeAt(0) - charCodeShift
  };

  if (turn.length === 5) {
    cords.newPiece = turn[4];
  }

  return cords;
};

export const cordsToTurn = (cords) => {
  if (cords.rookCol !== null) {
    const color = cords.from.row === 0 ? "b" : "w";
    const zeros = cords.rookCol === 0 ? "000" : "00";
    return color + zeros;
  }

  const charCodeShift = "a".charCodeAt(0);
  const numCodeShift = "1".charCodeAt(0);
  let turn = String.fromCharCode(
    cords.from.col + charCodeShift,
    cords.from.row + numCodeShift,
    cords.to.col + charCodeShift,
    cords.to.row + numCodeShift
  );

  if (cords.newPiece !== null) {
    turn += cords.newPiece;
  }

  return turn;
};

export const copyBoard = (board) => {
  return board.map(row => row.slice());
};

export const pieceColor = (piece) => {
  if ("PNBRQK".includes(piece)) {
    return "white";
  }
  if ("pnbrqk".includes(piece)) {
    return "black";
  }
  return "-";
};

export const enemyColor = (color) => color === "white" ? "black" : "white";

export const applyTurns = (turns, board = DEFAULT_BOARD, currentColor = "white", hasMoved = DEFAULT_HAS_MOVED) => {
  if (turns.length === 0) {
    return {
      board: board,
      hasMoved: hasMoved
    };
  }

  board = copyBoard(board);
  hasMoved = JSON.parse(JSON.stringify(hasMoved));

  const turn = turns.shift();

  const { from, to, rookCol, newPiece } = turn;
  const piece = board[from.row][from.col];
  const color = pieceColor(piece);

  if (turn.rookCol !== null) {
    board[from.row][from.col] = "-";
    board[to.row][to.col] = piece;
    board[from.row][rookCol === 0 ? 3 : 5] = board[from.row][rookCol];
    board[from.row][rookCol] = "-";

    hasMoved[color].king = true;
    hasMoved[color][rookCol === 0 ? "rookLeft" : "rookRight"] = true;

    return applyTurns(turns, board, enemyColor(color), hasMoved);
  }

  if (!hasMoved[color].king && "kK".includes(piece)) {
    hasMoved[color].king = true;
  }

  if ("rR".includes(piece)) {
    if (from.row === (color === "white" ? 7 : 0)) {
      if (from.col === 0) {
        hasMoved[color].rookLeft = true;
      } else if (from.col === 7) {
        hasMoved[color].rookRight = true;
      }
    }
  }

  if ("rR".includes(board[to.row][to.col])) {
    if (to.row === (enemyColor(color) === "white" ? 7 : 0)) {
      if (to.col === 0) {
        hasMoved[enemyColor(color)].rookLeft = true;
      } else if (to.col === 7) {
        hasMoved[enemyColor(color)].rookRight = true;
      }
    }
  }

  board[to.row][to.col] = newPiece ?? piece;
  board[from.row][from.col] = "-";

  return applyTurns(turns, board, enemyColor(currentColor), hasMoved);
};

export const isCordsValid = ({ row, col }) => row >= 0 && row < 8 && col >= 0 && col < 8;

export const isCordsBusy = (from, to, board) => pieceColor(board[from.row][from.col]) === pieceColor(board[to.row][to.col]);

const possibleMovesFns = {
  "P": ({ row, col }, board) => {
    const possibleMoves = [];

    let turn = { row: row - 1, col: col - 1 };
    if (isCordsValid(turn) && pieceColor(board[row - 1][col - 1]) === enemyColor(pieceColor(board[row][col]))) {
      possibleMoves.push(turn);
    }

    turn = { row: row - 1, col: col + 1 };
    if (isCordsValid(turn) && pieceColor(board[row - 1][col + 1]) === enemyColor(pieceColor(board[row][col]))) {
      possibleMoves.push(turn);
    }

    if (board[row - 1][col] === "-") {
      possibleMoves.push({ row: row - 1, col: col });
      if (row === 6 && board[4][col] === "-") {
        possibleMoves.push({ row: 4, col: col });
      }
    }

    return possibleMoves;
  },

  "N": ({ row, col }, board) => {
    const possibleMoves = [
      { row: row - 2, col: col - 1 },
      { row: row - 2, col: col + 1 },
      { row: row - 1, col: col - 2 },
      { row: row - 1, col: col + 2 },
      { row: row + 1, col: col - 2 },
      { row: row + 1, col: col + 2 },
      { row: row + 2, col: col - 1 },
      { row: row + 2, col: col + 1 }
    ];

    return possibleMoves.filter(isCordsValid).filter(to => !isCordsBusy({ row, col }, to, board));
  },

  "B": ({ row, col }, board) => {
    const possibleMoves = [];

    for (let i = 1; i < 8; i++) {
      const to = { row: row + i, col: col + i };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row + i][col + i] !== "-") break;
    }

    for (let i = 1; i < 8; i++) {
      const to = { row: row - i, col: col + i };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row - i][col + i] !== "-") break;
    }

    for (let i = 1; i < 8; i++) {
      const to = { row: row + i, col: col - i };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row + i][col - i] !== "-") break;
    }

    for (let i = 1; i < 8; i++) {
      const to = { row: row - i, col: col - i };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row - i][col - i] !== "-") break;
    }

    return possibleMoves;
  },

  "R": ({ row, col }, board) => {
    const possibleMoves = [];

    for (let i = 1; i < 8; i++) {
      const to = { row: row + i, col: col };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row + i][col] !== "-") break;
    }

    for (let i = 1; i < 8; i++) {
      const to = { row: row - i, col: col };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row - i][col] !== "-") break;
    }

    for (let i = 1; i < 8; i++) {
      const to = { row: row, col: col + i };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row][col + i] !== "-") break;
    }

    for (let i = 1; i < 8; i++) {
      const to = { row: row, col: col - i };
      if (!isCordsValid(to)) continue;
      possibleMoves.push(to);
      if (board[row][col - i] !== "-") break;
    }

    return possibleMoves;
  },

  "Q": ({ row, col }, board) => {
    return [...possibleMovesFns["R"]({ row, col }, board), ...possibleMovesFns["B"]({ row, col }, board)];
  },

  "K": ({ row, col }, board) => {
    const possibleMoves = [
      { row: row - 1, col: col - 1 },
      { row: row - 1, col: col },
      { row: row - 1, col: col + 1 },
      { row: row, col: col - 1 },
      { row: row, col: col + 1 },
      { row: row + 1, col: col - 1 },
      { row: row + 1, col: col },
      { row: row + 1, col: col + 1 }
    ];

    if (row === 7 && col === 4) {
      possibleMoves.push({ row: 7, col: 2 });
      possibleMoves.push({ row: 7, col: 6 });
    }

    return possibleMoves.filter(isCordsValid).filter(to => !isCordsBusy({ row, col }, to, board));
  }
};

export const getPossibleMoves = (board, cords) => {
  const piece = board[cords.row][cords.col];

  if (piece === "p" || piece === "k") {
    return possibleMovesFns[piece.toUpperCase()]({
      ...cords,
      row: 7 - cords.row
    }, board.map(row => [...row]).reverse()).map(to => ({
      ...to,
      row: 7 - to.row
    }));
  }

  return possibleMovesFns[piece.toUpperCase()](cords, board);
};

export const validateMove = (board, from, to, hasMoved) => {
  const possibleMoves = getPossibleMoves(board, from);
  if (!possibleMoves.some(move => move.row === to.row && move.col === to.col)) {
    return false;
  }

  const piece = board[from.row][from.col];
  const target = board[to.row][to.col];

  if (pieceColor(piece) === pieceColor(target)) {
    return false;
  }

  if ("pP".includes(piece) && from.col !== to.col && target === "-") {
    return false;
  }

  if ("kK".includes(piece) && Math.abs(from.col - to.col) === 2) {
    return canCastle(board, pieceColor(piece), to.col === 2 ? 0 : 7, hasMoved);
  }

  const turn = {
    from: from,
    to: to,
    newPiece: null,
    rookCol: null
  };

  const { board: newBoard, hasMoved: newHasMoved } = applyTurns([turn], board, pieceColor(piece), hasMoved);

  return !isCheck(newBoard, pieceColor(piece), newHasMoved);
};

export const isCheck = (board, color, hasMoved) => {
  const enemyPieces = [];
  let kingCords = {};
  const kingPiece = color === "white" ? "K" : "k";

  for (let i = 0; i < 8; i++) {
    for (let j = 0; j < 8; j++) {
      const piece = board[i][j];
      if (piece === kingPiece) {
        kingCords = {
          row: i,
          col: j
        };
      } else if (pieceColor(piece) === enemyColor(color)) {
        enemyPieces.push({
          row: i,
          col: j
        });
      }
    }
  }

  for (const enemyPiece of enemyPieces) {
    if (validateMove(board, enemyPiece, kingCords, hasMoved)) {
      return true;
    }
  }

  return false;
};

export const canCastle = (board, color, rookCol, hasMoved) => {
  if (hasMoved[color]["king"] || hasMoved[color][rookCol === 0 ? "rookLeft" : "rookRight"]) {
    return false;
  }

  const row = color === "white" ? 7 : 0;
  const colsToCheck = rookCol === 0 ? [1, 2, 3] : [5, 6];
  for (const col of colsToCheck) {
    if (board[row][col] !== "-") {
      return false;
    }
  }

  if (isCheck(board, color, hasMoved)) {
    return false;
  }

  const turn = {
    from: {
      row: row,
      col: 4
    },
    to: {
      row: row,
      col: rookCol === 0 ? 2 : 6
    },
    newPiece: null,
    rookCol: rookCol
  };

  const { board: newBoard, hasMoved: newHasMoved } = applyTurns([turn], board, color, hasMoved);

  return !isCheck(newBoard, color, newHasMoved);
};