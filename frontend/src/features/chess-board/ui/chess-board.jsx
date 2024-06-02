import React, { useEffect } from "react";
import styles from "../styles/chess-board.module.scss";
import { Rook, Bishop, EmptyField, King, Knight, Pawn, Queen } from "../../../entities/chess-figures";
import { COORDS } from "../../../shared/game";


export function ChessBoard ({ gameState:{initialBoard, moveAllowed,selectedPiece,myColor, gameHistory, hasMadeTurn }, event:handleSquareClick }) {
  const renderBoard = [];

  useEffect(() => {
    if (hasMadeTurn && gameHistory.length > 0) {
      // dispatch(s.turn(gameHistory[gameHistory.length - 1]));
    }
  }, [hasMadeTurn]);

  initialBoard.map((row, rowIndex) =>
    row.map((e, columnIndex) => {
      const specialColumnIndex = columnIndex+1
        switch (e) {
          case "R":
            renderBoard.push(
              <Rook event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "N":
            renderBoard.push(
              <Knight event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "B":
            renderBoard.push(
              <Bishop event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "Q":
            renderBoard.push(
              <Queen event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "K":
            renderBoard.push(
              <King event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "P":
            renderBoard.push(
              <Pawn event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"white"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "r":
            renderBoard.push(
              <Rook event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "n":
            renderBoard.push(
              <Knight event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "b":
            renderBoard.push(
              <Bishop event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "q":
            renderBoard.push(
              <Queen event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "k":
            renderBoard.push(
              <King event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          case "p":
            renderBoard.push(
              <Pawn event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"black"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
            break;
          default:
            renderBoard.push(
              <EmptyField event={()=>handleSquareClick(moveAllowed,selectedPiece,{row:rowIndex,col:columnIndex},e.toUpperCase()===e?'white':"black")} color={"empty"} key={`${COORDS[rowIndex]}${columnIndex}`} coordinate = {{row:rowIndex,col:columnIndex}} notation={`${COORDS[rowIndex]}${specialColumnIndex}`} />);
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
