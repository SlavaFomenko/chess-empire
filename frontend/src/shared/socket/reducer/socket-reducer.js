import { createSlice } from "@reduxjs/toolkit";

export const socketSlice = createSlice({
  name: "socket",
  initialState: {
    state: null,
    devices: []
  },
  reducers: {
    setState: (state, action) => {
      return { ...state, state: action.payload };
    },
    updateDevices: (state, action) => {
      return { ...state, devices: action.payload };
    }
  }
});