import React from "react";
import styles from "../styles/home.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import { SearchGame } from "../../../features/search-game";

export function HomePage () {

  return (
    <LayoutPage>
      <div className={styles.home_page}>
        <h1>
          HomePage
        </h1>
        <SearchGame/>
      </div>
    </LayoutPage>
  );
}