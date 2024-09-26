import axios from "axios";
import {GET_USER_BY_ID} from "../../config";

export const getUserById = async (id,token) => {
  return axios.get(GET_USER_BY_ID(id), {
    headers: {
      Authorization: `Bearer ${token}`
    }
  })
};