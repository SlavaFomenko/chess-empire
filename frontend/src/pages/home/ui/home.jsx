import React from "react";
import styles from "../styles/home.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { useDispatch } from "react-redux";
import { showNotification } from "../../../shared/notification";

export function HomePage () {

  const dispatch = useDispatch();

  const btnHandler = () => {
    const data = "hello";
    dispatch(showNotification(data));
  };

  return (
    <LayoutPage>
      <div className={styles.home_page}>
        <h1>
          HomePage
        </h1>
        <button onClick={btnHandler}>show notification</button>
      </div>
    </LayoutPage>
  );
}