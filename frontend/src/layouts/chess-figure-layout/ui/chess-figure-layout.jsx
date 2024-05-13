import React from "react";
import styles from "../styles/chess-figure-layout.module.scss";
import { useDispatch, useSelector } from "react-redux";
import { movePiece, selectPiece } from "../model/chess-figure-layout";
import classNames from "classnames";

export const ChessFigureLayout = ({ figureProps: { coordinate, color }, children }) => {
  const dispatch = useDispatch();
  const selectedPiece = useSelector((state) => state.game.selectedPiece);
  const possibleMoves = useSelector((state) => state.game.possibleMoves);
  const isSelected = selectedPiece && selectedPiece.row === coordinate.row && selectedPiece.col === coordinate.col;
  const isHighlighted = possibleMoves.some(move => move.row === coordinate.row && move.col === coordinate.col);

  const handleSquareClick = () => {
    if (isSelected) {
      dispatch(selectPiece(null));
    } else if (selectedPiece) {
      const newCoordinate = {
        newRow: coordinate.row,
        newCol: coordinate.col,
        figuresColor: color
      };
      dispatch(movePiece(newCoordinate));
    } else {
      dispatch(selectPiece(coordinate));
    }
  };

  return (
    <div
      onClick={handleSquareClick}
      className={classNames(styles.wrapper, { [styles.isSelected]: isSelected, [styles.isHighlighted]: isHighlighted })}
    >
      {children}
    </div>
  );
};