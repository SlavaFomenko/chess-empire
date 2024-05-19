import { configureStore } from "@reduxjs/toolkit";
import { userSlice } from "../../shared/user/reducer/userReducer";
import { gameSlice } from "../../shared/game";
import { notificationSlice } from "../../shared/notification";
import { socketMiddleware } from "../../shared/socket/middleware/socketMiddleware";
import { socketSlice } from "../../shared/socket/reducer/socketReducer";

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
