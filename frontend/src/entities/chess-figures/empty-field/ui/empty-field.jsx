import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";

export function EmptyField (props) {
  return (
    <div>
      <ChessFigureLayout figureProps={props}>

      </ChessFigureLayout>
    </div>
  );
}
