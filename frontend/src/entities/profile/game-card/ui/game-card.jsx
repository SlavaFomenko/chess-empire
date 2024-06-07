import React from "react";
import styles from "../styles/game-card.module.scss";
import { useSelector } from "react-redux";
import classNames from "classnames";
import { useLocation, useNavigate } from "react-router-dom";

export function GameCard ({ gameData }) {
  const user = useSelector(state => state.user.user);
  const navigate = useNavigate();
  const { pathname } = useLocation();

  const isAdminPanel = pathname.includes("admin");

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

  const rating = {
    white: gameData.white_rating,
    white_change: gameData.white_rating_change,
    white_class: gameData.white_rating_change > 0 ? styles.resultGreen : styles.resultRed,
    black: gameData.black_rating,
    black_change: gameData.black_rating_change,
    black_class: gameData.black_rating_change > 0 ? styles.resultGreen : styles.resultRed
  }

  return (
    <div className={styles.card} onClick={() => {navigate(`/game-review/${gameData.id}`);}}>
      <table>
        <tbody>
        <tr className={styles.players}>
          <td colSpan="3">
              <span className={styles.username}>
                {gameData.white_username} ({rating.white}{rating.white_change !== 0 && <span className={rating.white_class}>{` ${rating.white_change > 0 ? "+" : ""}${rating.white_change}`}</span>})
              </span>
            {" vs "}
            <span className={styles.username}>
                {gameData.black_username} ({rating.black}{rating.black_change !== 0 && <span className={rating.black_class}>{` ${rating.black_change > 0 ? "+" : ""}${rating.black_change}`}</span>})
              </span>
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
          {!isAdminPanel &&
            <td
              className={classNames({
                [styles.result]: color !== "-",
                [styles.resultRed]: color !== gameData.winner && gameData.winner !== "t",
                [styles.resultGreen]: color === gameData.winner && gameData.winner !== "t"
              })}
            >{color !== "-" && result}</td>
          }
        </tr>
        </tbody>
      </table>
    </div>
  );
}
