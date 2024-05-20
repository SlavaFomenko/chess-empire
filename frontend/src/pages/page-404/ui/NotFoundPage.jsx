import React from "react";
import { LayoutPage } from "../../../layouts/page-layout";
import styles from "../styles/not-found.module.scss";

export function NotFoundPage () {
  return (
    <LayoutPage>
      <div className={styles.notFoundPage}>
        <h1>Oops! We can't find this page :(</h1>
      </div>
    </LayoutPage>
  );
}