import React from "react";
import styles from "../styles/games-list.module.scss";
import { GameCard } from "../../game-card/ui/game-card";

export function GamesList ({ games }) {
  return (
    <div className={styles.container}>
      {games && games.map(game=><GameCard gameData={game}/>)}
    </div>
  );
}
