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
import { UsersPage } from "./admin-panel/pages/users";
import { GamesPage } from "./admin-panel/pages/games";
import { RatingsPage } from "./admin-panel/pages/ratings";
import { UserPage } from "./user";

export class Routing extends React.Component {
  shouldComponentUpdate () {
    return false;
  }

  render () {
    return (
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegistrationPage />} />
        <Route path="/" element={<PrivateRouteWrapper targetRoles={["ROLE_ADMIN", "ROLE_USER"]} />}>
          <Route path="/game" element={<GamePage />} />
          <Route path="/game-review/*" element={<GameReviewPage />} />
          <Route path="/user/*" element={<UserPage />} />
          <Route path="/profile" element={<ProfilePage />} />
        </Route>
        <Route path="/admin_panel/*" element={<PrivateRouteWrapper targetRoles={["ROLE_ADMIN"]} />}>
          <Route element={<AdminPanelPage />}>
            <Route path="users" element={<UsersPage/>} />
            <Route path="games" element={<GamesPage/>} />
            <Route path="ratings" element={<RatingsPage/>} />
          </Route>
        </Route>
        <Route path="*" element={<NotFoundPage />} />
      </Routes>
    );
  }
}
