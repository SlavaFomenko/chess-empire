import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";
import blackImage from "../../styles/icons/bn.png";
import whiteImage from "../../styles/icons/wn.png";

export function Knight (props) {
  const image = props.color === 'black' ? blackImage : whiteImage;

  return (
    <div>
      <ChessFigureLayout figureProps={props}>
        <img src={image} alt={`Knight ${props.color}`}/>
      </ChessFigureLayout>
    </div>
  );
}
