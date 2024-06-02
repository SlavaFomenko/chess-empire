import React from "react";
import styles from "../styles/game-over-dialog.module.scss";
import { s } from "../../../../shared/socket/";
import { useDispatch } from "react-redux";
import classNames from "classnames";

export function GameOverDialog ({ gameState }) {
  const gameOverState = gameState.gameOver;
  const dispatch = useDispatch();

  const endTitle = gameOverState.winner === "tie" ? "TIE" : `${gameOverState.winner} won`.toUpperCase();

  const reasonMessage = {
    disconnect: "The opponent left the game",
    timeout: "The opponent ran out of time",
    resign: "The opponent resigned",
    mate: "Mate!",
    tie: "There's nothing we can do"
  };

  const rating = {
    b: gameState.gameOver.black_rating,
    w: gameState.gameOver.white_rating
  };

  return (
    <div className={styles.container}>
      <div className={styles.gameOverDialog}>
        <h1>{endTitle}</h1>
        <p>{reasonMessage[gameOverState.reason]}</p>
        <table>
          <tr>
            <td>{gameState.black.username}</td>
            <td>
              <span
                className={classNames({
                  [styles.ratingRed]: rating.b < 0,
                  [styles.ratingGreen]: rating.b > 0
                })}
              >
                {`${rating.b > 0 ? "+" : ""}${rating.b}`}
              </span>
            </td>
          </tr>
          <tr>
            <td>{gameState.white.username}</td>
            <td>
              <span
                className={classNames({
                  [styles.ratingRed]: rating.w < 0,
                  [styles.ratingGreen]: rating.w > 0
                })}
              >
                {`${rating.w > 0 ? "+" : ""}${rating.w}`}
              </span>
            </td>
          </tr>
        </table>
        <button onClick={() => {dispatch(s.closeEndGame());}}>Got it</button>
      </div>
    </div>
  );
}
