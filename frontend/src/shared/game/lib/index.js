import { DEFAULT_BOARD } from "../config/config";

export const turnToCords = (turn) => {
  if(["b00", "b000", "w00", "w000"].includes(turn)){
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
    }
  }

  const charCodeShift = "a".charCodeAt(0);
  const numCodeShift = "1".charCodeAt(0);
  const cords = {
    fromCol: turn.charCodeAt(0) - charCodeShift,
    fromRow: turn.charCodeAt(1) - numCodeShift,
    toCol: turn.charCodeAt(2) - charCodeShift,
    toRow: turn.charCodeAt(3) - numCodeShift
  };

  const exchange = turn.length === 5 ? {newPiece: turn[5]} : {};

  return {...cords, exchange};
}

export const cordsToTurn = (cords) => {
  if(cords.castling){
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
  )

  return `${turn}${cords.newPiece ?? ""}`;
}

export const applyTurns = (turns, board = DEFAULT_BOARD) => {
  if (turns.length === 0) {
    return board;
  }

  board = board.map(row => [...row]);

  const { fromRow, fromCol, toRow, toCol } = turns.shift()
  const piece = board[fromRow][fromCol];
  board[toRow][toCol] = piece;
  board[fromRow][fromCol] = "";

  return applyTurns(turns, board);
}