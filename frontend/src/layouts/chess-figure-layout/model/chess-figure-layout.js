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

export const goToStep = (step) => {
  return {
    type: "game/goToStep",
    payload: {step}
  };
};
