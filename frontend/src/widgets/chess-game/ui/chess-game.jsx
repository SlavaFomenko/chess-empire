import React from "react";
import styles from '../styles/chess-game.module.scss'
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch } from "react-redux";
import { applyTurn, undoTurn } from "../../../layouts/chess-figure-layout/model/chess-figure-layout";



export function ChessGame () {

  const dispatch = useDispatch();

  return (
    <div className={styles.wrapper}>
      <ChessBoard/>
      <div className={styles.buttons}>
      <button onClick={()=>dispatch((undoTurn()))}>undoTurn</button>
      <button onClick={()=>dispatch((applyTurn()))}>applyTurn</button>
      </div>
    </div>
  );
}
