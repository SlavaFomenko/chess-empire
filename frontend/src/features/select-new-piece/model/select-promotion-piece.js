export const selectPromotionPiece = (selectedPiece) => {
  return {
    type: "game/selectPromotionPiece",
    payload: selectedPiece,
  };
};