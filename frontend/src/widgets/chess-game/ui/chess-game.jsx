import React from "react";
import styles from "../styles/chess-game.module.scss";
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch, useSelector } from "react-redux";
import { goToStep } from "../../../layouts/chess-figure-layout/model/chess-figure-layout";
import { PromotionDialog } from "../../../features/select-new-piece";
import { gameOver } from "../model/chess-game";

export function ChessGame () {
  const dispatch = useDispatch();
  const gameState = useSelector(state => state.game);
  const isPending = useSelector(state => state.game.promotion.isPending);
  const { winner, reason } = useSelector(state => state.game.gameOverYet);

  const buttonHandler = () => {
    dispatch(gameOver({
      winner: "w",
      reason: "mate",
      w_rating: 10,
      b_rating: -10
    }));
  };

  return (
    <div className={styles.wrapper}>
      <ChessBoard />
      <button onClick={buttonHandler}>some game end</button>
      {isPending && <PromotionDialog />}
      {winner &&
        <div className={styles.gameOverCard}>
          {reason}
        </div>}
      <div className={styles.buttons}>
        <button onClick={() => dispatch((goToStep(gameState.currentStep - 1)))}>undoTurn</button>
        <button onClick={() => dispatch((goToStep(gameState.currentStep + 1)))}>applyTurn</button>
      </div>
    </div>
  );
}
