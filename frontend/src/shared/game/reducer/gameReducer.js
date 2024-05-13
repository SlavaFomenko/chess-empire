import { createSlice } from "@reduxjs/toolkit";

export const gameSlice = createSlice({
  name: "game",
  initialState: {
    initialBoard: [
      ["r", "n", "b", "q", "k", "b", "n", "r"],
      ["p", "p", "p", "p", "p", "p", "p", "p"],
      ["", "", "", "", "", "", "", ""],
      ["", "", "", "", "", "", "", ""],
      ["", "", "", "", "", "", "", ""],
      ["", "", "", "", "", "", "", ""],
      ["P", "P", "P", "P", "P", "P", "P", "P"],
      ["R", "N", "B", "Q", "K", "B", "N", "R"]
    ],
    selectedPiece: null,
    currentPlayer: "white",
    possibleMoves: [],
    check: false,
    gameHistory: [],
    currentStep: 0,
    moveAllowed: true
  },
  reducers: {
    selectPiece: (state, action) => {
      if (action.payload === null || !state.moveAllowed) {
        state.selectedPiece = null;
        state.possibleMoves = [];
        return;
      }
      const { row, col } = action.payload;
      const piece = state.initialBoard[row][col];
      const possibleMoves = [];
      const validateFn = validate[piece.toUpperCase()];

      if (piece !== "" && (state.currentPlayer === "white" && piece.toUpperCase() === piece)) {
        if (state.check) {
          for (let i = 0; i < 8; i++) {
            for (let j = 0; j < 8; j++) {
              if (validate[piece](row, col, i, j, state.initialBoard) && canCaptureKing(row, col, i, j, state.initialBoard, state.currentPlayer)) {
                possibleMoves.push({ row: i, col: j });
              }
            }
          }
        } else {
          for (let i = 0; i < 8; i++) {
            for (let j = 0; j < 8; j++) {
              if (validateFn(row, col, i, j, state.initialBoard)) {
                if (state.initialBoard[i][j] === "" ||
                  (state.currentPlayer === "white" &&
                    state.initialBoard[i][j].toLowerCase() === state.initialBoard[i][j])) {
                  possibleMoves.push({ row: i, col: j });
                }
              }
            }
          }
        }
      }
      if (piece !== "" && (state.currentPlayer === "black" && piece.toLowerCase() === piece)) {
        const reversedBoard = state.initialBoard.map(row => [...row]).reverse();
        const validateFn = validate[piece.toUpperCase()];
        if (state.check) {
          for (let i = 0; i < 8; i++) {
            for (let j = 0; j < 8; j++) {
              if (validateFn(7 - row, col, 7 - i, j, reversedBoard) && canCaptureKing(7 - row, col, 7 - i, j, reversedBoard, state.currentPlayer)) {
                possibleMoves.push({ row: i, col: j });
              }
            }
          }
        } else {
          for (let i = 0; i < 8; i++) {
            for (let j = 0; j < 8; j++) {
              if (validateFn(7 - row, col, 7 - i, j, reversedBoard)) {
                if (state.initialBoard[i][j] === "" ||
                  (state.currentPlayer === "black" &&
                    state.initialBoard[i][j].toUpperCase() === state.initialBoard[i][j])) {
                  possibleMoves.push({ row: i, col: j });
                }
              }
            }
          }
        }
      }
      if (possibleMoves.length > 0) {
        state.selectedPiece = { row, col };
        state.possibleMoves = possibleMoves;
      }
    },
    movePiece: (state, action) => {
      const { row, col } = state.selectedPiece;
      const { newRow, newCol } = action.payload;
      const piece = state.initialBoard[row][col];
      if (piece && state.currentPlayer === "white" && piece.toUpperCase() === piece) {
        if (validate[`${piece}`](row, col, newRow, newCol, state.initialBoard)) {
          if (state.initialBoard[newRow][newCol] === "" || state.initialBoard[newRow][newCol].toLowerCase() === state.initialBoard[newRow][newCol]) {
            const newBoard = state.initialBoard.map(row => [...row]);
            newBoard[newRow][newCol] = piece;
            newBoard[row][col] = "";
            if (state.check && !state.possibleMoves.some(move => move.row === newRow && move.col === newCol)) {
              return state;
            }
            const check = isCheck(state.currentPlayer === "white" ? "black" : "white", newBoard);

            const newGameHistory = [...state.gameHistory, { fromRow: row, fromCol: col, toRow: newRow, toCol: newCol }];

            return {
              ...state,
              initialBoard: newBoard,
              selectedPiece: null,
              possibleMoves: [],
              currentPlayer: "black",
              gameHistory: newGameHistory,
              currentStep: state.currentStep + 1,
              check
            };
          }
        }
      } else if (piece && state.currentPlayer === "black" && piece.toLowerCase() === piece) {
        const reversedBoard = state.initialBoard.map(row => [...row]).reverse();
        if (validate[`${piece.toUpperCase()}`](7 - row, col, 7 - newRow, newCol, reversedBoard)) {
          if (state.initialBoard[newRow][newCol] === "" || state.initialBoard[newRow][newCol].toUpperCase() === state.initialBoard[newRow][newCol]) {
            const newBoard = reversedBoard.map(row => [...row]).reverse();
            newBoard[newRow][newCol] = piece;
            newBoard[row][col] = "";

            if (state.check && !state.possibleMoves.some(move => move.row === newRow && move.col === newCol)) {
              return state;
            }

            const check = isCheck(state.currentPlayer === "white" ? "black" : "white", newBoard);
            const newGameHistory = [...state.gameHistory, { fromRow: row, fromCol: col, toRow: newRow, toCol: newCol }];
            return {
              ...state,
              initialBoard: newBoard,
              selectedPiece: null,
              possibleMoves: [],
              currentPlayer: "white",
              gameHistory: newGameHistory,
              currentStep: state.currentStep + 1,
              check
            };
          }
        }
      }
      return state;
    },
    applyTurn: (state) => {
      if (state.currentStep >= state.gameHistory.length) {
        return state;
      }

      const { fromRow, fromCol, toRow, toCol } = state.gameHistory[state.currentStep];
      const piece = state.initialBoard[fromRow][fromCol];
      const newBoard = state.initialBoard.map(row => [...row]);
      newBoard[toRow][toCol] = piece;
      newBoard[fromRow][fromCol] = "";

      const check = isCheck(state.currentPlayer === "white" ? "black" : "white", newBoard);

      return {
        ...state,
        initialBoard: newBoard,
        selectedPiece: null,
        currentPlayer: state.currentPlayer === "white" ? "white" : "black",
        currentStep: state.currentStep + 1,
        check,
        moveAllowed: state.currentStep + 1 === state.gameHistory.length
      };
    },
    undoTurn: (state) => {
      const board = [
        ["r", "n", "b", "q", "k", "b", "n", "r"],
        ["p", "p", "p", "p", "p", "p", "p", "p"],
        ["", "", "", "", "", "", "", ""],
        ["", "", "", "", "", "", "", ""],
        ["", "", "", "", "", "", "", ""],
        ["", "", "", "", "", "", "", ""],
        ["P", "P", "P", "P", "P", "P", "P", "P"],
        ["R", "N", "B", "Q", "K", "B", "N", "R"]
      ];

      const index = state.gameHistory.findIndex((move, index) => index + 1 === state.currentStep);
      const historyToUndo = index !== -1 ? state.gameHistory.slice(0, index) : [];

      for (let i = 0; i < historyToUndo.length; i++) {
        const move = historyToUndo[i];
        const { fromRow, fromCol, toRow, toCol } = move;

        board[toRow][toCol] = board[fromRow][fromCol];
        board[fromRow][fromCol] = "";
      }

      return {
        ...state,
        initialBoard: board,
        currentStep: state.currentStep > 0 ? state.currentStep - 1 : 0,
        selectedPiece: null,
        moveAllowed: false
      };
    }
  }
});

function canCaptureKing (row, col, newRow, newCol, board, currentPlayer) {
  const piece = board[row][col];
  const newBoard = board.map(row => [...row]);

  if (newBoard[newRow][newCol] === "" ||
    (currentPlayer === "white" &&
      newBoard[newRow][newCol] === newBoard[newRow][newCol].toLowerCase()) || (currentPlayer === "black" &&
      newBoard[newRow][newCol] === newBoard[newRow][newCol].toUpperCase())) {
    newBoard[newRow][newCol] = piece;
    newBoard[row][col] = "";
  } else {
    return false;
  }

  const check = isCheck(currentPlayer, newBoard);

  return !check;
}

const validate = {
  "P": (row, col, newRow, newCol, board) => {
    if (col === newCol) {
      if (row === 6) {
        return (newRow === row - 1 || newRow === row - 2) && board[row - 1][col] === "" && (newRow === row - 2 || board[newRow][newCol] === "") && board[newRow][newCol] === "";
      } else {
        return newRow === row - 1 && board[newRow][newCol] === "";
      }
    } else {
      if (Math.abs(newCol - col) === 1) {
        if (newRow === row - 1 && board[newRow][newCol] !== "") {
          return true;
        }
      }
      return false;
    }
  },

  "N": (row, col, newRow, newCol) => {
    const dx = Math.abs(newRow - row);
    const dy = Math.abs(newCol - col);
    return (dx === 2 && dy === 1) || (dx === 1 && dy === 2);
  },

  "B": (row, col, newRow, newCol, board) => {
    if (Math.abs(newRow - row) === Math.abs(newCol - col)) {
      const directionX = newRow > row ? 1 : -1;
      const directionY = newCol > col ? 1 : -1;
      let i = row + directionX;
      let j = col + directionY;
      while (i !== newRow && j !== newCol) {
        if (board[i][j] !== "") return false;
        i += directionX;
        j += directionY;
      }
      return true;
    }
    return false;
  },

  "R": (row, col, newRow, newCol, board) => {
    if (newRow === row) {
      const direction = newCol > col ? 1 : -1;
      for (let i = col + direction; i !== newCol; i += direction) {
        if (board[row][i] !== "") return false;
      }
      return true;
    } else if (newCol === col) {
      const direction = newRow > row ? 1 : -1;
      for (let i = row + direction; i !== newRow; i += direction) {
        if (board[i][col] !== "") return false;
      }
      return true;
    }
    return false;
  },
  "Q": (row, col, newRow, newCol, board) => {
    if (Math.abs(newRow - row) === Math.abs(newCol - col)) {
      const directionX = newRow > row ? 1 : -1;
      const directionY = newCol > col ? 1 : -1;
      let i = row + directionX;
      let j = col + directionY;
      while (i !== newRow && j !== newCol) {
        if (board[i][j] !== "") return false;
        i += directionX;
        j += directionY;
      }
      return true;
    } else if (newRow === row) {
      const direction = newCol > col ? 1 : -1;
      for (let i = col + direction; i !== newCol; i += direction) {
        if (board[row][i] !== "") return false;
      }
      return true;
    } else if (newCol === col) {
      const direction = newRow > row ? 1 : -1;
      for (let i = row + direction; i !== newRow; i += direction) {
        if (board[i][col] !== "") return false;
      }
      return true;
    }
    return false;
  },

  "K": (row, col, newRow, newCol) => {
    const dx_k = Math.abs(newRow - row);
    const dy_k = Math.abs(newCol - col);
    return (dx_k <= 1 && dy_k <= 1);
  }
};

const isCheck = (player, board) => {
  let kingRow, kingCol;

  for (let i = 0; i < 8; i++) {
    for (let j = 0; j < 8; j++) {
      if (board[i][j] === (player === "white" ? "K" : "k")) {
        kingRow = i;
        kingCol = j;
        break;
      }
    }
  }
  for (let i = 0; i < 8; i++) {
    for (let j = 0; j < 8; j++) {
      if (player === "white") {
        if (board[i][j] !== "" && board[i][j].toUpperCase() !== board[i][j]) {
          const validateFn = validate[board[i][j].toUpperCase()];
          const reversedBoard = board.map(row => [...row]).reverse();
          return validateFn(7 - i, j, 7 - kingRow, kingCol, reversedBoard);
        }
      } else {
        if (board[i][j] !== "" && board[i][j].toLowerCase() !== board[i][j]) {
          if (board[i][j] === "P") {
            const validateFn = validate[board[i][j].toUpperCase()];
            if (validateFn(7 - i, j, 7 - kingRow, kingCol, [...board].reverse())) {
              return true;
            }
          }

          const validateFn = validate[board[i][j].toUpperCase()];
          if (validateFn(i, j, kingRow, kingCol, board)) {
            return true;
          }
        }
      }
    }
  }
  return false;
};