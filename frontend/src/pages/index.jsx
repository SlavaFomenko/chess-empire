import React from "react";
import { Route, Routes } from "react-router";
import { GamePage } from "./game";
import { LoginPage } from "./sign-in/login";
import { RegistrationPage } from "./sign-in/registration";
import { HomePage } from "./home";
import { NotFoundPage } from "./page-404";

export class Routing extends React.Component {

  shouldComponentUpdate () {
    return false;
  }

  render () {
    return (
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/game" element={<GamePage />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegistrationPage />} />
        <Route path="*" element={<NotFoundPage />} />
      </Routes>
    );
  }
}
