import React from "react";
import styles from "../styles/game-over-dialog.module.scss";
import { s } from "../../../../shared/socket/";
import { useDispatch } from "react-redux";
import classNames from "classnames";
import { BannerLayout } from "../../../../layouts/banner-layout";

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
    black: gameState.gameOver.black_rating,
    white: gameState.gameOver.white_rating
  };

  return (
    <BannerLayout>
      <div className={styles.gameOverDialog}>
        <h1>{endTitle}</h1>
        <p>{reasonMessage[gameOverState.reason]}</p>
        <table>
          <tr>
            <td>{gameState.black.username}</td>
            <td>
              <span
                className={classNames({
                  [styles.ratingRed]: rating.black < 0,
                  [styles.ratingGreen]: rating.black > 0
                })}
              >
                {`${rating.black > 0 ? "+" : ""}${rating.black}`}
              </span>
            </td>
          </tr>
          <tr>
            <td>{gameState.white.username}</td>
            <td>
              <span
                className={classNames({
                  [styles.ratingRed]: rating.white < 0,
                  [styles.ratingGreen]: rating.white > 0
                })}
              >
                {`${rating.white > 0 ? "+" : ""}${rating.white}`}
              </span>
            </td>
          </tr>
        </table>
        <button onClick={() => {dispatch(s.closeEndGame());}}>Got it</button>
      </div>
    </BannerLayout>
  );
}
