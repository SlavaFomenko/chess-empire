import React from "react";
import styles from "../styles/game.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { ChessGame } from "../../../widgets/chess-game";
import { GameOverDialog } from "../../../entities/game";
import { useSelector } from "react-redux";

export const GamePage = () => {
  const gameState = useSelector(state=>state.game)
  return (
    <LayoutPage>
      <div className={styles.game_page}>
        <ChessGame/>
        {gameState.gameOver.winner && <GameOverDialog gameState={gameState}/>}
      </div>
    </LayoutPage>
  );
};