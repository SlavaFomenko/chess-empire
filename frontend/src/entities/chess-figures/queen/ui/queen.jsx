import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";
import blackImage from "../../styles/icons/bq.png";
import whiteImage from "../../styles/icons/wq.png";

export function Queen (props) {
  const image = props.color === 'black' ? blackImage : whiteImage;

  return (
    <div>
      <ChessFigureLayout figureProps={props}>
        <img src={image} alt={`Queen ${props.color}`}/>
      </ChessFigureLayout>
    </div>
  );
}
