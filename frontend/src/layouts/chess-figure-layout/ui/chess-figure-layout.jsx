import React, { useEffect } from "react";
import styles from "../styles/chess-figure-layout.module.scss";
import { useDispatch, useSelector } from "react-redux";
import { movePiece, selectPiece } from "../model/chess-figure-layout";
import classNames from "classnames";
import { s } from "../../../shared/socket";

export const ChessFigureLayout = ({ figureProps: { coordinate, color }, children }) => {
  const dispatch = useDispatch();
  const {
    selectedPiece,
    possibleMoves,
    colorSelectedPiece,
    moveAllowed
  } = useSelector(state => state.game);
  const isSelected = selectedPiece && selectedPiece.row === coordinate.row && selectedPiece.col === coordinate.col;
  const isHighlighted = possibleMoves.some(move => move.row === coordinate.row && move.col === coordinate.col);

  const handleSquareClick = () => {
    if (!moveAllowed) {
      return;
    }

    if (isSelected) {
      return dispatch(selectPiece(null));
    }

    if (selectedPiece === null || colorSelectedPiece === color) {
      return dispatch(selectPiece(coordinate));
    }

    const newCoordinate = {
      newRow: coordinate.row,
      newCol: coordinate.col,
      figuresColor: color
    };

    dispatch(movePiece(newCoordinate));
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