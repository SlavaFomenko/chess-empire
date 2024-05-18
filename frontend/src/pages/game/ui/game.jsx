import React from "react";
import styles from "../styles/game.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { ChessGame } from "../../../widgets/chess-game";

export const GamePage = () => {
  return (
    <LayoutPage>
      <div className={styles.game_page}>
        <h1>GamePage</h1>
        <ChessGame/>
      </div>
    </LayoutPage>
  );
};