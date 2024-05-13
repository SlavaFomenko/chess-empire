import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";
import blackImage from "../../styles/icons/br.png";
import whiteImage from "../../styles/icons/wr.png";

export function Rook (props) {
  const image = props.color === 'black' ? blackImage : whiteImage;

  return (
    <div>
      <ChessFigureLayout figureProps={props}>
        <img src={image} alt={`Rook ${props.color}`}/>
      </ChessFigureLayout>
    </div>
  );
}
