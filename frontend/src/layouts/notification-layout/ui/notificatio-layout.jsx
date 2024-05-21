import React from "react";
import { createPortal } from "react-dom";
import styles from "../styles/notification-layout.module.scss";
import { useDispatch } from "react-redux";
import { hideNotification } from "../../../shared/notification";

export function NotificationLayout ({ children, buttons }) {
  const dispatch = useDispatch();

  const btnHandler = () => {
    dispatch(hideNotification());
  };

  return createPortal(
    <div className={styles.wrapper} onClick={()=>{dispatch(hideNotification())}}>
      <div className={styles.notification} onClick={(e)=>{e.stopPropagation()}}>
        {children}
      </div>
    </div>,
    document.body
  );
}
