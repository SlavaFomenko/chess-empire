import { createSlice } from "@reduxjs/toolkit";
import { loginUser } from "../../../features/authorization/model/authorization";

export const userSlice = createSlice({
  name: "user",
  initialState: {
    user: null,
    error: null,
  },
  reducers: {},
  extraReducers: (builder) => {
    builder
    .addCase(loginUser.fulfilled, (state, action) => {
      state.error = null;
      state.user = action.payload;
    })
    .addCase(loginUser.rejected, (state, action) => {
      state.user = null;
      state.error = action.payload ? action.payload : "Server Error";
    });
  },
});