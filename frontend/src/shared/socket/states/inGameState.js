import { baseState } from "./baseState";
import { gameOver } from "../../../widgets/chess-game/model/chess-game";

export const inGameState = ({ socket, dispatch, history, getState }) => {
  window.onbeforeunload = e => e.preventDefault();

  return {
    ...baseState({ socket, dispatch, history, getState }),
    name: "inGame",
    game_update: (data) => {
      dispatch({
        type: "game/updateState",
        payload: data
      });
    },
    game_timer_update: (data) => {
      dispatch({
        type: "game/updateTimers",
        payload: data
      });
    },
    game_end: (data) => {
      dispatch(gameOver(data));
      window.onbeforeunload = null;
    }
  };
};




