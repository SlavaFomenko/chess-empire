import compose from "compose-function";
import { withRouter } from "./with-router";
import { store } from "./with-store";

export const withProviders = compose(store,withRouter);