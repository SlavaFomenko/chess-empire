import { registration } from "../../../shared/user";

export const registerUser = async (data) => {
  const response = await registration(data);

};