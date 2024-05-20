import React, { useEffect, useState } from "react";
import styles from "../styles/chess-game.module.scss";
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch, useSelector } from "react-redux";
import { goToStep } from "../../../layouts/chess-figure-layout/model/chess-figure-layout";
import { PromotionDialog } from "../../../features/select-new-piece";
import { hideNotification, showNotification } from "../../../shared/notification";
import { s } from "../../../shared/socket"
import { GameOverDialog } from "../../../entities/game/game-over-dialog/ui/game-over-dialog";
import { gameOver } from "../model/chess-game";

export function ChessGame () {
  const dispatch = useDispatch();
  const gameState = useSelector(state => state.game);
  const { currentStep, currentPlayer, black, white, gameOver } = gameState;
  const isPending = useSelector(state => state.game.promotion.isPending);
  const formatTime = (time) => {
    const minutes = Math.floor(time / 60);
    const seconds = time % 60;
    return  `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`
  };

  const submitResign = () => {
    dispatch(showNotification(
      <div>
       Are you sure you want to resign?
        <button onClick={() =>{
          dispatch(s.resign())
          dispatch(hideNotification())
        }
        }>Yes</button>
      </div>
    ))
  }

  return (
    <div className={styles.wrapper}>
      <ChessBoard />
      {isPending && <PromotionDialog />}
      <div className={styles.rightPanel}>
        <div>
          <p>Black time: {black && formatTime(black.time)}</p>
          <p>White time: {black && formatTime(white.time)}</p>
        </div>
        <div className={styles.buttons}>
          <button onClick={() => dispatch((goToStep(currentStep - 1)))}>Undo</button>
          <button onClick={() => dispatch((goToStep(currentStep + 1)))}>Redo</button>
        </div>
        <div>
          <button onClick={submitResign}>Resign</button>
        </div>
      </div>
    </div>
  );
}
