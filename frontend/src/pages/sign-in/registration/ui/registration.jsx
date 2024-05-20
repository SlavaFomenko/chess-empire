import React from "react";
import { LayoutPage } from "../../../../layouts/page-layout";
import { Registration } from "../../../../features/registration";
import { useNavigate } from "react-router-dom";
import { useDispatch } from "react-redux";
import { showNotification } from "../../../../shared/notification";
import styles from "../styles/registration.module.scss"

export function RegistrationPage (props) {
  const navigate = useNavigate();
  const dispatch = useDispatch();

  const notification = (data) => {
    const message = data.response?.data?.message ? data.response.data.message : "Please, try again later";
    dispatch(showNotification(message));
  };

  return (
    <LayoutPage>
      <div className={styles.registrationPage}>
        <h1>Sign Up</h1>
        <Registration notification={notification} navigate={navigate} />
      </div>
    </LayoutPage>
  );
}