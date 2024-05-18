import React from "react";
import { createPortal } from "react-dom";
import styles from "../styles/notification-layout.module.scss";
import { useDispatch } from "react-redux";
import { hideNotification } from "../../../shared/notification";

export function NotificationLayout ({ children, onClose }) {

  const dispatch = useDispatch();

  const btnHandler = () => {
    dispatch(hideNotification());
  };

  return createPortal(
    <div className={styles.wrapper}>
      <span>Notification</span>
      {children}
      <button onClick={btnHandler}>close</button>
    </div>,
    document.body
  );
}
