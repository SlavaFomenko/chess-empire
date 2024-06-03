import React from "react";
import styles from "../styles/chess-board.module.scss";
import { Rook, Bishop, EmptyField, King, Knight, Pawn, Queen } from "../../../entities/chess-figures";
import { COORDS } from "../../../shared/game";

export function ChessBoard ({
  gameState: { board, myColor },
  event: handleSquareClick
}) {
  const renderBoard = [];

  board.map((row, rowIndex) =>
    row.map((piece, columnIndex) => {
        const color = "PNBRQK".includes(piece) ? "white" : "black";
        const coordinate = { row: rowIndex, col: columnIndex };
        const key = `${COORDS[rowIndex]}${columnIndex}`;
        switch (piece.toUpperCase()) {
          case "R":
            renderBoard.push(
              <Rook event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "N":
            renderBoard.push(
              <Knight event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "B":
            renderBoard.push(
              <Bishop event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "Q":
            renderBoard.push(
              <Queen event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "K":
            renderBoard.push(
              <King event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "P":
            renderBoard.push(
              <Pawn event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          default:
            renderBoard.push(
              <EmptyField event={handleSquareClick} color={"empty"} key={key} coordinate={coordinate} />);
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
