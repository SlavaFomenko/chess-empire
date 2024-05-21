import React from "react";
import styles from "../styles/game-card.module.scss";
import { useSelector } from "react-redux";
import classNames from "classnames";

export function GameCard ({ gameData }) {
  const user = useSelector(state => state.user.user);

  // {
  //   "id": 10,
  //   "time": 300,
  //   "rated": false,
  //   "winner": "b",
  //   "b_Rating": 100,
  //   "w_Rating": 100,
  //   "b_Id": 1,
  //   "w_Id": 2,
  //   "history": "f7f6 e2e3 g7g5 d1h5",
  //   "playedDate": "1716249770",
  //   "b_username": "SmthFromSpace",
  //   "w_username": "F0menko"
  // },

  const formatDate = (date) => {
    const pad = (number) => number.toString().padStart(2, "0");

    const day = pad(date.getDate());
    const month = pad(date.getMonth() + 1);
    const year = date.getFullYear();
    const hours = pad(date.getHours());
    const minutes = pad(date.getMinutes());

    return `${day}.${month}.${year} ${hours}:${minutes}`;
  };

  const color = user?.id === gameData.w_id ? "w" : user?.id === gameData.b_id ? "b" : "-";
  const result = gameData.winner === "t" ? "Tie" : color === gameData.winner ? "Won" : "Lost";
  const formattedDate = formatDate(new Date(gameData.playedDate * 1000));

  return (
    <div className={styles.card}>
      <table>
        <tr className={styles.players}>
          <td colSpan="3">
            <span className={styles.username}>{gameData.w_username} ({gameData.w_rating})</span>
            {" vs "}
            <span className={styles.username}>{gameData.b_username} ({gameData.b_rating})</span>
          </td>
        </tr>
        <tr>
          <td>
            <p className={styles.stats}>
              {gameData.time / 60}m, {gameData.history.split(" ").length} turns
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
