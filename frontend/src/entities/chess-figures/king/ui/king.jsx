import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";
import blackImage from "../../styles/icons/bk.png";
import whiteImage from "../../styles/icons/wk.png";

export function King (props) {
  const image = props.color === "black" ? blackImage : whiteImage;
  return (
    <div>
      <ChessFigureLayout figureProps={props}>
        <img src={image} alt={`King ${props.color}`} />
      </ChessFigureLayout>
    </div>
  );
}
