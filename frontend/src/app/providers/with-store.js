import { configureStore, } from "@reduxjs/toolkit";
import { userSlice } from "../../shared/user/reducer/userReducer";
import { gameSlice } from "../../shared/game";

export const store = configureStore({
  reducer:{
    user:userSlice.reducer,
    game:gameSlice.reducer
  },
  devTools:true
})
