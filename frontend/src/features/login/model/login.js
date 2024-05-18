import { createAsyncThunk } from "@reduxjs/toolkit";
import { login } from "../../../shared/user";

export const loginUser = createAsyncThunk(
  "user/login",
  async (data, { rejectWithValue, dispatch }) => {
    try {
      const result = await login(data);
      console.log(result)
      if(!result.token){
        throw new Error();
      }
      return result;
    } catch (error) {
      console.log(error)
      if (!error.response) {
        throw error;
      }
      return rejectWithValue(error.response);
    }
  }
);
