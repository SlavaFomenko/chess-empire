import React from "react";
import styles from "../styles/chess-players-bar.module.scss";
import defaultProfilePic from "../../../shared/images/icons/defaultProfilePic.png";
import { HOST_URL } from "../../../shared/config";

export function ChessPlayersBar ({players: {white, black}}) {
  return (
    <div className={styles.playersBar}>
      {!white ? <span className={styles.playerNameSpan}>Pending...</span> : <>
        <img src={white.profilePic === "" ? defaultProfilePic : `${HOST_URL}/${white.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic" />
        <span className={styles.playerNameSpan}>{white?.username}</span>
      </>}
      <h1>vs</h1>
      {!black ? <span className={styles.playerNameSpan}>Pending...</span> : <>
        <span className={styles.playerNameSpan}>{black?.username}</span>
        <img src={black.profilePic === "" ? defaultProfilePic : `${HOST_URL}/${black.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic" />
      </>}
    </div>
  );
}
