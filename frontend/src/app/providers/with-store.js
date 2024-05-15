import { configureStore, } from "@reduxjs/toolkit";
import { userSlice } from "../../shared/user/reducer/userReducer";
import { gameSlice } from "../../shared/game";
import { notificationSlice } from "../../shared/notification";

export const store = configureStore({
  reducer:{
    user:userSlice.reducer,
    game:gameSlice.reducer,
    notification: notificationSlice.reducer
  },
  devTools:true
})
