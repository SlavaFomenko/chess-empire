import { configureStore } from "@reduxjs/toolkit";
import { userSlice } from "../../shared/user/reducer/user-reducer";
import { gameSlice } from "../../shared/game";
import { notificationSlice } from "../../shared/notification";
import { socketMiddleware } from "../../shared/socket/middleware/socket-middleware";
import { socketSlice } from "../../shared/socket/reducer/socket-reducer";

export const store = configureStore({
  reducer:{
    socket:socketSlice.reducer,
    user:userSlice.reducer,
    game:gameSlice.reducer,
    notification: notificationSlice.reducer
  },
  middleware: (getDefaultMiddleware) => [socketMiddleware, ...getDefaultMiddleware()],
  devTools:true
})
