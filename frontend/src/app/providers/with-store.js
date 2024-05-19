import { configureStore } from "@reduxjs/toolkit";
import { userSlice } from "../../shared/user/reducer/userReducer";
import { gameSlice } from "../../shared/game";
import { notificationSlice } from "../../shared/notification";
import { socketMiddleware } from "../../shared/socket/middleware/socketMiddleware";

export const store = configureStore({
  reducer:{
    user:userSlice.reducer,
    game:gameSlice.reducer,
    notification: notificationSlice.reducer
  },
  middleware: (getDefaultMiddleware) => [socketMiddleware, ...getDefaultMiddleware()],
  devTools:true
})
