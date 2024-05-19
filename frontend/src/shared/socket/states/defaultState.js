import { baseState } from "./baseState";

export const defaultState = ({ socket, dispatch, history }) => {
  return {
    ...baseState({ socket, dispatch, history }),
    name: "default"
  };
};




