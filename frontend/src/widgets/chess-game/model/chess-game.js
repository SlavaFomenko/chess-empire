export const gameOver = (data) => {
  return {
    type: "game/gameOver",
    payload: data,
  };
};
export const resetTimer = (interval) => {
  return {
    type: "game/resetTimer",
    payload: interval
  };
};
export const tickTimer = () => {
  return {
    type: "game/tickTimer"
  };
};