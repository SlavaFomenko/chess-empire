import React from "react";
import styles from "../styles/game.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { ChessGame } from "../../../widgets/chess-game";
import { GameOverDialog } from "../../../entities/game";
import { useSelector } from "react-redux";
import { SearchGame } from "../../../features/search-game";

export const GamePage = () => {
  const userState = useSelector(state => state.user);
  const gameState = useSelector(state => state.game);
  const socketState = useSelector(store => store.socket);

  return (
    <LayoutPage>
      {!userState.user?.token &&
        <div className={styles.gameSearchPage}>
          <h1>Please, sign in/up for playing</h1>
        </div>
      }
      {["default", "searchingGame"].includes(socketState.state) &&
        <div className={styles.gameSearchPage}>
          <h1>Play with stranger</h1>
          <SearchGame />
        </div>
      }
      {socketState.state === "inGame" &&
        <div className={styles.gamePage}>
          <ChessGame />
          {gameState.gameOver.winner && <GameOverDialog gameState={gameState} />}
        </div>
      }
    </LayoutPage>
  );
};