import { DEFAULT_BOARD } from "../config/config";

export const turnToCords = (turn) => {
  if (["b00", "b000", "w00", "w000"].includes(turn)) {
    const row = turn[0] === "b" ? 0 : 7;
    const long = turn.length === 4;
    return {
      fromRow: row,
      fromCol: 4,
      toRow: row,
      toCol: long ? 2 : 6,
      castling: true,
      rookFromCol: long ? 0 : 7,
      rookToCol: long ? 3 : 5
    };
  }

  const charCodeShift = "a".charCodeAt(0);
  const numCodeShift = "1".charCodeAt(0);
  const cords = {
    fromCol: turn.charCodeAt(0) - charCodeShift,
    fromRow: turn.charCodeAt(1) - numCodeShift,
    toCol: turn.charCodeAt(2) - charCodeShift,
    toRow: turn.charCodeAt(3) - numCodeShift
  };

  const exchange = turn.length === 5 ? { newPiece: turn[4] } : {};

  return { ...cords, ...exchange };
};

export const cordsToTurn = (cords) => {
  if (cords.castling) {
    const color = cords.fromRow === 0 ? "b" : "w";
    const zeros = cords.rookToCol === 3 ? "000" : "00";
    return `${color}${zeros}`;
  }

  const charCodeShift = "a".charCodeAt(0);
  const numCodeShift = "1".charCodeAt(0);
  const turn = String.fromCharCode(
    cords.fromCol + charCodeShift,
    cords.fromRow + numCodeShift,
    cords.toCol + charCodeShift,
    cords.toRow + numCodeShift
  );

  return `${turn}${cords.newPiece ?? ""}`;
};

export const applyTurns = (turns, board = DEFAULT_BOARD, currentPlayer = "white", hasMoved = {
  whiteKing: false,
  whiteRookLeft: false,
  whiteRookRight: false,
  blackKing: false,
  blackRookLeft: false,
  blackRookRight: false
}) => {
  if (turns.length === 0) {
    return { board, hasMoved };
  }

  board = board.map(row => [...row]);

  const {
    fromRow,
    fromCol,
    toRow,
    toCol,
    castling,
    rookFromCol,
    rookToCol,
    newPiece
  } = turns.shift();

  if (castling) {
    const newBoard = board.map(row => [...row]);
    const piece = newBoard[fromRow][fromCol];
    newBoard[fromRow][fromCol] = "";
    newBoard[toRow][toCol] = piece;
    newBoard[fromRow][rookFromCol] = "";
    newBoard[fromRow][rookToCol] = currentPlayer === "white" ? "R" : "r";
    board = newBoard;
  } else {
    const piece = newPiece ?? board[fromRow][fromCol];
    board[toRow][toCol] = piece;
    board[fromRow][fromCol] = "";
  }

  if (currentPlayer === "white") {
    if (board[toRow][toCol] === "K") {
      hasMoved.whiteKing = true;
    }
    if (board[toRow][toCol] === "R") {
      if (fromCol === 0) hasMoved.whiteRookLeft = true;
      if (fromCol === 7) hasMoved.whiteRookRight = true;
    }
  } else {
    if (board[toRow][toCol] === "k") {
      hasMoved.blackKing = true;
    }
    if (board[toRow][toCol] === "r") {
      if (fromCol === 0) hasMoved.blackRookLeft = true;
      if (fromCol === 7) hasMoved.blackRookRight = true;
    }
  }

  return applyTurns(turns, board, currentPlayer === "white" ? "black" : "white", hasMoved);
};