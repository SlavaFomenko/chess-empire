import React, { useEffect } from "react";
import { Login } from "../../../../features/login";
import { LayoutPage } from "../../../../layouts/page-layout";
import { useDispatch, useSelector } from "react-redux";
import { showNotification, hideNotification } from "../../../../shared/notification";
import { useNavigate } from "react-router-dom";
import { s } from "../../../../shared/socket/actions/socket";
import styles from "../styles/login.module.scss"

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
      dispatch(s.connect());
      navigate("/");
    }
  }

  useEffect(notification, [user])

  return (
    <LayoutPage>
      <div className={styles.loginPage}>
        <h1>Sign In</h1>
        <Login />
      </div>
    </LayoutPage>
  );
}

