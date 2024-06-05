import React, { useEffect } from "react";
import styles from "../styles/chess-game.module.scss";
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch, useSelector } from "react-redux";
import { hideNotification, showNotification } from "../../../shared/notification";
import { s } from "../../../shared/socket";
import { goToStep, movePiece, selectPiece } from "../../../shared/game";
import { pieceColor } from "../../../shared/game/lib";
import { ChessHistory } from "../../../features/chess-history";
import { ChessPlayerBar } from "../../../features/chess-players-bar";

export function ChessGame () {
  const dispatch = useDispatch();

  const gameState = useSelector(state => state.game);
  const { selectedPiece, currentColor, board, hasMadeTurn, gameHistory } = gameState;
  const { currentStep, black, white, myColor } = gameState;

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
      <div className={styles.game}>
        <ChessPlayerBar player={myColor !== 'white' ?white:black} timer={true}/>
        <ChessBoard gameState={gameState} event={handleSquareClick} />
        <ChessPlayerBar player={myColor === 'white' ? white:black} timer={true}/>
      </div>
      <div className={styles.horizontal}>
        <div className={styles.rightPanel}>
          <div>
            <button onClick={submitResign}>Resign</button>
          </div>
          <ChessHistory gameHistory={gameHistory} step={currentStep} setStep={(step) => dispatch((goToStep(step)))} />
        </div>
      </div>
    </div>
  );
}
