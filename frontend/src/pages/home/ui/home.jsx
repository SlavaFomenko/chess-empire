import React from "react";
import styles from "../styles/home.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";

export function HomePage () {

  return (
    <LayoutPage>
      <div className={styles.homePage}>
        <h1>Welcome to Chess Empire!</h1>
      </div>
    </LayoutPage>
  );
}