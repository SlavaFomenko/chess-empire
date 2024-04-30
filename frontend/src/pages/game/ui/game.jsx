import React from "react";
import styles from "../styles/game.module.scss";
import { LayoutPage } from "../../../layouts/layout-page";

export const GamePage = () => {
  return (
    <LayoutPage>
      <div className={styles.game_page}>
        <h1>GamePage</h1>
      </div>
    </LayoutPage>
  );
};