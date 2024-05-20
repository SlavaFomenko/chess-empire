import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { selectPromotionPiece } from "../model/select-promotion-piece";
import styles from '../styles/select-new-piece.module.scss'
import { s } from "../../../shared/socket";

export const PromotionDialog = () => {
  const dispatch = useDispatch();
  const { isPending, position } = useSelector((state) => state.game.promotion);
  const { gameHistory } = useSelector((state) => state.game);

  if (!isPending) {
    return null;
  }

  const handleSelect = (piece) => {
    dispatch(selectPromotionPiece(piece));
    // dispatch(s.turn(gameHistory[gameHistory.length - 1]));
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
