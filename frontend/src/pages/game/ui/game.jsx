import React from "react";
import styles from "../styles/game.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { ChessGame } from "../../../widgets/chess-game";
import { GameOverDialog } from "../../../entities/game";
import { useSelector } from "react-redux";
import { SearchGame } from "../../../features/search-game";

export const GamePage = () => {
  const gameState = useSelector(state => state.game);
  const socketState = useSelector(store => store.socket);

  return (
    <LayoutPage>
      {["default", "searchingGame"].includes(socketState.state) && <SearchGame /> }
      {socketState.state === "inGame" &&
        <div className={styles.game_page}>
          <ChessGame />
          {gameState.gameOver.winner && <GameOverDialog gameState={gameState} />}
        </div>
      }
    </LayoutPage>
  );
};