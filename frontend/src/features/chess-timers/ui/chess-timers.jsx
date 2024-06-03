import React from "react";
import styles from "../styles/chess-timers.module.scss";

export function ChessTimers ({players: {black, white}}) {
  const formatTime = (time) => {
    const minutes = Math.floor(time / 60);
    const seconds = time % 60;
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
  };

  return (
    <div>
      <p className={`${styles.timer} ${black?.time <= 10 ? styles.timerRed : ""}`}>Black: {black && formatTime(black.time)}</p>
      <p className={`${styles.timer} ${white?.time <= 10 ? styles.timerRed : ""}`}>White: {white && formatTime(white.time)}</p>
    </div>
  );
}
