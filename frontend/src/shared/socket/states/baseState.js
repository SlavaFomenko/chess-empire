import { showNotification } from "../../notification";

export const baseState = ({ socket, dispatch }) => {
  return {
    close: (data) => {
      dispatch(showNotification("Connection with server lost"));
    }
  };
};


