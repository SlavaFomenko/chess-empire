import { createSlice } from "@reduxjs/toolkit";
import { loginUser } from "../../../features/authorization/model/authorization";

export const notificationSlice = createSlice({
  name: "notification",
  initialState: {
    isVisible: false,
    content: null,
  },
  reducers: {
    showNotification:(state, action)=>{
      return{
        ...state,
        isVisible:true,
        content: action.payload.data
      }
    },
    hideNotification:(state)=>{
      return{
        ...state,
        isVisible:false,
        content:null
      }
    }
  },
});