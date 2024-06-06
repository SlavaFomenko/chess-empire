import React from "react";
import { LayoutPage } from "../../../layouts/page-layout";
import { Outlet, useNavigate } from "react-router-dom";

export function AdminPanelPage () {

  const navigate = useNavigate()

  const btnHandler =(url)=>{
    navigate('/admin_panel' + url)
  }

  return (
    <LayoutPage>
      <h1>Admin panel</h1>
      <button onClick={()=>btnHandler('/users')}>users</button>
      <button onClick={()=>btnHandler('/games')}>games</button>
      <div>
        <Outlet/>
      </div>
    </LayoutPage>
  );
}
