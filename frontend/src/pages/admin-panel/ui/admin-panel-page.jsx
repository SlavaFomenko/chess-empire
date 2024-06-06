import React, { useState } from "react";
import { LayoutPage } from "../../../layouts/page-layout";
import { Outlet, useNavigate } from "react-router-dom";
import styles from "../styles/admin-panel-page.module.scss";

export function AdminPanelPage() {
  const navigate = useNavigate();
  const [selectedTab, setSelectedTab] = useState("/users");

  const btnHandler = (url) => {
    navigate("/admin_panel" + url);
    setSelectedTab(url);
  };

  return (
    <LayoutPage>
      <h1>Admin panel</h1>
      <div className={styles.wrapper}>
        <button
          className={selectedTab === "/users" ? styles.active : ""}
          onClick={() => btnHandler("/users")}
        >
          Users
        </button>
        <button
          className={selectedTab === "/games" ? styles.active : ""}
          onClick={() => btnHandler("/games")}
        >
          Games
        </button>
      </div>
      <div>
        <Outlet />
      </div>
    </LayoutPage>
  );
}
