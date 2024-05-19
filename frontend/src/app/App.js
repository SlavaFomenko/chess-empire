import styles from "./styles/index.module.scss";
import { withProviders } from "./providers";
import { Routing } from "../pages";
import { useDispatch, useSelector } from "react-redux";
import { NotificationLayout } from "../layouts/notification-layout";
import { restoreToken } from "./model/restoreToken";
import { useEffect } from "react";
import { s } from "../shared/socket";

function App () {
  const dispatch = useDispatch();
  const { isVisible, content } = useSelector(store => store.notification);

  const restoreAuthentication = () => {
    const token = localStorage.getItem("token");
    if (token) {
      dispatch(restoreToken(token));
      dispatch(s.connect())
    }
  }

  useEffect(restoreAuthentication, []);

  return (
    <div className={styles.App}>
      {isVisible && (
        <NotificationLayout>
          {content}
        </NotificationLayout>
      )}
      <Routing />
    </div>
  );
}

export default withProviders(App);
