export const selectPiece = (coordinates) => {
  return {
    type: "game/selectPiece",
    payload: coordinates,
  };
};

export const movePiece = (newCoordinates) => {
  return {
    type: "game/movePiece",
    payload: newCoordinates,
  };
};
export const applyTurn = (turns) => {
  return {
    type: "game/applyTurn",
    payload:turns
  };
};
export const undoTurn = (turns) => {
  return {
    type: "game/undoTurn",
    payload:turns
  };
};


