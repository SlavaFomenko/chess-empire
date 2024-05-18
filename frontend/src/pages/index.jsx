import React from "react";
import { Routes, Route } from "react-router";
import { GamePage }  from "./game";
import { AuthorizationPage } from "./sign-in/authorization";
import { RegestrationPage } from "./sign-in/registration";
import { HomePage } from "./home";
import { NotFoundPage } from "./page-404";

export class Routing extends React.Component {

  shouldComponentUpdate () {
    return false;
  }

  render() {
    return (
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/game" element={<GamePage />} />
        <Route path="/authorization" element={<AuthorizationPage />} />
        <Route path="/regestration" element={<RegestrationPage />} />
        <Route path="*" element={<NotFoundPage />} />
      </Routes>
    );
  }
}