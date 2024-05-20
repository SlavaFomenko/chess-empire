import { baseState } from "./baseState";

export const defaultState = ({ socket, dispatch, history, getState }) => {
  return {
    ...baseState({ socket, dispatch, history, getState }),
    name: "default"
  };
};




