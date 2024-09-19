import React from "react";
import styles from "../styles/banner-layout.module.scss";

export const BannerLayout = ({ children, onClick = ()=>{} }) => {
  return (
    <div className={styles.container} onClick={onClick}>
      {children}
    </div>
  );
}
