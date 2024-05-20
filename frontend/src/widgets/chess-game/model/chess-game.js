export const gameOver = (type) => {
  return {
    type: "game/gameOver",
    payload: type,
  };
};