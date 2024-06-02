import React, { useEffect } from "react";
import styles from "../styles/chess-game.module.scss";
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch, useSelector } from "react-redux";
import { hideNotification, showNotification } from "../../../shared/notification";
import { s } from "../../../shared/socket";
import { goToStep, movePiece, selectPiece } from "../../../shared/game";
import { pieceColor } from "../../../shared/game/lib";

export function ChessGame () {
  const dispatch = useDispatch();

  const gameState = useSelector(state => state.game);
  const { selectedPiece, currentColor, board, hasMadeTurn, gameHistory } = gameState;
  const { currentStep, black, white } = gameState;

  const formatTime = (time) => {
    const minutes = Math.floor(time / 60);
    const seconds = time % 60;
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
  };

  const submitResign = () => {
    dispatch(showNotification(
      <div className={styles.resignSubmit}>
        Are you sure you want to resign?
        <div>
          <button
            onClick={() => {
              dispatch(s.resign());
              dispatch(hideNotification());
            }
            }
          >Yes
          </button>
          <button
            onClick={() => {
              dispatch(hideNotification());
            }
            }
          >No
          </button>
        </div>
      </div>
    ));
  };


  const handleSquareClick = (cords) => {
    if (selectedPiece && selectedPiece.row === cords.row && selectedPiece.col === cords.col) {
      return dispatch(selectPiece(null));
    }

    const color = pieceColor(board[cords.row][cords.col]);
    if (selectedPiece === null || currentColor === color) {
      return dispatch(selectPiece(cords));
    }

    dispatch(movePiece(cords));
  };

  useEffect(() => {
    if (hasMadeTurn && gameHistory.length > 0) {
      dispatch(s.turn(gameHistory[gameHistory.length - 1]));
    }
  }, [hasMadeTurn]);

  return (
    <div className={styles.wrapper}>
      <h1>
        <span className={styles.playerNameSpan}>{white?.username}</span>
        {" vs "}
        <span className={styles.playerNameSpan}>{black?.username}</span>
      </h1>
      <div className={styles.horizontal}>
        <ChessBoard gameState={gameState} event={handleSquareClick}/>
        <div className={styles.rightPanel}>
          <div>
            <p className={`${styles.timer} ${black?.time <= 10 ? styles.timerRed : ""}`}>Black: {black && formatTime(black.time)}</p>
            <p className={`${styles.timer} ${white?.time <= 10 ? styles.timerRed : ""}`}>White: {white && formatTime(white.time)}</p>
          </div>
          <div className={styles.buttons}>
            <button onClick={() => dispatch((goToStep(currentStep - 1)))}>Undo</button>
            <button onClick={() => dispatch((goToStep(currentStep + 1)))}>Redo</button>
          </div>
          <div>
            <button onClick={submitResign}>Resign</button>
          </div>
        </div>
      </div>
    </div>
  );
}
