import { createSlice } from "@reduxjs/toolkit";
import { loginUser } from "../../../features/login/model/login";
import { jwtDecode } from "jwt-decode";

export const userSlice = createSlice({
  name: "user",
  initialState: {
    user: null,
    error: null
  },
  reducers: {
    restoreToken: (state, action) => {
      const decoded = jwtDecode(action.payload);
      state.user = { ...state.user, token: action.payload, id: decoded.id, role: decoded.role };
    }
  },
  extraReducers: (builder) => {
    builder.addCase(loginUser.fulfilled, (state, action) => {
      state.error = null;
      const decoded = jwtDecode(action.payload.token);
      state.user = { ...state.user, token: action.payload.token, id: decoded.id, role: decoded.role };
      localStorage.setItem("token", action.payload.token);
    }).addCase(loginUser.rejected, (state, action) => {
      state.user = null;
      state.error = action.payload ? action.payload.data.message : "Server Error";
    });
  }
});