import React, { useState, useEffect } from "react";
import { LayoutPage } from "../../../layouts/page-layout";
import { Outlet, useNavigate, useLocation } from "react-router-dom";
import styles from "../styles/admin-panel-page.module.scss";
import classNames from "classnames";

export function AdminPanelPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const [selectedTab, setSelectedTab] = useState("/users");

  useEffect(() => {
    setSelectedTab(location.pathname.replace("/admin_panel", ""));
  }, [location]);

  const btnHandler = (url) => {
    navigate("/admin_panel" + url);
    setSelectedTab(url);
  };

  return (
    <LayoutPage>
      <div className={styles.wrapper}>
        <div className={styles.tabsBar}>
          <button
            className={classNames({ [`${styles.active}`]: selectedTab === "/users" })}
            onClick={() => btnHandler("/users")}
          >
            Users
          </button>
          <button
            className={classNames({ [`${styles.active}`]: selectedTab === "/games" })}
            onClick={() => btnHandler("/games")}
          >
            Games
          </button>
          <button
            className={classNames({ [`${styles.active}`]: selectedTab === "/ratings" })}
            onClick={() => btnHandler("/ratings")}
          >
            Ratings
          </button>
        </div>
        <div>
          <Outlet />
        </div>
      </div>
    </LayoutPage>
  );
}