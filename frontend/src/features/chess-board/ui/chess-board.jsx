import React, { useEffect } from "react";
import styles from "../styles/chess-board.module.scss";
import { Rook, Bishop, EmptyField, King, Knight, Pawn, Queen } from "../../../entities/chess-figures";
import { useDispatch, useSelector } from "react-redux";
import { COORDS } from "../../../shared/game";
import { s } from "../../../shared/socket";


export function ChessBoard () {
  const {initialBoard, currentPlayer, myColor, gameHistory, hasMadeTurn } = useSelector(state => state.game);
  const dispatch = useDispatch();

  const renderBoard = [];

  useEffect(() => {
    if (hasMadeTurn && gameHistory.length > 0) {
      dispatch(s.turn(gameHistory[gameHistory.length - 1]));
    }
  }, [hasMadeTurn]);

  initialBoard.map((row, rowIndex) =>
    row.map((e, columnIndex) => {
      const specialColumnIndex = columnIndex+1
        switch (e) {
          case "R":
            renderBoard.push(
              <Rook color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "N":
            renderBoard.push(
              <Knight color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "B":
            renderBoard.push(
              <Bishop color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "Q":
            renderBoard.push(
              <Queen color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "K":
            renderBoard.push(
              <King color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "P":
            renderBoard.push(
              <Pawn color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "r":
            renderBoard.push(
              <Rook color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "n":
            renderBoard.push(
              <Knight color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "b":
            renderBoard.push(
              <Bishop color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "q":
            renderBoard.push(
              <Queen color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "k":
            renderBoard.push(
              <King color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "p":
            renderBoard.push(
              <Pawn color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          default:
            renderBoard.push(
              <EmptyField color={"empty"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
        }
      }
    )
  );

  return (
    <div className={styles.chessBoard}>
      {myColor === "white" ? renderBoard : renderBoard.reverse()}
    </div>
  );
}
