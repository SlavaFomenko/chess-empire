import styles from "./styles/index.module.scss";
import { withProviders } from "./providers";
import { Routing } from "../pages";
import { useDispatch, useSelector } from "react-redux";
import { NotificationLayout } from "../layouts/notification-layout";
import { restoreToken } from "./model/restoreToken";

function App () {
  const dispatch = useDispatch();
  const token = localStorage.getItem("token");
  if (token) {
    dispatch(restoreToken(token));
  }

  const { isVisible, content } = useSelector(store => store.notification);

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
