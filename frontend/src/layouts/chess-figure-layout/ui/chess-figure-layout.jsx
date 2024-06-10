import React from "react";
import styles from "../styles/chess-figure-layout.module.scss";
import { useSelector } from "react-redux";
import classNames from "classnames";

export const ChessFigureLayout = ({ figureProps: { coordinate, event, digit, letter }, children }) => {
  const {
    selectedPiece,
    availableMoves
  } = useSelector(state => state.game);

  const isSelected = selectedPiece && selectedPiece.row === coordinate.row && selectedPiece.col === coordinate.col;
  const isHighlighted = availableMoves.some(move => move.row === coordinate.row && move.col === coordinate.col);

  return (
    <div
      onClick={() => event(coordinate)}
      className={classNames(styles.wrapper, {
        [styles.isSelected]: isSelected,
        [styles.isHighlighted]: isHighlighted,
        [styles.dark]: (coordinate.row + coordinate.col) % 2 === 0
      })}
    >
      {digit && <div className={styles.numbers}>
        {digit}
      </div>}
      {letter &&
        <div className={styles.letters}>
          {letter}
        </div>}
      {children}
    </div>
  );
};