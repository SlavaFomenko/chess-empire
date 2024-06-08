import { createSlice } from "@reduxjs/toolkit";
import { applyTurns, getPossibleMoves, pieceColor, turnToCords, validateMove } from "../lib";
import { DEFAULT_BOARD, DEFAULT_HAS_MOVED } from "../config/config";

const initialState = {
  id: null,
  inProgress: false,
  board: DEFAULT_BOARD,
  selectedPiece: null,
  currentColor: "white",
  myColor: "white",
  availableMoves: [],
  gameHistory: [],
  currentStep: 0,
  hasMadeTurn: false,
  hasMoved: DEFAULT_HAS_MOVED,
  promotion: {
    isPending: false,
    position: null,
    newPiece: null
  },
  gameOver: {
    winner: null,
    reason: null,
    white_rating: null,
    black_rating: null,
    white_rating_change: null,
    black_rating_change: null,
  },
  black: null,
  white: null
};

export const gameSlice = createSlice({
  name: "game",
  initialState: { ...initialState },
  reducers: {
    selectPiece: (state, action) => {
      if (action.payload === null || state.currentStep < state.gameHistory.length || state.myColor !== state.currentColor)
      {
        state.selectedPiece = null;
        state.availableMoves = [];
        return;
      }

      const { row, col } = action.payload;
      if (pieceColor(state.board[row][col]) !== state.myColor) {
        state.selectedPiece = null;
        state.availableMoves = [];
        return;
      }

      state.selectedPiece = { row, col };

      const availableMoves = getPossibleMoves(state.board, { row, col });
      state.availableMoves = availableMoves.filter(to => validateMove(state.board, { row, col }, to, state.hasMoved));
    },
    movePiece: (state, action) => {
      if (state.currentStep < state.gameHistory.length || state.myColor !== state.currentColor)
      {
        state.selectedPiece = null;
        state.availableMoves = [];
        return;
      }

      const from = state.selectedPiece;
      const to = action.payload;

      if(validateMove(state.board, from, to, state.hasMoved)){
        let turn = {
          from: from,
          to: to,
          rookCol: null,
          newPiece: null
        };
        const piece = state.board[from.row][from.col];

        if("kK".includes(piece) && Math.abs(from.col - to.col) === 2){
          turn.rookCol = from.col > to.col ? 0 : 7;
        }

        const apply = applyTurns([turn], state.board, state.currentColor, state.hasMoved);

        const enemyRow = state.currentColor === "white" ? 0 : 7;
        if("pP".includes(piece) && to.row === enemyRow){
          return {
            ...state,
            board: apply.board,
            gameHistory: [...state.gameHistory, turn],
            hasMoved: apply.hasMoved,
            promotion: {
              isPending: true,
              position: to,
              newPiece: null
            }
          }
        }

        return {
          ...state,
          board: apply.board,
          selectedPiece: null,
          currentColor: state.currentColor === "white" ? "black" : "white",
          availableMoves: [],
          gameHistory: [...state.gameHistory, turn],
          currentStep: state.currentStep + 1,
          hasMadeTurn: true,
          hasMoved: apply.hasMoved,
        }
      }
    },
    selectPromotionPiece: (state, action) => {
      const selectedPiece = action.payload;
      const { row, col } = state.promotion.position;
      state.board[row][col] = state.currentColor === "white" ? selectedPiece.toLowerCase() : selectedPiece;
      state.selectedPiece = null;
      state.currentColor = state.currentColor === "white" ? "black" : "white";
      state.availableMoves = [];
      state.gameHistory[state.gameHistory.length - 1].newPiece = state.currentColor === "white" ? selectedPiece.toLowerCase() : selectedPiece;
      state.hasMadeTurn = true;
      state.promotion.selectedPiece = selectedPiece;
      state.promotion.isPending = false;
    },
    goToStep: (state, action) => {
      const newStep = action.payload.step;

      if (state.gameHistory.length <= 0 || newStep > state.gameHistory.length || newStep < 0) {
        return { ...state };
      }

      const historyToApply = state.gameHistory.slice(0, Math.max(newStep, 0));

      const { board, hasMoved } = applyTurns(historyToApply);

      return {
        ...state,
        board: board,
        hasMoved: hasMoved,
        selectedPiece: null,
        currentStep: newStep,
        availableMoves: [],
      };
    },
    updateState: (state, action) => {
      const { id, turn, history, black, white, myColor } = action.payload;
      const newHistory = history === "" ? [] : history.split(" ").map(turn => turnToCords(turn));
      const { board, hasMoved } = applyTurns([...newHistory]);
      return {
        ...state,
        id: id,
        currentColor: turn,
        myColor: myColor ?? state.myColor,
        gameHistory: newHistory,
        currentStep: newHistory.length,
        black: black,
        white: white,
        board: board,
        hasMoved: hasMoved,
        hasMadeTurn: false
      };
    },
    updateTimers: (state, action) => {
      const { black, white } = action.payload;
      state.black.time = black ?? state.black.time;
      state.white.time = white ?? state.white.time;
    },
    gameOver: (state, action) => {
      return {
        ...state,
        inProgress: false,
        gameOver: {
          winner: action.payload.winner,
          reason: action.payload.reason,
          white_rating: action.payload.white_rating,
          black_rating: action.payload.black_rating,
          white_rating_change: action.payload.white_rating_change,
          black_rating_change: action.payload.black_rating_change,
        }
      };
    },
    reset: () => {
      return { ...initialState };
    }
  }
});