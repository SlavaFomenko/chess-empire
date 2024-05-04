import styles from "./styles/index.module.scss";
import { withProviders } from "./providers";
import { Routing } from "../pages";
import { Provider } from "react-redux";
import { store } from "./providers/with-store";

function App () {
  return (
    <Provider store={store}>
      <div className={styles.App}>
        <Routing />
      </div>
    </Provider>);
}

export default withProviders(App);
