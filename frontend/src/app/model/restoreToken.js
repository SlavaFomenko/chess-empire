export const restoreToken = (token) => {
  return {
    type: "user/restoreToken",
    payload: token,
  };
};
