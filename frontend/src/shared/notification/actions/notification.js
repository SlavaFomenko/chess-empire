export const hideNotification = () => {
  return {
    type: "notification/hideNotification"
  };
};

export const showNotification = (data) => {
  return {
    type: "notification/showNotification",
    payload: { data }
  };
};
