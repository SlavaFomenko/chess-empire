import React from "react";
import { Route, Routes } from "react-router-dom";
import { GamePage } from "./game";
import { LoginPage } from "./sign-in/login";
import { RegistrationPage } from "./sign-in/registration";
import { HomePage } from "./home";
import { NotFoundPage } from "./page-404";
import { ProfilePage } from "./profile";
import { GameReviewPage } from "./game-review";
import { AdminPanelPage } from "./admin-panel";
import { PrivateRouteWrapper } from "../shared/routing";

export class Routing extends React.Component {
  shouldComponentUpdate() {
    return false;
  }

  render() {
    return (
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/game" element={<GamePage />} />
        <Route path="/game-review/*" element={<GameReviewPage />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegistrationPage />} />
        <Route path="/profile" element={<ProfilePage />} />
        <Route path="*" element={<NotFoundPage />} />
        <Route path="/admin_panel" element={<PrivateRouteWrapper />}>
          <Route path="" element={<AdminPanelPage />} />
        </Route>
      </Routes>
    );
  }
}
