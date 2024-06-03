import React from "react";
import styles from "../styles/banner-layout.module.scss";

export function BannerLayout ({ children, onClick = ()=>{} }) {
  return (
    <div className={styles.container} onClick={onClick}>
      {children}
    </div>
  );
}
