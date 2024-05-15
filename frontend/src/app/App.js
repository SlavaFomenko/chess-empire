import styles from "./styles/index.module.scss";
import { withProviders } from "./providers";
import { Routing } from "../pages";
import { useSelector } from "react-redux";
import { NotificationLayout } from "../layouts/notification-layout";

function App () {

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
