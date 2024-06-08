import React, { useEffect, useState } from "react";
import styles from "../styles/game-review.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import { GET_GAME_BY_ID } from "../../../shared/config";
import { useDispatch, useSelector } from "react-redux";
import { ChessPlayerBar } from "../../../features/chess-players-bar";
import { ChessBoard } from "../../../features/chess-board";
import { ChessHistory } from "../../../features/chess-history";
import { applyTurns, enemyColor, turnToCords } from "../../../shared/game/lib";

export const GameReviewPage = () => {
  const userStore = useSelector(state => state.user);
  const [error, setError] = useState(null);
  const [gameState, setGameState] = useState(null);

  useEffect(() => {
    setError(null);
    if (!userStore.user?.token) {
      setError("You are not authorized");
      return;
    }

    let gameId = window.location.pathname.replace("/game-review/", "");
    if (`${+gameId}` !== `${gameId}`) {
      setError("Invalid URL");
      return;
    }
    gameId = +gameId;

    axios.get(GET_GAME_BY_ID(gameId), {
      headers: {
        Authorization: `Bearer ${userStore.user.token}`
      }
    }).then(response => {
      const state = response.data;

      state.history = state.history !== "" ? state.history.split(" ").map(turn => turnToCords(turn)) : [];

      const { board } = applyTurns(state.history.slice(0, 1));

      setGameState({
        black: {
          id: state.black_id,
          profilePic: state.black_profilePic,
          username: state.black_username
        },
        white: {
          id: state.white_id,
          profilePic: state.white_profilePic,
          username: state.white_username
        },
        history: state.history,
        board: board,
        myColor: userStore.user.id === state.black_id ? "black" : "white",
        currentStep: 1
      });
    }).catch(error => setError(error.response.data.message));
  }, [userStore.user]);

  const goToStep = (step) => {
    if (gameState?.history.length <= 0 || step > gameState?.history.length || step <= 0) {
      return;
    }

    const historyToApply = gameState?.history.slice(0, Math.max(step, 0));

    const { board, hasMoved } = applyTurns(historyToApply);

    setGameState({
      ...gameState,
      board: board,
      hasMoved: hasMoved,
      currentStep: step
    });
  };

  return (
    <LayoutPage>
      <div className={styles.gameReviewPage}>
        {error && <h1 className={styles.errorMessage}>{error}</h1>}
        {gameState && <>
          <div className={styles.wrapper}>
            <div className={styles.game}>
              <ChessPlayerBar player={gameState.myColor !== 'white' ? gameState.white: gameState.black} />
              <ChessBoard gameState={gameState} event={() => {}} />
              <ChessPlayerBar player={gameState.myColor === 'white' ? gameState.white: gameState.black} />
            </div>
            <div className={styles.horizontal}>
              <div className={styles.rightPanel}>
                <div>
                  <button onClick={() => {setGameState({ ...gameState, myColor: enemyColor(gameState.myColor) });}}>Flip
                                                                                                                    Board
                  </button>
                </div>
                <div className={styles.historyButtons}>
                  <button disabled={gameState.currentStep <= 1} onClick={() => {goToStep(gameState.currentStep - 1);}}>Back</button>
                  <button disabled={gameState.currentStep >= gameState.history.length} onClick={() => {goToStep(gameState.currentStep + 1);}}>Next</button>
                </div>
                <ChessHistory gameHistory={gameState.history} step={gameState.currentStep} setStep={step => goToStep(step)} />
              </div>
            </div>
          </div>
        </>}
      </div>
    </LayoutPage>
  );
};