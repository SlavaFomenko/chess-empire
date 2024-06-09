import { baseState } from "./baseState";
import { showNotification } from "../../notification";
import { inGameState } from "./inGameState";
import { s } from "../actions/socket";
import React from "react";
import { GameInviteDialog } from "../../../entities/profile/game-invite-dialog/ui/game-invite-dialog";

export const defaultState = ({ socket, dispatch, history, getState }) => {
  return {
    ...baseState({ socket, dispatch, history, getState }),
    name: "default",
    game_join: (data) => {
      history.push("/game");
      const { id } = getState().user.user;
      const { black, white } = data;
      let myColor = null;
      if (id === black.id) {
        myColor = "black";
      } else if (id === white.id) {
        myColor = "white";
      }
      socket.setState(inGameState, { dispatch, history, getState });

      dispatch({
        type: "game/updateState",
        payload: { ...data, myColor: myColor }
      });
    },
    play_friend_err: (data) => {
      dispatch(showNotification(data ?? "Unknown error"));
      socket.setState(defaultState, { dispatch, history, getState });
    },
    game_invite: (data) => {
      dispatch(showNotification(
        <GameInviteDialog data={data} onAccept={() => dispatch(s.gameAccept(data.id))} onReject={() => dispatch(s.gameReject(data.id))} />
      ));
    }
  };
};




