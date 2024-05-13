import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";
import blackImage from "../../styles/icons/bp.png";
import whiteImage from "../../styles/icons/wp.png";

export function Pawn (props) {
  const image = props.color === 'black' ? blackImage : whiteImage;

  return (
    <div>
      <ChessFigureLayout figureProps={props}>
        <img src={image} alt={`Pawn ${props.color}`}/>
      </ChessFigureLayout>
    </div>
  );
}
