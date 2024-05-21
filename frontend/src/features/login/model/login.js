import { createAsyncThunk } from "@reduxjs/toolkit";
import { login } from "../../../shared/user";

export const loginUser = createAsyncThunk(
  "user/login",
  async (data, { rejectWithValue, dispatch }) => {
    try {
      const result = await login(data);
      if(!result.token){
        throw new Error();
      }
      return result;
    } catch (error) {
      if (!error.response) {
        throw error;
      }
      return rejectWithValue(error.response);
    }
  }
);
