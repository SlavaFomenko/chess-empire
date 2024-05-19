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
    moveAllowed: true,
    colorSelectedPiece: null,
    hasMoved: {
      whiteKing: false,
      whiteRookLeft: false,
      whiteRookRight: false,
      blackKing: false,
      blackRookLeft: false,
      blackRookRight: false
    },
    promotion: {
      isPending: false,
      position: null,
      selectedPiece: null
    }
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
      state.colorSelectedPiece = piece.toUpperCase() === piece ? "white" : "black";
      const possibleMoves = [];
      const validateFn = validate[piece.toUpperCase()];

      if (piece !== "" && (state.currentPlayer === "white" && piece.toUpperCase() === piece)) {
        for (let i = 0; i < 8; i++) {
          for (let j = 0; j < 8; j++) {
            if (validateFn(row, col, i, j, state.initialBoard) && canCaptureKing(row, col, i, j, state.initialBoard, state.currentPlayer)) {
              if (state.initialBoard[i][j] === "" ||
                (state.currentPlayer === "white" &&
                  state.initialBoard[i][j].toLowerCase() === state.initialBoard[i][j])) {
                possibleMoves.push({ row: i, col: j });
              }
            }
          }
        }

        if (piece === "K" && !state.hasMoved.whiteKing) {
          if (!state.hasMoved.whiteRookLeft && canCastle(state.initialBoard, row, col, 0, state.currentPlayer)) {
            possibleMoves.push({ row: row, col: col - 2 });
          }
          if (!state.hasMoved.whiteRookRight && canCastle(state.initialBoard, row, col, 7, state.currentPlayer)) {
            possibleMoves.push({ row: row, col: col + 2 });
          }
        }
      }

      if (piece !== "" && (state.currentPlayer === "black" && piece.toLowerCase() === piece)) {
        const reversedBoard = state.initialBoard.map(row => [...row]).reverse();
        const validateFn = validate[piece.toUpperCase()];
        for (let i = 0; i < 8; i++) {
          for (let j = 0; j < 8; j++) {
            if (validateFn(7 - row, col, 7 - i, j, reversedBoard) && canCaptureKing(7 - row, col, 7 - i, j, reversedBoard, state.currentPlayer)) {
              if (state.initialBoard[i][j] === "" ||
                (state.currentPlayer === "black" &&
                  state.initialBoard[i][j].toUpperCase() === state.initialBoard[i][j])) {
                possibleMoves.push({ row: i, col: j });
              }
            }
          }
        }

        if (piece === "k" && !state.hasMoved.blackKing) {
          if (!state.hasMoved.blackRookLeft && canCastle(state.initialBoard, row, col, 0, state.currentPlayer)) {
            possibleMoves.push({ row: row, col: col - 2 });
          }
          if (!state.hasMoved.blackRookRight && canCastle(state.initialBoard, row, col, 7, state.currentPlayer)) {
            possibleMoves.push({ row: row, col: col + 2 });
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
      let newState = JSON.parse(JSON.stringify(state));

      if (piece.toUpperCase() === "P" && (newRow === 0 || newRow === 7)) {
        newState.promotion.isPending = true;
        newState.promotion.position = action.payload
        //debugger
      }
        // console.log(newState);

      if (piece.toUpperCase() === "K" && Math.abs(newCol - col) === 2) {
        const kingRow = row;
        const kingCol = col;
        const rookCol = newCol > col ? 7 : 0;
        const newRookCol = newCol > col ? newCol - 1 : newCol + 1;

        if (canCastle(newState.initialBoard, kingRow, kingCol, rookCol, newState.currentPlayer)) {
          const newBoard = newState.initialBoard.map(row => [...row]);
          newBoard[kingRow][kingCol] = "";
          newBoard[kingRow][newCol] = piece;
          newBoard[kingRow][rookCol] = "";
          newBoard[kingRow][newRookCol] = newState.currentPlayer === "white" ? "R" : "r";

          const newGameHistory = [...newState.gameHistory, {
            fromRow: row,
            fromCol: col,
            toRow: newRow,
            toCol: newCol,
            castling: true,
            rookFromCol: rookCol,
            rookToCol: newRookCol
          }];
          const check = isCheck(newState.currentPlayer === "white" ? "black" : "white", newBoard);

          if (newState.currentPlayer === "white") {
            newState.hasMoved = {
              ...newState.hasMoved,
              whiteKing: true,
              whiteRookLeft: rookCol === 0 ? true : newState.hasMoved.whiteRookLeft,
              whiteRookRight: rookCol === 7 ? true : newState.hasMoved.whiteRookRight
            };
          } else {
            newState.hasMoved = {
              ...newState.hasMoved,
              blackKing: true,
              blackRookLeft: rookCol === 0 ? true : newState.hasMoved.blackRookLeft,
              blackRookRight: rookCol === 7 ? true : newState.hasMoved.blackRookRight
            };
          }

          newState = {
            ...newState,
            initialBoard: newBoard,
            selectedPiece: null,
            possibleMoves: [],
            currentPlayer: newState.currentPlayer === "white" ? "black" : "white",
            gameHistory: newGameHistory,
            currentStep: newState.currentStep + 1,
            check
          };
          //debugger
          return newState;
        }
      }
      if (piece && newState.currentPlayer === "white" && piece.toUpperCase() === piece) {
        if (validate[`${piece}`](row, col, newRow, newCol, newState.initialBoard)) {
          if (newState.initialBoard[newRow][newCol] === "" || newState.initialBoard[newRow][newCol].toLowerCase() === newState.initialBoard[newRow][newCol]) {
            const newBoard = newState.initialBoard.map(row => [...row]);
            newBoard[newRow][newCol] = piece;
            newBoard[row][col] = "";
            if (newState.check && !newState.possibleMoves.some(move => move.row === newRow && move.col === newCol)) {
              return newState;
            }
            const check = isCheck(newState.currentPlayer === "white" ? "black" : "white", newBoard);

            const newGameHistory = [...newState.gameHistory, { fromRow: row, fromCol: col, toRow: newRow, toCol: newCol }];

            if (piece.toUpperCase() === "K") {
              newState.hasMoved.whiteKing = true;
            } else if (piece.toUpperCase() === "R") {
              if (col === 0) newState.hasMoved.whiteRookLeft = true;
              if (col === 7) newState.hasMoved.whiteRookRight = true;
            }

            newState = {
              ...newState,
              initialBoard: newBoard,
              selectedPiece: null,
              possibleMoves: [],
              currentPlayer: newState.currentPlayer === "white" ? "black" : "white",
              gameHistory: newGameHistory,
              currentStep: newState.currentStep + 1,
              check
            };
            //debugger
            return newState
          }
        }
      } else if (piece && newState.currentPlayer === "black" && piece.toLowerCase() === piece) {
        const reversedBoard = newState.initialBoard.map(row => [...row]).reverse();
        if (validate[`${piece.toUpperCase()}`](7 - row, col, 7 - newRow, newCol, reversedBoard)) {
          if (newState.initialBoard[newRow][newCol] === "" || newState.initialBoard[newRow][newCol].toUpperCase() === newState.initialBoard[newRow][newCol]) {
            const newBoard = newState.initialBoard.map(row => [...row]);
            newBoard[newRow][newCol] = piece;
            newBoard[row][col] = "";
            if (newState.check && !newState.possibleMoves.some(move => move.row === newRow && move.col === newCol)) {
              return newState;
            }
            const check = isCheck(newState.currentPlayer === "white" ? "black" : "white", newBoard);

            const newGameHistory = [...newState.gameHistory, { fromRow: row, fromCol: col, toRow: newRow, toCol: newCol }];

            if (piece.toLowerCase() === "k") {
              newState.hasMoved.blackKing = true;
            } else if (piece.toLowerCase() === "r") {
              if (col === 0) newState.hasMoved.blackRookLeft = true;
              if (col === 7) newState.hasMoved.blackRookRight = true;
            }

            newState =  {
              ...newState,
              initialBoard: newBoard,
              selectedPiece: null,
              possibleMoves: [],
              currentPlayer: newState.currentPlayer === "white" ? "black" : "white",
              gameHistory: newGameHistory,
              currentStep: newState.currentStep + 1,
              check
            };

            return newState
          }
        }
      }
      //debugger
      return newState;
    },
    selectPromotionPiece: (state, action) => {
      const  selectedPiece  = action.payload;
      const { newRow, newCol } = state.promotion.position;
      state.promotion.selectedPiece = selectedPiece;
      state.promotion.isPending = false;
      state.initialBoard[newRow][newCol] = state.currentPlayer === 'white'? selectedPiece.toLowerCase() : selectedPiece ;
      state.gameHistory[state.gameHistory.length - 1].newPiece = selectedPiece;
    },

    applyTurn: (state) => {

      if (state.gameHistory.length === state.currentStep) {
        return state;
      }

      const { fromRow, fromCol, toRow, toCol, castling, rookFromCol, rookToCol,newPiece } = state.gameHistory[state.currentStep];

      if(newPiece){
        state.initialBoard[toRow][toCol] = "";
        state.initialBoard[fromRow][fromCol] = newPiece;
      }

      if (castling) {
        const newBoard = state.initialBoard.map(row => [...row]);
        const piece = newBoard[fromRow][fromCol];
        newBoard[fromRow][fromCol] = "";
        newBoard[toRow][toCol] = piece;
        newBoard[fromRow][rookFromCol] = "";
        newBoard[fromRow][rookToCol] = state.currentPlayer === "white" ? "R" : "r";
        state.initialBoard = newBoard;
      } else {
        const piece = state.initialBoard[fromRow][fromCol];
        state.initialBoard[toRow][toCol] = piece;
        state.initialBoard[fromRow][fromCol] = "";
      }

      if (state.currentPlayer === "white") {
        if (state.initialBoard[toRow][toCol] === "K") {
          state.hasMoved.whiteKing = true;
        }
        if (state.initialBoard[toRow][toCol] === "R") {
          if (fromCol === 0) state.hasMoved.whiteRookLeft = true;
          if (fromCol === 7) state.hasMoved.whiteRookRight = true;
        }
      } else {
        if (state.initialBoard[toRow][toCol] === "k") {
          state.hasMoved.blackKing = true;
        }
        if (state.initialBoard[toRow][toCol] === "r") {
          if (fromCol === 0) state.hasMoved.blackRookLeft = true;
          if (fromCol === 7) state.hasMoved.blackRookRight = true;
        }
      }

      state.currentPlayer = state.currentPlayer === "white" ? "black" : "white";
      state.currentStep++;
      state.moveAllowed = state.gameHistory.length === state.currentStep;
    },
    undoTurn: (state) => {
      if (state.currentStep === 0) return state;

      state.currentStep -= 1;
      const initialBoard = [
        ["r", "n", "b", "q", "k", "b", "n", "r"],
        ["p", "p", "p", "p", "p", "p", "p", "p"],
        ["", "", "", "", "", "", "", ""],
        ["", "", "", "", "", "", "", ""],
        ["", "", "", "", "", "", "", ""],
        ["", "", "", "", "", "", "", ""],
        ["P", "P", "P", "P", "P", "P", "P", "P"],
        ["R", "N", "B", "Q", "K", "B", "N", "R"]
      ];

      state.initialBoard = initialBoard;
      state.hasMoved = {
        whiteKing: false,
        whiteRookLeft: false,
        whiteRookRight: false,
        blackKing: false,
        blackRookLeft: false,
        blackRookRight: false
      };

      for (let i = 0; i < state.currentStep; i++) {
        const move = state.gameHistory[i];

        if (move.castling) {
          const kingRow = move.fromRow;
          const kingCol = move.fromCol;
          const newKingCol = move.toCol;
          const rookCol = move.rookFromCol;
          const newRookCol = move.rookToCol;
          const kingPiece = state.initialBoard[kingRow][kingCol];
          const rookPiece = state.initialBoard[kingRow][rookCol];

          state.initialBoard[kingRow][kingCol] = "";
          state.initialBoard[kingRow][newKingCol] = kingPiece;
          state.initialBoard[kingRow][rookCol] = "";
          state.initialBoard[kingRow][newRookCol] = rookPiece;

          if (kingPiece === "K") {
            state.hasMoved.whiteKing = true;
            if (rookCol === 0) state.hasMoved.whiteRookLeft = true;
            if (rookCol === 7) state.hasMoved.whiteRookRight = true;
          } else {
            state.hasMoved.blackKing = true;
            if (rookCol === 0) state.hasMoved.blackRookLeft = true;
            if (rookCol === 7) state.hasMoved.blackRookRight = true;
          }
        } else {
          const piece = state.initialBoard[move.fromRow][move.fromCol];
          state.initialBoard[move.toRow][move.toCol] = piece;
          state.initialBoard[move.fromRow][move.fromCol] = "";

          if (move.newPiece) {
            state.initialBoard[move.toRow][move.toCol] = move.newPiece.toUpperCase();
          }

          if (piece.toUpperCase() === "K") {
            if (piece === "K") {
              state.hasMoved.whiteKing = true;
            } else {
              state.hasMoved.blackKing = true;
            }
          } else if (piece.toUpperCase() === "R") {
            if (piece === "R") {
              if (move.fromCol === 0) state.hasMoved.whiteRookLeft = true;
              if (move.fromCol === 7) state.hasMoved.whiteRookRight = true;
            } else {
              if (move.fromCol === 0) state.hasMoved.blackRookLeft = true;
              if (move.fromCol === 7) state.hasMoved.blackRookRight = true;
            }
          }
        }
      }

      state.currentPlayer = state.currentPlayer === "white" ? "black" : "white";
      state.selectedPiece = null;
      state.possibleMoves = [];
      state.moveAllowed = false;

      return state;
    },


  }

});

const canCastle = (board, kingRow, kingCol, rookCol, player) => {
  if (isCheck(player, board)) return false;

  const direction = rookCol > kingCol ? 1 : -1;
  for (let col = kingCol + direction; col !== rookCol; col += direction) {
    if (board[kingRow][col] !== "") return false;
    const newBoard = board.map(row => [...row]);
    newBoard[kingRow][col] = player === "white" ? "K" : "k";
    newBoard[kingRow][kingCol] = "";
    if (isCheck(player, newBoard)) {
      return false;
    }
  }
  return true;
};

function canCaptureKing (row, col, newRow, newCol, board, currentPlayer) {
  const piece = board[row][col];
  const newBoard = board.map(row => [...row]);

  if (newBoard[newRow][newCol] === "" ||
    (currentPlayer === "white" &&
      newBoard[newRow][newCol] === newBoard[newRow][newCol].toLowerCase()) ||
    (currentPlayer === "black" &&
      newBoard[newRow][newCol] === newBoard[newRow][newCol].toUpperCase())
  ) {
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
        if (i < 0 || i >= board.length || j < 0 || j >= board[0].length) return false;
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
        if (i < 0 || i >= 8 || j < 0 || j >= 8 || board[i][j] !== "") return false;
        i += directionX;
        j += directionY;
      }
      return true;
    } else if (newRow === row) {
      const direction = newCol > col ? 1 : -1;
      for (let i = col + direction; i !== newCol; i += direction) {
        if (i < 0 || i >= 8 || board[row][i] !== "") return false;
      }
      return true;
    } else if (newCol === col) {
      const direction = newRow > row ? 1 : -1;
      for (let i = row + direction; i !== newRow; i += direction) {
        if (i < 0 || i >= 8 || board[i][col] !== "") return false;
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
          if (validateFn(7 - i, j, 7 - kingRow, kingCol, reversedBoard)) {
            return true;
          }
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
