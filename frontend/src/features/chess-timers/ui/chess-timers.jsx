import React from "react";
import styles from "../styles/chess-timers.module.scss";

export function ChessTimer ({player}) {
  const formatTime = (time) => {
    const minutes = Math.floor(time / 60);
    const seconds = time % 60;
    return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
  };
  return (
    <div>
      <p className={`${styles.timer} ${player?.time <= 10 ? styles.timerRed : ""}`}> {player && formatTime(player.time)}</p>
    </div>
  );
}
