import React from "react";
import styles from "../styles/game-card.module.scss";
import { useSelector } from "react-redux";
import classNames from "classnames";
import { useNavigate } from "react-router-dom";

export function GameCard ({ gameData }) {
  const user = useSelector(state => state.user.user);
  const navigate = useNavigate();

  const formatDate = (date) => {
    const pad = (number) => number.toString().padStart(2, "0");

    const day = pad(date.getDate());
    const month = pad(date.getMonth() + 1);
    const year = date.getFullYear();
    const hours = pad(date.getHours());
    const minutes = pad(date.getMinutes());

    return `${day}.${month}.${year} ${hours}:${minutes}`;
  };

  const color = user?.id === gameData.white_id ? "w" : user?.id === gameData.black_id ? "b" : "-";
  const result = gameData.winner === "t" ? "Tie" : color === gameData.winner ? "Won" : "Lost";
  const formattedDate = formatDate(new Date(gameData.playedDate * 1000));

  return (
    <div className={styles.card} onClick={()=>{navigate(`/game-review/${gameData.id}`)}}>
      <table>
        <tr className={styles.players}>
          <td colSpan="3">
            <span className={styles.username}>{gameData.white_username} ({gameData.white_rating})</span>
            {" vs "}
            <span className={styles.username}>{gameData.black_username} ({gameData.black_rating})</span>
          </td>
        </tr>
        <tr>
          <td>
            <p className={styles.stats}>
              {gameData.time / 60}m, {gameData.history === "" ? 0 : gameData.history.split(" ").length} turns
            </p>
            <p className={styles.playedDate}>
              {formattedDate}
            </p>
          </td>
          <td className={classNames({
            [styles.result]: color !== "-",
            [styles.resultRed]: color !== gameData.winner && gameData.winner !== "t",
            [styles.resultGreen]: color === gameData.winner && gameData.winner !== "t",
          })}>{color !== "-" && result}</td>
        </tr>
      </table>
    </div>
  );
}
