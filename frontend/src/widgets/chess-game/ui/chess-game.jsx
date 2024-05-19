import React from "react";
import styles from "../styles/chess-game.module.scss";
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch } from "react-redux";
import { applyTurn, undoTurn } from "../../../layouts/chess-figure-layout/model/chess-figure-layout";
import { PromotionDialog } from "../../../features/select-new-piece";

export function ChessGame () {

  const dispach = useDispatch();
  const isPending = useSelector(state => state.game.promotion.isPending);
  return (
    <div className={styles.wrapper}>
      <ChessBoard />

      {isPending && <PromotionDialog/>}

      <div className={styles.buttons}>
        <button onClick={() => dispach((undoTurn()))}>undoTurn</button>
        <button onClick={() => dispach((applyTurn()))}>applyTurn</button>
      </div>
    </div>
  );
}
