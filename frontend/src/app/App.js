import styles from "./styles/index.module.scss";
import { withProviders } from "./providers";
import { Routing } from "../pages";

function App () {
  return (
    <div className={styles.App}>
        <Routing />
    </div>);
}

export default withProviders(App);
