import React from "react";
import styles from '../styles/chess-game.module.scss'
import { ChessBoard } from "../../../features/chess-board";
import { useDispatch, useSelector } from "react-redux";
import { applyTurn, undoTurn } from "../../../layouts/chess-figure-layout/model/chess-figure-layout";



export function ChessGame () {

  const dispach = useDispatch();

  return (
    <div className={styles.wrapper}>
      <ChessBoard/>
      <div className={styles.buttons}>
      <button onClick={()=>dispach((undoTurn()))}>undoTurn</button>
      <button onClick={()=>dispach((applyTurn()))}>applyTurn</button>
      </div>
    </div>
  );
}
