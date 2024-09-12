import React from "react";
import { PageLayout } from "../../../layouts/page-layout";
import styles from "../styles/not-found.module.scss";

export function NotFoundPage () {
  return (
    <PageLayout>
      <div className={styles.notFoundPage}>
        <h1>Oops! We can't find this page :(</h1>
      </div>
    </PageLayout>
  );
}