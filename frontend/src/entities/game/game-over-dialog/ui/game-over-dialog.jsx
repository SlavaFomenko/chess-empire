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

  const ratingChange = {
    black: gameState.gameOver.black_rating_change,
    white: gameState.gameOver.white_rating_change
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
              {`${rating.black} `}
              <span
                className={classNames({
                  [styles.ratingRed]: ratingChange.black < 0,
                  [styles.ratingGreen]: ratingChange.black > 0
                })}
              >
                {`(${ratingChange.black > 0 ? "+" : ""}${ratingChange.black})`}
              </span>
            </td>
          </tr>
          <tr>
            <td>{gameState.white.username}</td>
            <td>
              {`${rating.white} `}
              <span
                className={classNames({
                  [styles.ratingRed]: ratingChange.white < 0,
                  [styles.ratingGreen]: ratingChange.white > 0
                })}
              >
                {`(${ratingChange.white > 0 ? "+" : ""}${ratingChange.white})`}
              </span>
            </td>
          </tr>
        </table>
        <button onClick={() => {dispatch(s.closeEndGame());}}>Got it</button>
      </div>
    </BannerLayout>
  );
}
