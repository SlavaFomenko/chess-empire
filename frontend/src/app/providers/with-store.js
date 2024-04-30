import { configureStore, } from "@reduxjs/toolkit";
import { userSlice } from "../../shared/user/reducer/userReducer";

export const store = configureStore({
  reducer:{
    user:userSlice.reducer
  },
  devTools:true
})
