import React from "react";
import styles from "../styles/game.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { ChessGame } from "../../../widgets/chess-game";
import { GameOverDialog } from "../../../entities/game";
import { useDispatch, useSelector } from "react-redux";
import { SearchGame } from "../../../features/search-game";
import { PromotionDialog } from "../../../features/select-new-piece";
import { s } from "../../../shared/socket";

export const GamePage = () => {
  const dispatch = useDispatch();
  const userState = useSelector(state => state.user);
  const gameState = useSelector(state => state.game);
  const socketState = useSelector(store => store.socket);
  const isPending = useSelector(state => state.game.promotion.isPending);

  return (
    <LayoutPage>
      {userState.user?.token ?
        <>
          {["default", "searchingGame"].includes(socketState.state) &&
            <div className={styles.gameSearchPage}>
              <h1>Play with stranger</h1>
              <SearchGame onSubmit={(data) => dispatch(s.searchGame(data))}>
                {socketState.state === "default" && <button type="submit">Search</button>}
                {socketState.state === "searchingGame" &&
                  <button type="button" onClick={() => {dispatch(s.cancelSearchGame());}}>Cancel</button>}
              </SearchGame>
            </div>
          }
          {
            [null, "disconnected"].includes(socketState.state) &&
            <div className={styles.gameSearchPage}>
              <h1>Game server is currently unavailable :(</h1>
            </div>
          }
          {socketState.state === "unauthorized" &&
            <div className={styles.gameSearchPage}>
              <h1>Authorization failed</h1>
            </div>
          }
          {socketState.state === "inGame" &&
            <div className={styles.gamePage}>
              <ChessGame />
              {isPending && <PromotionDialog />}
              {gameState.gameOver.winner && <GameOverDialog gameState={gameState} />}
            </div>
          }
        </>
        : <div className={styles.gameSearchPage}>
          <h1>Please, sign in/up for playing</h1>
        </div>
      }
    </LayoutPage>
  );
};