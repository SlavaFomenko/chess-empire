import React from "react";
import styles from "../styles/chess-figure-layout.module.scss";
import { useSelector } from "react-redux";
import classNames from "classnames";

export const ChessFigureLayout = ({ figureProps: { coordinate, event }, children }) => {
  const {
    selectedPiece,
    availableMoves,
  } = useSelector(state => state.game);

  const isSelected = selectedPiece && selectedPiece.row === coordinate.row && selectedPiece.col === coordinate.col;
  const isHighlighted = availableMoves.some(move => move.row === coordinate.row && move.col === coordinate.col);

  return (
    <div
      onClick={()=>event(coordinate)}
      className={classNames(styles.wrapper, {
        [styles.isSelected]: isSelected,
        [styles.isHighlighted]: isHighlighted,
        [styles.dark]: (coordinate.row + coordinate.col) % 2 === 1
      })}
    >
     <div className={styles.coords}>{coordinate.row} {coordinate.col}</div>
      {children}
    </div>
  );
};