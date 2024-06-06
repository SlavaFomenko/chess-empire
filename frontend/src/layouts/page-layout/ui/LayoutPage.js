import React, { useState, useEffect } from "react";
import styles from "../styles/layout_page.module.scss";
import { SideBar } from "../../../widgets/side-bar";
import menuIcon from '../../../shared/images/icons/menuIcon.png';
import classNames from "classnames";


export function LayoutPage({ children }) {
  const [isMobile, setIsMobile] = useState(false);

  const [sideBarIsOpen, setSideBarIsOpen] =  useState(!isMobile);

  useEffect(() => {
    const handleResize = () => {
      setIsMobile(window.innerWidth <= 768);
      setSideBarIsOpen(!(window.innerWidth <= 768))
    };
    handleResize();
    window.addEventListener("resize", handleResize);
    return () => {
      window.removeEventListener("resize", handleResize);
    };
  }, []);

  const openSideBar = ()=>{
    setSideBarIsOpen(true)
  }

  const closeSideBar = ()=>{
    setSideBarIsOpen(false)
  }

  return (
    <main className={styles.layout_page}>

      {!sideBarIsOpen && <button className={styles.openSideBarBtn} onClick={openSideBar}>
        <img src={menuIcon} alt={'menu icon'}></img>
      </button>}
      {sideBarIsOpen && <SideBar isMobile={isMobile} closeSideBar={closeSideBar}/>}
      <section className={classNames({[`${styles.mobile}`] :isMobile})}>
        {children}
      </section>
    </main>
  );
}
