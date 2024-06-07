import { GET_ALL_USERS_URL } from "../../config";
import axios from "axios";

export const getAllUsers = async ({ name, page = 1, order = {by: null}, rating = {min: null, max: null}}) => {
  const params = {page};
  if (name && name.trim().length > 0) {
    params.name = name;
  }
  if (order.by) {
    params.orderBy = order.by;
    params.order = order.desc ? "desc" : "asc"
  }
  if (rating.min) {
    params.ratingMin = rating.min;
  }
  if (rating.max) {
    params.ratingMax = rating.max;
  }
  const response = await axios.get(GET_ALL_USERS_URL, {
    params
  });
  return response.data;
};
