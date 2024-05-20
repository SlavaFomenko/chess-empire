
import { baseState } from "./baseState";
import { gameOver } from "../../../widgets/chess-game/model/chess-game";

export const inGameState = ({ socket, dispatch, history }) => {
  return {
    ...baseState({ socket, dispatch, history }),
    name: "inGame",
    game_update: (data) => {
      dispatch({
        type: "game/updateState",
        payload: data,
      });
    },
    game_timer_update: (data) => {
      dispatch({
        type: "game/updateTimers",
        payload: data,
      });
    },
    game_end: (data) => {
      dispatch(gameOver(data));
    }
  };
};




