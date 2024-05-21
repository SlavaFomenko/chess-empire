import { createSlice } from "@reduxjs/toolkit";

export const socketSlice = createSlice({
  name: "socket",
  initialState: {
    state: null
  },
  reducers: {
    setState: (state, action) => {
      return { ...state, state: action.payload };
    }
  }
});