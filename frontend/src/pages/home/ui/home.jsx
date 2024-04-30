import React from "react";
import styles from "../styles/home.module.scss";
import { SideBar } from "../../../widgets/side-bar";
import { LayoutPage } from "../../../layouts/layout-page";

export function HomePage () {
  return (
    <LayoutPage>
      <div className={styles.home_page}>
        <h1>
          HomePage
        </h1>
      </div>
    </LayoutPage>
  );
}