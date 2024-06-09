import React from "react";
import styles from "../styles/game-invite-dialog.module.scss";
import { s } from "../../../../shared/socket";
import { hideNotification } from "../../../../shared/notification";
import { useDispatch } from "react-redux";

export function GameInviteDialog ({ data, onAccept, onReject}) {
  const dispatch = useDispatch();

  const initiator = data.white.id === null ? data.black : data.white;

  return (
    <div className={styles.gameInvite}>
      {initiator.username} invites you to {initiator.time/60}m game
      <div>
        <button
          onClick={() => {
            onAccept();
            dispatch(hideNotification());
          }
          }
        >Accept
        </button>
        <button
          onClick={() => {
            onReject();
            dispatch(hideNotification());
          }
          }
        >Reject
        </button>
      </div>
    </div>
  );
}
