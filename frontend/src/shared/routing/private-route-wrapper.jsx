import { Outlet, useNavigate } from "react-router-dom";
import { useSelector } from "react-redux";
import { jwtDecode } from "jwt-decode";
import { useEffect, useMemo } from "react";

export const PrivateRouteWrapper = () => {


  const token = localStorage.getItem("token");
  const navigate = useNavigate();

  const user = useMemo(() => {
    if (!token) {
      return null;
    }

    try {
      return jwtDecode(token);
    } catch (error) {
      console.error('Invalid token', error);
      return null;
    }
  }, [token]);

  useEffect(() => {
    if (!user || user.role !== "ROLE_ADMIN") {
      navigate("/");
    }
  }, [navigate, user]);

  if (!user || user.role !== "ROLE_ADMIN") {
    return null;
  }

  return <Outlet />;
};
