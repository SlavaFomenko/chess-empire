import React from "react";
import styles from "../styles/layout_page.module.scss";
import { SideBar } from "../../../widgets/side-bar";

export function LayoutPage ({ children }) {
  return (
    <main className={styles.layout_page}>
      <SideBar />
      {children}
    </main>
  );
}