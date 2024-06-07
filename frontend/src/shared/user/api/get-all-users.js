import { GET_ALL_USERS_URL } from "../../config";
import axios from "axios";

export const getAllUsers = async ({ name, page = 1, order, rating}) => {
  const params = {page};
  if (name && name.trim().length > 0) {
    params.name = name;
  }
  if (order && order.by) {
    params.orderBy = order.by;
    params.order = order.desc ? "desc" : "asc"
  }
  const response = await axios.get(GET_ALL_USERS_URL, {
    params
  });
  return response.data;
};
