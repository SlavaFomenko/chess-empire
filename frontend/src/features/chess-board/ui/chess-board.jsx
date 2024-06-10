import React from "react";
import styles from "../styles/chess-board.module.scss";
import { Rook, Bishop, EmptyField, King, Knight, Pawn, Queen } from "../../../entities/chess-figures";
import { COORDS } from "../../../shared/game";

export function ChessBoard ({
  gameState: { board, myColor },
  event: handleSquareClick
}) {
  const renderBoard = [];
  const reversed = myColor === "white";
  const preparedBoard = reversed ? board?.map(row=>row)?.reverse() : board?.map(row=>row.map(cell=>cell).reverse());
  const letters = ["a", "b", "c", "d", "e", "f", "g", "h"]

  preparedBoard?.map((row, rowIndex) =>
    row.map((piece, columnIndex) => {
        const color = "PNBRQK".includes(piece) ? "white" : "black";
        const coordinate = { row: reversed ? 7 - rowIndex : rowIndex, col: reversed ? columnIndex : 7 - columnIndex };
        const key = `${COORDS[rowIndex]}${columnIndex}`;
        let digit = null, letter = null;
        if(columnIndex === 0){
          digit = reversed ? 8 - rowIndex : rowIndex + 1;
        }

        if(rowIndex === 7){
          letter = letters[reversed ? columnIndex : 7 - columnIndex];
        }
        switch (piece.toUpperCase()) {
          case "R":
            renderBoard.push(
              <Rook letter={letter} digit={digit} event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "N":
            renderBoard.push(
              <Knight letter={letter} digit={digit} event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "B":
            renderBoard.push(
              <Bishop letter={letter} digit={digit} event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "Q":
            renderBoard.push(
              <Queen letter={letter} digit={digit} event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "K":
            renderBoard.push(
              <King letter={letter} digit={digit} event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          case "P":
            renderBoard.push(
              <Pawn letter={letter} digit={digit} event={handleSquareClick} color={color} key={key} coordinate={coordinate} />);
            break;
          default:
            renderBoard.push(
              <EmptyField letter={letter} digit={digit} event={handleSquareClick} color={"empty"} key={key} coordinate={coordinate} />);
            break;
        }
      }
    )
  );

  return (
    <div className={styles.chessBoard}>
      {renderBoard}
    </div>
  );
}
