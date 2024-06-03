import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { selectPromotionPiece } from "../model/select-promotion-piece";
import styles from "../styles/select-new-piece.module.scss";
import blackQueen from "../../../entities/chess-figures/styles/icons/bq.png";
import blackRook from "../../../entities/chess-figures/styles/icons/br.png";
import blackBishop from "../../../entities/chess-figures/styles/icons/bb.png";
import blackKnight from "../../../entities/chess-figures/styles/icons/bn.png";
import whiteQueen from "../../../entities/chess-figures/styles/icons/wq.png";
import whiteRook from "../../../entities/chess-figures/styles/icons/wr.png";
import whiteBishop from "../../../entities/chess-figures/styles/icons/wb.png";
import whiteKnight from "../../../entities/chess-figures/styles/icons/wn.png";
import { BannerLayout } from "../../../layouts/banner-layout";

export const PromotionDialog = () => {
  const dispatch = useDispatch();
  const { isPending } = useSelector((state) => state.game.promotion);
  const { myColor } = useSelector((state) => state.game);

  const figures = myColor === "black" ?
    {
      q: blackQueen,
      r: blackRook,
      b: blackBishop,
      n: blackKnight
    } : {
      q: whiteQueen,
      r: whiteRook,
      b: whiteBishop,
      n: whiteKnight
    };

  if (!isPending) {
    return null;
  }

  const handleSelect = (piece) => {
    dispatch(selectPromotionPiece(piece));
  };

  return (
    <BannerLayout>
      <div className={styles.wrapper}>
        <button onClick={() => handleSelect("Q")}><img src={figures.q} /></button>
        <button onClick={() => handleSelect("R")}><img src={figures.r} /></button>
        <button onClick={() => handleSelect("B")}><img src={figures.b} /></button>
        <button onClick={() => handleSelect("N")}><img src={figures.n} /></button>
      </div>
    </BannerLayout>
  );
};
