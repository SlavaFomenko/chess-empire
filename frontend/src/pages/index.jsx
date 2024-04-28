import { Routes, Route } from "react-router";
import HomePage from "./home-page";
import GamePage from "./game-page";
import NotFoundPage from "./not-found-page/not-found-page";

export const Routing = () => {
  return (
    <Routes>
      <Route path="/" element={<HomePage />} />
      <Route path="/game" element={<GamePage />} />
      <Route path="*" element={<NotFoundPage />} />
    </Routes>
  );
};