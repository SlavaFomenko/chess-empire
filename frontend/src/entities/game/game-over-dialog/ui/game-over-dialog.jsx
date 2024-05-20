import React, { useEffect, useState } from "react";
import styles from "../styles/game-over-dialog.module.scss";

export function GameOverDialog ({gameState}) {
  const gameOverState = gameState.gameOver;

  const endTitle = gameOverState.winner === "tie" ? "TIE" : `${gameOverState.winner} won`.toUpperCase();

  const reasonMessage = {
    disconnect: "The opponent left the game",
    timeout: "The opponent ran out of time",
    resign: "The opponent resigned",
    mate: "Mate!",
    tie: "There's nothing we can do"
  }

  const rating = {
    b: gameState.gameOver.b_rating,
    w: gameState.gameOver.w_rating
  }

  return (
    <div className={styles.container}>
      <div className={styles.gameOverDialog}>
        <h1>{endTitle}</h1>
        <p>{reasonMessage[gameOverState.reason]}</p>
        <table>
          <tr>
            <td>{gameState.black.username}</td>
            <td>
              <span>{`${rating.b > 0 ? "+" : ""}${rating.b}`}</span>
            </td>
          </tr>
          <tr>
            <td>{gameState.white.username}</td>
            <td>
              <span>{`${rating.w > 0 ? "+" : ""}${rating.w}`}</span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  );
}
