import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";
import blackImage from '../../styles/icons/bb.png'
import whiteImage from '../../styles/icons/wb.png'

export function Bishop (props) {
  const image = props.color === 'black' ? blackImage : whiteImage;

  return (
    <div>
      <ChessFigureLayout figureProps={props}>
        <img src={image} alt={`Bishop ${props.color}`}/>
      </ChessFigureLayout>
    </div>
  );
}
