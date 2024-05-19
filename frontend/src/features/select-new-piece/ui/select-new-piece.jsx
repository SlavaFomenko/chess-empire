import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { selectPromotionPiece } from "../model/select-promotion-piece";
import styles from '../styles/select-new-piece.module.scss'

export const PromotionDialog = () => {
  const dispatch = useDispatch();
  const { isPending, position } = useSelector((state) => state.game.promotion);

  if (!isPending) {
    return null;
  }

  const handleSelect = (piece) => {
    dispatch(selectPromotionPiece(piece));
  };

  return (
    <div className={styles.wrapper}>
      <button onClick={() => handleSelect('Q')}>Ферзь</button>
      <button onClick={() => handleSelect('R')}>Ладья</button>
      <button onClick={() => handleSelect('B')}>Слон</button>
      <button onClick={() => handleSelect('N')}>Конь</button>
    </div>
  );
};
