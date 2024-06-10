import React from "react";
import styles from "../styles/chess-players-bar.module.scss";
import defaultProfilePic from "../../../shared/images/icons/defaultProfilePic.png";
import { HOST_URL } from "../../../shared/config";
import { ChessTimer } from "../../chess-timers";
import { useNavigate } from "react-router-dom";

export function ChessPlayerBar ({ player, timer=false  }) {
  const navigate = useNavigate();

  return (
    <div className={styles.playersBar}>
      {!player ?
        <span className={styles.playerNameSpan}>Pending...</span> :
        <>
          <div className={styles.profile} onClick={()=>{navigate(`/user/${player.id}`)}}>
            <img src={player.profilePic === "" ? defaultProfilePic : `https://${HOST_URL}/${player.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic" />
            <span className={styles.playerNameSpan}>{player?.username}</span>
          </div>
          {timer && <ChessTimer player={player} />}
        </>}
    </div>
  );
}
