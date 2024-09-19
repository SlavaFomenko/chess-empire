import React from "react";
import { ChessFigureLayout } from "../../../../layouts/chess-figure-layout";

export const EmptyField = (props) => {
  return (
    <div>
      <ChessFigureLayout figureProps={props}>

      </ChessFigureLayout>
    </div>
  );
}
