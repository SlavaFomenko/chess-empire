import React, { useEffect } from "react";
import { Login } from "../../../../features/login";
import { LayoutPage } from "../../../../layouts/page-layout";
import { useDispatch, useSelector } from "react-redux";
import { showNotification, hideNotification } from "../../../../shared/notification";
import { useNavigate } from "react-router-dom";

export function LoginPage () {
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const user = useSelector(state => state.user);

  const notification = () => {
    if(user.error){
      let message = user.error === "Server Error" ? "Please, try again later" : `Error: ${user.error}`;
      dispatch(showNotification(message));
    } else if(user.user && user.user.token){
      hideNotification();
      navigate("/");
    }
  }

  useEffect(notification, [user])

  return (
    <LayoutPage>
      <div>
        <h1>Sign In</h1>
        <Login />
      </div>
    </LayoutPage>
  );
}
