import React from "react";
import styles from "../styles/chess-game.module.scss";
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch, useSelector } from "react-redux";
import { goToStep } from "../../../layouts/chess-figure-layout/model/chess-figure-layout";
import { PromotionDialog } from "../../../features/select-new-piece";

export function ChessGame () {
  const dispatch = useDispatch();
  const gameState = useSelector(state => state.game);
  const isPending = useSelector(state => state.game.promotion.isPending);

  return (
    <div className={styles.wrapper}>
      <ChessBoard />

      {isPending && <PromotionDialog/>}

      <div className={styles.buttons}>
        <button onClick={()=>dispatch((goToStep(gameState.currentStep - 1)))}>undoTurn</button>
        <button onClick={()=>dispatch((goToStep(gameState.currentStep + 1)))}>applyTurn</button>
      </div>
    </div>
  );
}
