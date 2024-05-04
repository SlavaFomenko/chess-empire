import { createAsyncThunk } from "@reduxjs/toolkit";
import { authorization } from "../../../shared/user";

export const loginUser = createAsyncThunk(
  "user/login",
  async (data, { rejectWithValue }) => {
    try {
      return await authorization(data);
    } catch (error) {
      if (!error.response) {
        throw error;
      }
      return rejectWithValue(error.response.status);
    }
  }
);
