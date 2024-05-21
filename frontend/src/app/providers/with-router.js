import React from 'react';
import { Router } from "react-router-dom";
import { history } from "../../shared/routing"

export const CustomRouter = ({
  basename,
  children,
  history,
}) => {
  const [state, setState] = React.useState({
    action: history.action,
    location: history.location,
  });

  React.useLayoutEffect(() => history.listen(setState), [history]);

  return (
    <Router
      basename={basename}
      children={children}
      location={state.location}
      navigationType={state.action}
      navigator={history}
    />
  );
};

export const withRouter = ( Component ) => () => (
  <CustomRouter history={history}>
    <Component />
  </CustomRouter>
);
