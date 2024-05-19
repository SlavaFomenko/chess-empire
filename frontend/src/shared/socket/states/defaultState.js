import { baseState } from "./baseState";

export const defaultState = ({ socket, dispatch }) => {
  return {
    ...baseState({ socket, dispatch })
  };
};




