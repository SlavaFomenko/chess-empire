import { createSlice } from "@reduxjs/toolkit";
import { loginUser } from "../../../features/login/model/login";

export const userSlice = createSlice({
  name: "user",
  initialState: {
    user: null,
    error: null
  },
  reducers: {
    restoreToken: (state, action) => {
      state.user = { ...state.user, token: action.payload };
    }
  },
  extraReducers: (builder) => {
    builder.addCase(loginUser.fulfilled, (state, action) => {
      state.error = null;
      state.user = { ...state.user, token: action.payload.token };
      localStorage.setItem("token", action.payload.token);
    }).addCase(loginUser.rejected, (state, action) => {
      state.user = null;
      state.error = action.payload ? action.payload.data.message : "Server Error";
    });
  }
});